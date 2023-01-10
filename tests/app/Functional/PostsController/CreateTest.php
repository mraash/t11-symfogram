<?php

declare(strict_types=1);

namespace Tests\App\Functional\PostsController;

use App\Domain\Entity\Post;
use App\Domain\Entity\User;
use App\Domain\Repository\PostRepository;
use App\Domain\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreateTest extends WebTestCase
{
    private PostRepository $postRepository;
    private KernelBrowser $client;
    private User $loggedUser;

    protected function setUp(): void
    {
        $this->client = self::createClient();

        /** @var UserRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);

        /** @var PostRepository */
        $this->postRepository = self::getContainer()->get(PostRepository::class);

        $this->loggedUser = new User();

        $this->loggedUser->setEmail('test1@test.com');
        $this->loggedUser->setPassword('123');
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

    public function test_create_page(): void
    {
        $this->client->request('GET', '/posts/create');

        $this->assertResponseIsSuccessful();
    }

    public function test_valid_create(): void
    {
        $this->client->request('POST', '/posts/create', [
            'title' => 'My title',
        ]);

        $post = $this->postRepository->findOneByOrNull(['title' => 'My title']);

        $this->assertResponseRedirects('/posts/create');
        $this->assertInstanceOf(Post::class, $post);
    }

    public function test_create_with_empty_fields(): void
    {
        $this->client->request('POST', '/posts/create');

        $posts = $this->postRepository->findAll();

        $this->assertResponseRedirects('/posts/create');
        $this->assertCount(0, $posts);
    }
}
