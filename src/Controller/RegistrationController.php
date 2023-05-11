<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Classe\SearchMembre;
use App\Entity\Partner;
use App\Entity\User;
use App\Form\CalendarType;
use App\Form\RegistrationFormType;
use App\Form\ResendVerifyEmailForm;
use App\Form\SearchMembreType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\OrderBy;
use Knp\Component\Pager\PaginatorInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use function mysql_xdevapi\getSession;

class RegistrationController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, VerifyEmailHelperInterface $verifyEmailHelper, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
		$entityManager = $this->getDoctrine()->getManager();

		if ($form->isSubmitted() && $form->isValid()) {
			try {

				$user->setPassword($passwordHasher->hashPassword($user, $form->get('plainPassword')->getData()));

				$entityManager->persist($user);
				$entityManager->flush();

				$signatureComponent = $verifyEmailHelper->generateSignature(
					'app_verify_email',
					$user->getId(),
					$user->getEmail(),
					['id' => $user->getId()]
				);
				$expirationDate = new \DateTime('@'.$signatureComponent->getExpiresAt()->getTimestamp());
				$expirationDate->setTimezone(new \DateTimeZone('Europe/Paris'));

				$email = (new TemplatedEmail())
					->from('sqvald@example.com')
					->to($user->getEmail())
					->subject('[SQVALD] Confirmez votre adresse email')
					->htmlTemplate('email/verify_email.html.twig')
					->context([
						'link' => $signatureComponent->getSignedUrl(),
						'expiration_date' => $expirationDate
					])
				;
				$mailer->send($email);

				$this->addFlash('success', 'Inscription confirmée, un email à été envoyé à votre adresse email pour la vérifier');


			} catch (TransportExceptionInterface $exception) {
				$entityManager->remove($user);
				$entityManager->flush();

				$this->addFlash('error', 'Le service de messagerie est indisponible pour le moment, veuillez réessayer ultérieurement');

			}

			return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository, MailerInterface $mailer): Response
    {
		$user = $userRepository->find($request->query->get('id'));
		if (!$user) {
			throw $this->createNotFoundException();
		}

		try {
			$verifyEmailHelper->validateEmailConfirmation(
				$request->getUri(),
				$user->getId(),
				$user->getEmail()
			);
		} catch (VerifyEmailExceptionInterface $e) {
			$this->addFlash('error', $e->getReason());
			return $this->redirectToRoute('app_register');
		}

		$user->setIsVerified(true);
		$this->entityManager->flush();

		$adminUsers = $userRepository->findAdmins();

		$email = (new TemplatedEmail())
			->from('sqvald@example.com')
			->subject('[SQVALD] Nouvel utilisateur en attente de validation')
			->htmlTemplate('email/new_user.html.twig')
			->context([
				'user' => $user
			])
		;
		$this->addFlash('success', 'Adresse email vérifiée, un administrateur va rapidement valider votre inscription');

		try {
			foreach ($adminUsers as $adminUser) $mailer->send($email->to($adminUser->getEmail()));
		} catch (TransportExceptionInterface $exception) {
			$this->addFlash('warning', 'Le service de messagerie est indisponible pour le moment, veuillez contacter un administrateur en cas de durée de validation trop longue');
		}

		return $this->redirectToRoute('home');
    }

	/**
	 * @Route("/verify/resend", name="app_verify_resend_email")
	 */
	public function ProcessResendVerifyEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, MailerInterface $mailer):Response {
		$form = $this->createForm(ResendVerifyEmailForm::class);
		$form->handleRequest($request);

		if ($request->isMethod(Request::METHOD_POST) && $form->isSubmitted() && $form->isValid()) {
			$userId = $request->getSession()->get('userId');
			if ($userId == null) {
				throw new NotFoundHttpException('Aucun utilisateur séléctionné');
			}
			$user = $this->entityManager->getRepository(User::class)->find($userId);
			$signatureComponent = $verifyEmailHelper->generateSignature(
				'app_verify_email',
				$user->getId(),
				$user->getEmail(),
				['id' => $user->getId()]
			);

			$expirationDate = new \DateTime('@'.$signatureComponent->getExpiresAt()->getTimestamp());
			$expirationDate->setTimezone(new \DateTimeZone('Europe/Paris'));

			$email = (new TemplatedEmail())
				->from('sqvald@example.com')
				->to($user->getEmail())
				->subject('[SQVALD] Confirmez votre adresse email')
				->htmlTemplate('email/verify_email.html.twig')
				->context([
					'link' => $signatureComponent->getSignedUrl(),
					'expiration_date' => $expirationDate
				])
			;
			try {
				$mailer->send($email);
				$this->addFlash('success', 'Inscription confirmée, un email à été envoyé à votre adresse email pour la vérifier');
			} catch (TransportExceptionInterface $exception) {
				$this->addFlash('error', 'Le service de messagerie est indisponible pour le moment, veuillez réessayer ultérieurement');
			}


			return $this->redirectToRoute('home');
		}
		return $this->render('registration/resend_verify_email.html.twig', ['form' => $form->createView()]);
}


    // Recuperation de la liste des membres

    /**
     * @Route("/membre", name="show_membre")
     */
    public function showMembre(Request $request, PaginatorInterface $paginator):Response
    {
//		$this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(SearchMembreType::class, null, ['action' => $this->generateUrl('show_membre') ,'method' => 'GET']);

		$partner = $request->query->get('partner');

		$form->handleRequest($request);

		if ($partner != null) {
			$users = $this->entityManager->getRepository(User::class)->findByPartner($partner);
		} else {
			$users = $this->entityManager->getRepository(User::class)->findBy(['isValide' => true]);
		}

		$users = $paginator->paginate(
            $users, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

        return $this->render('show/indexMembre.html.twig', [
            'users' => $users,
            'form'=>$form->createView()

        ]);
    }
}
