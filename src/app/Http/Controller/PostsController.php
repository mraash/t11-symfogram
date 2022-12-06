<?php

declare(strict_types=1);

namespace App\Http\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    #[Route('posts/create', methods: ['HEAD', 'GET'], name: 'pages.posts.create')]
    public function showCreationForm(): Response
    {
        return $this->render('pages/posts/create.twig');
    }

    #[Route('posts/create', methods: ['POST'], name: 'actions.posts.create')]
    public function create(): Response
    {
        $this->addErrorFlash('Method is empty');

        return $this->redirectBack();
    }
}
