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

    public function test_single_page(): void
    {
        $visitedUser = $this->userService->create('test2@test.com', '123', 'Bb', 'Bbb', true, true);

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
