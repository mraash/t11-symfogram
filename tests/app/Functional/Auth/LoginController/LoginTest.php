<?php

declare(strict_types=1);

namespace Tests\App\Functional\Auth\LoginController;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginTest extends WebTestCase
{
    private KernelBrowser $client;
    private User $user;
    private string $userEmail = 'test1@test.com';
    private string $userPlainPassword = '123';

    protected function setUp(): void
    {
        $this->client = self::createClient();

        /** @var UserRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);
        /** @var UserPasswordHasherInterface */
        $passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);

        $this->user = new User();
        $hashedPassword = $passwordHasher->hashPassword($this->user, $this->userPlainPassword);

        $this->user->setEmail($this->userEmail);
        $this->user->setPassword($hashedPassword);
        $this->user->addVerifiedRole();
        $this->user->addBasedRole();

        $userRepository->save($this->user);
        $userRepository->flush();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->client);
        unset($this->user);
    }

    public function test_login_page(): void
    {
        $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
    }

    public function test_valid_login(): void
    {
        $this->client->request('POST', '/login', [
            'email' => $this->userEmail,
            'password' => $this->userPlainPassword,
        ]);

        $this->assertResponseRedirects('/');
    }

    public function test_login_with_invalid_credentials(): void
    {
        $this->client->request('POST', '/login', [
            'email' => $this->userEmail . 'abc',
            'password' => $this->userPlainPassword . 'abc',
        ]);

        $this->assertResponseRedirects('/login');
    }

    public function test_login_with_invalid_password(): void
    {
        $this->client->request('POST', '/login', [
            'email' => $this->userEmail,
            'password' => $this->userPlainPassword . 'abc',
        ]);

        $this->assertResponseRedirects('/login');
    }
}
