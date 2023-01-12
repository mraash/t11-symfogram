<?php

declare(strict_types=1);

namespace Tests\App\Functional\Auth\RegistrationController;

use App\Domain\Type\UserRoles;
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

        $user = new User();
            
        $user->setEmail('test1@test.com');
        $user->setPassword('123');

        $repository->save($user);
        $repository->flush();

        $tokenString = 'abcde';

        $token = new EmailVerificationToken();

        $token->setOwner($user);
        $token->setToken($tokenString);

        $tokenRepository->save($token);
        $tokenRepository->flush();

        $client->request('GET', '/register/verify-email', ['token' => $tokenString]);

        $this->assertResponseRedirects('/register/create-profile');
        $this->assertContains(UserRoles::Verified->value, $user->getRoles());
    }

    public function test_verification_with_no_token_in_db(): void
    {
        $client = self::createClient();

        $client->request('GET', '/register/verify-email', ['token' => 'abc']);

        $this->assertResponseRedirects('/register');
    }
}
