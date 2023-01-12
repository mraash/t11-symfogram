<?php

declare(strict_types=1);

namespace Tests\App\Functional\Auth\RegistrationController;

use App\Domain\Type\UserRoles;
use App\Domain\Entity\User;
use App\Domain\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreateProfileTest extends WebTestCase
{
    private UserRepository $userRepository;
    private KernelBrowser $client;
    private User $loggedUser;

    protected function setUp(): void
    {
        $this->client = self::createClient();

        /** @var UserRepository */
        $this->userRepository = self::getContainer()->get(UserRepository::class);

        $this->loggedUser = new User();

        $this->loggedUser->setEmail('test1@test.com');
        $this->loggedUser->setPassword('123');
        $this->loggedUser->addVerifiedRole();

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

    public function test_profile_creation_page(): void
    {
        $this->client->request('GET', 'register/create-profile');

        $this->assertResponseIsSuccessful();
    }

    public function test_valid_profile_creation(): void
    {
        $this->client->request('POST', 'register/create-profile', [
            'first_name' => 'Johan',
            'last_name' => 'James',
            'bio' => 'About me.',
        ]);

        $this->assertResponseRedirects('/');
        $this->assertSame('Johan', $this->loggedUser->getFirstName());
        $this->assertSame('James', $this->loggedUser->getLastName());
        $this->assertSame('About me.', $this->loggedUser->getBioOrNull());
        $this->assertContains(UserRoles::Based->value, $this->loggedUser->getRoles());
    }

    public function test_profile_creation_with_empty_required_parameters(): void
    {
        $this->client->request('POST', 'register/create-profile', [
            'first_name' => 'Johan',
        ]);

        $this->assertResponseRedirects('/register/create-profile');
        $this->assertNotContains(UserRoles::Based->value, $this->loggedUser->getRoles());
    }
}
