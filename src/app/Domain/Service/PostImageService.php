<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Post;
use App\Domain\Entity\PostImage;
use App\Domain\Repository\PostImageRepository;
use SymfonyExtension\Domain\Service\AbstractService;

/**
 * @extends AbstractService<PostImage>
 *
 * @method void save(PostImage $image)
 * @method void remove(PostImage $image)
 *
 * @method PostImage|null findByIdOrNull(int $id)
 * @method PostImage      findByIdOr(int $id)
 * @method PostImage|null findOneByOrNull(array $criteria)
 * @method PostImage      findOneBy(array $criteria)
 * @method PostImage[]    findAll()
 * @method PostImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostImageService extends AbstractService
{
    public function __construct(
        PostImageRepository $repository
    ) {
        parent::__construct($repository);
    }

    public function create(Post $post, string $uri): PostImage
    {
        $image = $this->getRepository()->create($post, $uri);
        $this->getRepository()->flush();

        return $image;
    }

    protected function getRepository(): PostImageRepository
    {
        /** @var PostImageRepository */
        return parent::getRepository();
    }
}
