<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Post;
use App\Domain\Entity\User;
use App\Domain\Repository\PostImageRepository;
use App\Domain\Repository\PostRepository;
use SymfonyExtension\Domain\Exception\LogicException;
use SymfonyExtension\Domain\Service\AbstractService;

/**
 * @extends AbstractService<Post>
 *
 * @method void save(Post $post)
 * @method void remove(Post $post)
 * @method void saveList(Post[] $post)
 * @method void removeList(Post[] $post)
 *
 * @method Post|null findByIdOrNull(int $id)
 * @method Post      findByIdOr(int $id)
 * @method Post|null findOneByOrNull(array $criteria)
 * @method Post      findOneBy(array $criteria)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostService extends AbstractService
{
    public function __construct(
        PostRepository $repository,
        private PostImageService $postImageService
    ) {
        parent::__construct($repository);
    }

    /**
     * @param string[] $imageUriList
     */
    public function create(User $user, string $title = null, array $imageUriList = []): Post
    {
        if ($title === null && empty($imageUriList)) {
            throw new LogicException();
        }

        $post = new Post();

        $post->setOwner($user);
        $post->setTitle($title);

        $this->save($post);

        $this->postImageService->createListForPost($post, $imageUriList);

        return $post;
    }

    protected function getRepository(): PostRepository
    {
        /** @var PostRepository */
        return parent::getRepository();
    }
}
