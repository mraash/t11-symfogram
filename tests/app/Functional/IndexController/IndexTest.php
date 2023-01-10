<?php

declare(strict_types=1);

namespace Tests\App\Functional\IndexController;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexTest extends WebTestCase
{
    private KernelBrowser $client;
    private User $loggedUser;

    protected function setUp(): void
    {
        $this->client = self::createClient();

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
        $this->client->request('GET', "/");

        $this->assertResponseRedirects('/users');
    }
}
