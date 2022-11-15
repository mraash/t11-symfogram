<?php

declare(strict_types=1);

namespace App\Http\SupportService\EmailVerifier;

use App\Domain\Entity\User;
use App\Domain\Exception\EntityNotFoundException;
use App\Domain\Repository\EmailVerificationTokenRepository;
use App\Domain\Repository\UserRepository;
use App\Http\SupportService\EmailVerifier\Exceptions\EmailIsAlreadyVerifiedException;
use App\Http\SupportService\EmailVerifier\Exceptions\TokenNotFoundException;
use App\Http\SupportService\EmailVerifier\Exceptions\TokenNotProvidedException;
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

    /**
     * @throws TokenNotProvidedException
     * @throws TokenNotFoundException
     * @throws EmailIsAlreadyVerifiedException
     */
    public function verifyEmailByRequest(Request $request): User
    {
        $tokenStr = $request->query->get('token', null);

        if ($tokenStr === null) {
            throw new TokenNotProvidedException();
        }

        try {
            $token = $this->emailVerificationTokenRepository->findOneByToken($tokenStr);
        }
        catch(EntityNotFoundException) {
            throw new TokenNotFoundException();
        }

        $user = $token->getOwner();

        if ($user->isVerified()) {
            throw new EmailIsAlreadyVerifiedException();
        }

        $user->setIsEmailVerified(true);
        $this->userRepository->flush();

        return $user;
    }
}
