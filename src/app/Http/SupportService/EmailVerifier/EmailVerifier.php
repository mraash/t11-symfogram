<?php

declare(strict_types=1);

namespace App\Http\SupportService\EmailVerifier;

use App\Domain\Entity\User;
use App\Domain\Service\EmailVerificationTokenService;
use App\Domain\Service\UserService;
use App\Http\SupportService\EmailVerifier\Exceptions\TokenNotFoundException;
use App\Http\SupportService\EmailVerifier\Exceptions\TokenNotProvidedException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailVerifier
{
    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator,
        private UserService $userService,
        private EmailVerificationTokenService $emailVerificationTokenService
    ) {
    }

    public function createTokenAndSendEmail(User $user): void
    {
        $email = new TemplatedEmail();

        $email->to($user->getEmail());
        $email->subject('Please verify your email.');
        $email->htmlTemplate('emails/verify-email.twig');

        $token = $this->emailVerificationTokenService->createRandom($user);

        $link = $this->urlGenerator->generate(
            'pactions.register.verify-email',
            ['token' => $token->getToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email->context($email->getContext() + [
            'link' => $link,
        ]);

        $this->mailer->send($email);
    }

    /**
     * @throws TokenNotProvidedException
     * @throws TokenNotFoundException
     */
    public function verifyTokenAndReturnUser(Request $request): User
    {
        /** @var string|null */
        $tokenStr = $request->query->get('token', null);

        if ($tokenStr === null) {
            throw new TokenNotProvidedException();
        }

        $token = $this->emailVerificationTokenService->findOneByTokenOrNull($tokenStr);

        if ($token === null) {
            throw new TokenNotFoundException();
        }

        $user = $token->getOwner();

        if (!$user->hasVerifiedRole()) {
            $user->addVerifiedRole();
            $this->userService->save($user);
        }

        return $user;
    }
}
