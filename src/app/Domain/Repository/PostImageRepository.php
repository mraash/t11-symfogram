<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Post;
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
 * @method PostImage|null findByIdOrNull(int $id)
 * @method PostImage      findById(int $id)
 */
class PostImageRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostImage::class);
    }

    public function create(Post $post, string $uri): PostImage
    {
        $image = new PostImage();

        $image->setPost($post);
        $image->setUri($uri);

        $this->getEntityManager()->persist($image);

        return $image;
    }

    public function save(PostImage $image): void
    {
        $this->getEntityManager()->persist($image);
    }

    public function remove(PostImage $image): void
    {
        $this->getEntityManager()->remove($image);
    }
}
