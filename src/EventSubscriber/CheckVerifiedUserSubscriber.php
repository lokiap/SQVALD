<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Security\AccountNotValidateAuthentificationException;
use App\Security\AccountNotVerifiedAuthentificationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class CheckVerifiedUserSubscriber implements EventSubscriberInterface
{
	private $requestStack;
	private $router;
	private $flashBag;

	public function __construct(RequestStack $requestStack, RouterInterface $router, FlashBagInterface $flashBag)
	{
		$this->requestStack = $requestStack;
		$this->router = $router;
		$this->flashBag = $flashBag;
	}

	public function onCheckPassport(CheckPassportEvent $event)
	{
		$passport = $event->getPassport();
		if (!$passport instanceof UserPassportInterface) {
			throw new \Exception('Unexcpected passport type');
		}

		$user = $passport->getUser();
		if (!$user instanceof User) {
			throw new \Exception('Unexcpected user type');
		}

		if (!$user->isVerified()) {
			$this->requestStack->getSession()->set('userId', $user->getId());
			throw new AccountNotVerifiedAuthentificationException();
		}

		if (!$user->getIsValide()) {
			$this->requestStack->getSession()->set('userId', $user->getId());
			throw new AccountNotValidateAuthentificationException();
		}
	}

	public function onLoginFailure(LoginFailureEvent $event)
	{
		if (!($event->getException() instanceof AccountNotVerifiedAuthentificationException || $event->getException() instanceof AccountNotValidateAuthentificationException)) {
			return;
		}

		if ($event->getException() instanceof AccountNotVerifiedAuthentificationException) {
			$response = new RedirectResponse($this->router->generate('app_verify_resend_email'));
			$event->setResponse($response);
		}
		if ($event->getException() instanceof AccountNotValidateAuthentificationException) {
			$response = new RedirectResponse($this->router->generate('home'));
			$this->flashBag->add('error', 'Votre compte est en attente de validation par un administrateur');
			$event->setResponse($response);
		}
	}

	public static function getSubscribedEvents()
	{
		return [
			CheckPassportEvent::class => ['onCheckPassport', -10],
			LoginFailureEvent::class => 'onLoginFailure'
		];
	}
}