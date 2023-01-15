<?php

declare(strict_types=1);

namespace Tests\App\Functional\UserController;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepository;
use App\Domain\Service\UserService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SingleTest extends WebTestCase
{
    private UserService $userService;
    private KernelBrowser $client;
    private User $loggedUser;

    protected function setUp(): void
    {
        $this->client = self::createClient();

        $this->userService = self::getContainer()->get(UserService::class);

        /** @var UserRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);

        $this->loggedUser = new User();

        $this->loggedUser->setEmail('test1@test.com');
        $this->loggedUser->setPassword('123');
        $this->loggedUser->setFirstName('Aa');
        $this->loggedUser->setLastName('Aaa');
        $this->loggedUser->addVerifiedRole();
        $this->loggedUser->addBasedRole();

        $userRepository->save($this->loggedUser);
        $userRepository->flush();

        $this->client->loginUser($this->loggedUser);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->client);
        unset($this->loggedUser);
    }

    public function test_single_page(): void
    {
        $visitedUser = new User();
        $visitedUser->setEmail('test2@test.com');
        $visitedUser->setPassword('123');
        $visitedUser->setFirstName('Bb');
        $visitedUser->setLastName('Bbb');
        $visitedUser->addVerifiedRole();
        $visitedUser->addBasedRole();

        $this->userService->save($visitedUser);

        $id = $visitedUser->getId();

        $this->client->request('GET', "/users/$id");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Bb Bbb');
        $this->assertSelectorTextNotContains('body', 'Edit profile');
    }

    public function test_single_self_page(): void
    {
        $id = $this->loggedUser->getId();

        $this->client->request('GET', "/users/$id");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Aa Aaa');
        $this->assertSelectorTextContains('body', 'Edit profile');
    }

    public function test_not_existing_user_page(): void
    {
        $this->client->request('GET', '/users/999');

        $this->assertResponseStatusCodeSame(404);
    }
}
