<?php

declare(strict_types=1);

namespace Tests\App\Functional\UserController;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepository;
use App\Domain\Service\UserService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexTest extends WebTestCase
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

    public function test_index_page(): void
    {
        $user1 = new User();
        $user1->setEmail('test2@test.com');
        $user1->setPassword('123');
        $user1->setFirstName('Bb');
        $user1->setLastName('Bbb');
        $user1->addVerifiedRole();
        $user1->addBasedRole();

        $user2 = new User();
        $user2->setEmail('test3@test.com');
        $user2->setPassword('123');
        $user2->setFirstName('Cc');
        $user2->setLastName('Ccc');
        $user2->addVerifiedRole();

        $this->userService->saveList([$user1, $user2]);

        $this->client->request('GET', '/users');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('tbody', 'Aaa');
        $this->assertSelectorTextContains('tbody', 'Bbb');
        $this->assertSelectorTextNotContains('tbody', 'Ccc');
    }
}
