<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Domain\Repository\PostImageRepository;
use App\Domain\Repository\PostRepository;
use App\Http\Input\Post\CreatePostInput;
use App\Http\SupportService\FileUploader\FileUploader;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    public function __construct(
        private PostRepository $postRepository,
        private PostImageRepository $postImageRepository,
        private FileUploader $fileUploader,
        RequestStack $requestStack
    ) {
        parent::__construct($requestStack);
    }

    #[Route('posts/create', methods: ['HEAD', 'GET'], name: 'pages.posts.create')]
    public function showCreationForm(): Response
    {
        return $this->render('pages/posts/create.twig');
    }

    #[Route('posts/create', methods: ['POST'], name: 'actions.posts.create')]
    public function create(CreatePostInput $input): Response
    {
        if (!$this->validateInput($input)) {
            return $this->redirectBack();
        }

        $title = $input->getTitleParam();
        $images = $input->getImageParams();

        $user = $this->getUser();
        $post = $this->postRepository->create($user);

        $post->setTitle($title);

        /** @var string */
        $imagesFolder = $this->getParameter('public.images.posts');

        foreach ($images as $uploadedImage) {
            $uriFilename = $this->fileUploader->createFilename($uploadedImage, $imagesFolder);
            $uri = $uriFilename->getFullUri();

            $this->fileUploader->upload($uploadedImage, $uriFilename);
            $postImage = $this->postImageRepository->create($post, $uri);

            $post->addImage($postImage);
        }

        $this->postImageRepository->flush();

        return $this->redirectBack();
    }
}
