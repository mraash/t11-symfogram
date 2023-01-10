<?php

declare(strict_types=1);

namespace Tests\App\Functional\Auth\RegistrationController;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterTest extends WebTestCase
{
    public function test_register_page(): void
    {
        $client = static::createClient();

        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
    }

    public function test_valid_registration(): void
    {
        $client = static::createClient();

        /** @var UserRepository */
        $repository = self::getContainer()->get(UserRepository::class);

        $client->request('POST', '/register', [
            'email' => 'test1@test.com',
            'password' => '123',
            'password_repeat' => '123',
        ]);

        $user = $repository->findOneByOrNull(['email' => 'test1@test.com']);
        $verificationToken = $user?->getEmailVerificationToken()?->getToken();
        $mail = $this->getMailerMessage();

        $this->assertResponseRedirects();
        $this->assertInstanceOf(User::class, $user);
        $this->assertEmailCount(1);
        $this->assertEmailHtmlBodyContains($mail, "$verificationToken");
    }

    public function test_registration_with_invalid_email(): void
    {
        $client = static::createClient();

        /** @var UserRepository */
        $repository = self::getContainer()->get(UserRepository::class);

        $client->request('POST', '/register', [
            'email' => 'test1',
            'password' => '123',
            'password_repeat' => '123',
        ]);

        $user = $repository->findOneByOrNull(['email' => 'test1@test.com']);

        $this->assertNull($user);
    }

    public function test_registration_with_taken_email(): void
    {
        $client = static::createClient();

        /** @var UserRepository */
        $repository = self::getContainer()->get(UserRepository::class);

        $user = new User();

        $user->setEmail('test1@test.com');
        $user->setPassword('abc');

        $repository->save($user);
        $repository->flush();

        $client->request('POST', '/register', [
            'email' => 'test1@test.com',
            'password' => '123',
            'password_repeat' => '123',
        ]);

        $user = $repository->findOneByOrNull(['email' => 'test1@test.com']);
        $password = $user?->getPassword();

        $this->assertSame('abc', $password);
    }

    public function test_registration_with_invalid_password_repeat(): void
    {
        $client = static::createClient();

        /** @var UserRepository */
        $repository = self::getContainer()->get(UserRepository::class);

        $client->request('POST', '/register', [
            'email' => 'test1@test.com',
            'password' => '123',
            'password_repeat' => '1234',
        ]);

        $user = $repository->findOneByOrNull(['email' => 'test1@test.com']);

        $this->assertNull($user);
    }
}
