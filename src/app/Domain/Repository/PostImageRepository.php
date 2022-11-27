<?php

namespace App\Domain\Repository;

use App\Domain\Entity\PostImage;
use Doctrine\Persistence\ManagerRegistry;
use SymfonyExtension\Domain\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<PostImage>
 *
 * @method PostImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostImage[]    findAll()
 * @method PostImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostImageRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostImage::class);
    }

    public function save(PostImage $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function remove(PostImage $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }
}
