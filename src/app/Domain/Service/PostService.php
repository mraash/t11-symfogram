<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Post;
use App\Domain\Entity\User;
use App\Domain\Repository\PostImageRepository;
use App\Domain\Repository\PostRepository;
use SymfonyExtension\Domain\Service\AbstractService;

/**
 * @extends AbstractService<Post>
 *
 * @method void save(Post $post)
 * @method void remove(Post $post)
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
        private PostImageRepository $postImageRepository,
    ) {
        parent::__construct($repository);
    }

    /**
     * @param array<array<string>> $imageDataList  Array of image data arrays. Image
     *  data schema:
     *  0 - uri
     */
    public function create(User $user, array $imageDataList = [], string $title = null): Post
    {
        $post = $this->getRepository()->create($user);

        foreach ($imageDataList as $imageData) {
            $this->postImageRepository->create($post, $imageData[0]);
        }

        $post->setTitle($title);

        $this->getRepository()->flush();

        return $post;
    }

    protected function getRepository(): PostRepository
    {
        /** @var PostRepository */
        return parent::getRepository();
    }
}
