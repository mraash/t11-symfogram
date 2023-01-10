<?php

declare(strict_types=1);

namespace Tests\App\Functional\Auth\LoginController;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LogoutTest extends WebTestCase
{
    private UserRepository $userRepository;
    private KernelBrowser $client;
    private User $user;

    protected function setUp(): void
    {
        $this->client = self::createClient();

        /** @var UserRepository */
        $this->userRepository = self::getContainer()->get(UserRepository::class);

        $this->user = new User();

        $this->user->setEmail('test1@test.com');
        $this->user->setPassword('123');
        $this->user->addVerifiedRole();
        $this->user->addBasedRole();

        $this->userRepository->save($this->user);
        $this->userRepository->flush();

        $this->client->loginUser($this->user);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->client);
        unset($this->user);
    }

    public function test_logout(): void
    {
        $this->client->request('GET', '/logout');

        $this->assertResponseRedirects('http://localhost/login');
    }
}
