<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Post;
use App\Domain\Entity\PostImage;
use Doctrine\Persistence\ManagerRegistry;
use SymfonyExtension\Domain\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<PostImage>
 *
 * @method void save(PostImage $image)
 * @method void remove(PostImage $image)
 *
 * @method PostImage|null findByIdOrNull(int $id)
 * @method PostImage|null findOneByOrNull(array $criteria)
 * @method PostImage[]    findAll()
 * @method PostImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostImageRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostImage::class);
    }
}
