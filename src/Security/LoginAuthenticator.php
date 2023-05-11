<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

	private $userRepository;
	private $router;
	private $flashBag;

    public function __construct(UserRepository $userRepository, RouterInterface $router, FlashBagInterface $flashBag)
    {
		$this->userRepository = $userRepository;
		$this->router = $router;
		$this->flashBag = $flashBag;
    }

    public function authenticate(Request $request): PassportInterface
    {
        $email = $request->request->get('email');
		$password = $request->request->get('password');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email, function ($userIdentifier) {
				$user = $this->userRepository->findOneBy(['email' => $userIdentifier]);
				if (!$user) {
					throw new UserNotFoundException();
				}
				return $user;
			}),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $request->get('_csrf_token')), (new RememberMeBadge())->enable(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

		return new RedirectResponse($this->router->generate('home'));
    }

	public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
	{
		$this->flashBag->add('error', 'Email ou mot de passe incorrect');
		return new RedirectResponse($this->router->generate('app_login'));
	}

	protected function getLoginUrl(Request $request): string
    {
		return $this->router->generate('app_login');
    }
}
