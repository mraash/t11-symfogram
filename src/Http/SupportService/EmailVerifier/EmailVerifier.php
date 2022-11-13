<?php

declare(strict_types=1);

namespace App\Http\SupportService\EmailVerifier;

use App\Domain\Entity\User;
use App\Domain\Repository\EmailVerificationTokenRepository;
use App\Domain\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailVerifier
{
    private MailerInterface $mailer;
    private UrlGeneratorInterface $urlGenerator;
    private UserRepository $userRepository;
    private EmailVerificationTokenRepository $emailVerificationTokenRepository;

    public function __construct(
        MailerInterface $mailer,
        UrlGeneratorInterface $urlGenerator,
        UserRepository $userRepository,
        EmailVerificationTokenRepository $emailVerificationTokenRepository
    ) {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->userRepository = $userRepository;
        $this->emailVerificationTokenRepository = $emailVerificationTokenRepository;
    }

    public function createTokenAndSendEmail(User $user, RawMessage $email): void
    {
        $token = $this->emailVerificationTokenRepository->create($user);
        $this->emailVerificationTokenRepository->flush();

        $link = $this->urlGenerator->generate(
            'actions.register.verify-email',
            ['token' => $token->getToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email->context($email->getContext() + [
            'link' => $link,
        ]);

        $this->mailer->send($email);
    }

    public function verifyToken(Request $request): void
    {
    }
}
