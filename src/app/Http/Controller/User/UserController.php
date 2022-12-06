<?php

declare(strict_types=1);

namespace App\Http\Controller\User;

use App\Domain\Entity\User;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/users', methods: ['HEAD', 'GET'], name: 'pages.users.index')]
    public function showIndex(): Response
    {
        return $this->render('pages/users/index.twig');
    }

    #[Route('/users/{id<\d+>}', methods: ['HEAD', 'GET'], name: 'pages.users.single')]
    public function showSingle(int $id): Response
    {
        $user = $this->getUser();

        $isSelf = $id === $user->getId();

        return $this->render('pages/users/single.twig', [
            'is_self' => $isSelf,
        ]);
    }
}
