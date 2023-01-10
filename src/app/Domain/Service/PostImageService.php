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
 * @method void saveList(PostImage[] $image)
 * @method void removeList(PostImage[] $image)
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
        $image = $this->createEntity($post, $uri);

        $this->save($image);

        return $image;
    }

    /**
     * @param string[] $uriList  Array of uri string
     *
     * @return PostImage[]
     */
    public function createListForPost(Post $post, array $uriList): array
    {
        $imageList = [];

        foreach ($uriList as $uri) {
            $imageList[] = $this->createEntity($post, $uri);
        }

        $this->saveList($imageList);

        return $imageList;
    }

    private function createEntity(Post $post, string $uri): PostImage
    {
        $image = new PostImage();

        $image->setPost($post);
        $image->setUri($uri);

        return $image;
    }

    protected function getRepository(): PostImageRepository
    {
        /** @var PostImageRepository */
        return parent::getRepository();
    }
}
