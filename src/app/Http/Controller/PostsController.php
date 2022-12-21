<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Domain\Service\PostService;
use App\Http\Input\Post\CreatePostInput;
use App\Http\SupportService\FileUploader\FileUploader;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    public function __construct(
        private PostService $postService,
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

        /** @var string */
        $imagesFolder = $this->getParameter('public.images.posts');

        $uriList = $this->fileUploader->uploadListAndReturnFilenames($images, $imagesFolder);
        /** @var array<string[]> */
        $imageDataList = [];

        foreach ($uriList as $uri) {
            $imageDataList[] = [$uri->getFullUri()];
        }

        $user = $this->getUser();

        $this->postService->create($user, $imageDataList, $title);

        return $this->redirectBack();
    }
}
