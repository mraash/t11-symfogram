<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Post;
use App\Domain\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use SymfonyExtension\Domain\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Post|null findByIdOrNull(int $id)
 */
class PostRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function create(User $user): Post
    {
        $post = new Post();

        $post->setOwner($user);

        $this->save($post);

        return $post;
    }

    public function save(Post $post): void
    {
        $this->getEntityManager()->persist($post);
    }

    public function remove(Post $post): void
    {
        $this->getEntityManager()->remove($post);
    }
}
