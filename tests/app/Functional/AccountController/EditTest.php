<?php

declare(strict_types=1);

namespace Tests\App\Functional\AccountController;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EditTest extends WebTestCase
{
    private UserRepository $userRepository;
    private KernelBrowser $client;
    private User $loggedUser;

    private string $loggedUserFirstName = 'Johan';
    private string $loggedUserLastName = 'Smith';

    protected function setUp(): void
    {
        $this->client = self::createClient();

        /** @var UserRepository */
        $this->userRepository = self::getContainer()->get(UserRepository::class);

        $this->loggedUser = new User();

        $this->loggedUser->setEmail('test1@test.com');
        $this->loggedUser->setPassword('123');
        $this->loggedUser->addVerifiedRole();
        $this->loggedUser->addBasedRole();
        $this->loggedUser->setFirstName($this->loggedUserFirstName);
        $this->loggedUser->setLastName($this->loggedUserLastName);

        $this->userRepository->save($this->loggedUser);
        $this->userRepository->flush();

        $this->client->loginUser($this->loggedUser);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->client);
        unset($this->loggedUser);
    }

    public function test_edit_page(): void
    {
        $this->client->request('GET', '/account/edit');

        $this->assertResponseIsSuccessful();
    }

    public function test_valid_edit(): void
    {
        $this->client->request('POST', '/account/edit', [
            'first_name' => 'Sam',
            'last_name' => 'White',
            'bio' => 'Hello'
        ]);

        $this->assertResponseRedirects('/account/edit');
        $this->assertSame('Sam', $this->loggedUser->getFirstName());
        $this->assertSame('White', $this->loggedUser->getLastName());
        $this->assertSame('Hello', $this->loggedUser->getBioOrNull());
    }

    public function test_edit_with_empty_required_fields(): void
    {
        $this->client->request('POST', '/account/edit', [
            'first_name' => 'Sam',
        ]);

        $this->assertResponseRedirects('/account/edit');
        $this->assertSame($this->loggedUserFirstName, $this->loggedUser->getFirstName());
    }
}
