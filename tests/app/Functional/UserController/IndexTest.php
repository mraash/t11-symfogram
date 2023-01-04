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

        $this->loggedUser = (new User())
            ->setEmail('test1@test.com')
            ->setPassword('123')
            ->setFirstName('Aa')
            ->setLastName('Aaa')
            ->addVerifiedRole()
            ->addBasedRole()
        ;

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
        $this->userService->create('test2@test.com', '123', 'Bb', 'Bbb', true, true);
        $this->userService->create('test3@test.com', '123', 'Cc', 'Ccc', true, true);
        $this->userService->create('test4@test.com', '123', 'Dd', 'Ddd', true, false);
        $this->userService->create('test5@test.com', '123', 'Ee', 'Eee', false, false);

        $this->client->request('GET', '/users');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('tbody', 'Aaa');
        $this->assertSelectorTextContains('tbody', 'Bbb');
        $this->assertSelectorTextContains('tbody', 'Ccc');
        $this->assertSelectorTextNotContains('tbody', 'Ddd');
        $this->assertSelectorTextNotContains('tbody', 'Eee');
    }
}
