<?php

declare(strict_types=1);

namespace Tests\App\Functional\Auth\RegistrationController;

use App\Domain\Entity\PostImage;
use App\Domain\Entity\User;
use App\Domain\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CreateProfileTest extends WebTestCase
{
    private UserRepository $userRepository;
    private KernelBrowser $client;
    private User $loggedUser;

    public function setUp(): void
    {
        unset($this->client);
        unset($this->loggedUser);

        $this->client = self::createClient();

        /** @var UserRepository */
        $this->userRepository = self::getContainer()->get(UserRepository::class);

        $this->loggedUser = (new User())
            ->setEmail('test1@test.com')
            ->setPassword('123')
            ->addVerifiedRole()
        ;

        $this->userRepository->save($this->loggedUser);
        $this->userRepository->flush();

        $this->client->loginUser($this->loggedUser);
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
        $this->assertSame('About me.', $this->loggedUser->getBio());
        $this->assertContains('ROLE_BASED', $this->loggedUser->getRoles());
    }

    public function test_profile_creation_with_empty_required_parameters(): void
    {
        $this->client->request('POST', 'register/create-profile', [
            'first_name' => 'Johan',
        ]);

        $this->assertResponseRedirects('/register/create-profile');
        $this->assertNotContains('ROLE_BASED', $this->loggedUser->getRoles());
    }
}
