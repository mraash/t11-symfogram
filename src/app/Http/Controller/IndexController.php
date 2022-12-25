<?php

declare(strict_types=1);

namespace App\Http\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', methods: ['GET', 'HEAD'], name: 'pages.index')]
    public function showIndex(): RedirectResponse
    {
        return $this->redirectToRoute('pages.users.index', [], 301);
    }
}
