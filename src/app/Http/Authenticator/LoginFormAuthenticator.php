<?php

declare(strict_types=1);

namespace App\Http\Authenticator;

use App\Domain\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\InteractiveAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class LoginFormAuthenticator extends AbstractAuthenticator implements
    AuthenticationEntryPointInterface,
    InteractiveAuthenticatorInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function supports(Request $request): bool
    {
        $isLoginMethod = $request->isMethod('POST');
        $isLoginPath = $this->getLoginActionUri() === $request->getPathInfo();

        return $isLoginMethod && $isLoginPath;
    }

    public function authenticate(Request $request): Passport
    {
        /** @var string */
        $email = $request->get('email', '');
        /** @var string */
        $password = $request->get('password', '');

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        $uri = $this->getLoginPageUri();
        return new RedirectResponse($uri);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): Response
    {
        /** @var User */
        $user = $token->getUser();

        if (!$user->hasVerifiedRole()) {
            $uri = $this->urlGenerator->generate('pactions.logout');
            return new RedirectResponse($uri);
        }

        if (!$user->hasBasedRole()) {
            $uri = $this->urlGenerator->generate('pages.register.create-profile');
            return new RedirectResponse($uri);
        }

        $uri = $this->urlGenerator->generate('pages.index');
        return new RedirectResponse($uri);
    }

    /**
     * Override to control what happens when the user hits a secure page
     * but isn't logged in yet.
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $url = $this->getLoginPageUri();
        return new RedirectResponse($url);
    }

    public function isInteractive(): bool
    {
        return true;
    }

    public function getLoginPageUri(): string
    {
        return $this->urlGenerator->generate('pages.login');
    }

    private function getLoginActionUri(): string
    {
        return $this->urlGenerator->generate('actions.login');
    }
}
