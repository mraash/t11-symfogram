<?php

declare(strict_types=1);

namespace Tests\App\Functional\Auth\RegistrationController;

use App\Domain\Entity\EmailVerificationToken;
use App\Domain\Entity\User;
use App\Domain\Repository\EmailVerificationTokenRepository;
use App\Domain\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VerifyEmailTest extends WebTestCase
{
    public function test_valid_verification(): void
    {
        $client = self::createClient();

        /** @var UserRepository */
        $repository = self::getContainer()->get(UserRepository::class);

        /** @var EmailVerificationTokenRepository */
        $tokenRepository = self::getContainer()->get(EmailVerificationTokenRepository::class);

        $user = (new User())
            ->setEmail('test1@test.com')
            ->setPassword('123')
        ;

        $repository->save($user);
        $repository->flush();

        $tokenString = 'abcde';
        $token = (new EmailVerificationToken())
            ->setOwner($user)
            ->setToken($tokenString)
        ;

        $tokenRepository->save($token);
        $tokenRepository->flush();

        $client->request('GET', '/register/verify-email', ['token' => $tokenString]);

        $this->assertResponseRedirects('/register/create-profile');
        $this->assertContains('ROLE_VERIFIED', $user->getRoles());
    }

    public function test_verification_with_no_token_in_db(): void
    {
        $client = self::createClient();

        $client->request('GET', '/register/verify-email', ['token' => 'abc']);

        $this->assertResponseRedirects('/register');
    }
}