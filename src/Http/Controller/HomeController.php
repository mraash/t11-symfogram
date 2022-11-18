<?php

declare(strict_types=1);

namespace App\Http\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', methods: ['HEAD', 'GET'])]
    public function showIndex(): RedirectResponse
    {
        return $this->redirectToRoute('pages.home', [], 301);
    }

    #[Route('/home', methods: ['HEAD', 'GET'], name: 'pages.home')]
    public function showHome(): Response
    {
        return $this->render('pages/home.twig');
    }
}
