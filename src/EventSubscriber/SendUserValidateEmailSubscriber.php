<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;

class SendUserValidateEmailSubscriber implements EventSubscriberInterface
{
	private $mailer;
	private $entityManager;

	public function __construct(MailerInterface $mailer, EntityManagerInterface $entityManager)
	{
		$this->mailer = $mailer;
		$this->entityManager = $entityManager;
	}

	public static function getSubscribedEvents(): array
	{
		return [
			BeforeEntityUpdatedEvent::class => 'onpreUpdate'
		];
	}

	public function onPreUpdate(BeforeEntityUpdatedEvent $event)
	{
		$user = $event->getEntityInstance();
		if (!$user instanceof User) return;
		$uow = $this->entityManager->getUnitOfWork();
		$oldIsValide = $uow->getOriginalEntityData($user)['isValide'];
		$newIsValide = $user->getIsValide();

		if (!$oldIsValide && $newIsValide) {
			$email = (new TemplatedEmail())
				->from('sqvald@example.com')
				->to($user->getEmail())
				->subject('[SQVALD] Votre inscription a été validée')
				->htmlTemplate('email/registration_complete.html.twig');
			try {
				$this->mailer->send($email);
			} catch (TransportExceptionInterface $ignored) {}
		}
	}
}