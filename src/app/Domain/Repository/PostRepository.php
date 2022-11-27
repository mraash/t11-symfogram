<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Post;
use Doctrine\Persistence\ManagerRegistry;
use SymfonyExtension\Domain\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function save(Post $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function remove(Post $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }
}
