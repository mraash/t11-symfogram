<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Post;
use App\Domain\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use SymfonyExtension\Domain\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<Post>
 *
 * @method void save(Post $post)
 * @method void remove(Post $entity)
 *
 * @method Post|null findByIdOrNull(int $id)
 * @method Post|null findOneByOrNull(array $criteria)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }
}
