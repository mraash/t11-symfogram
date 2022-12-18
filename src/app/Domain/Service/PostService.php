<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Post;
use App\Domain\Entity\User;
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
        PostRepository $repository
    ) {
        parent::__construct($repository);
    }

    public function create(User $user): Post
    {
        $post = $this->getRepository()->create($user);
        $this->getRepository()->flush();

        return $post;
    }

    protected function getRepository(): PostRepository
    {
        /** @var PostRepository */
        return parent::getRepository();
    }
}
