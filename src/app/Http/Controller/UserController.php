<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Domain\Service\UserService;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private UserService $userService,
        RequestStack $requestStack
    ) {
        parent::__construct($requestStack);
    }

    #[Route('/users', methods: ['GET', 'HEAD'], name: 'pages.users.index')]
    public function showIndex(): Response
    {
        $users = $this->userService->findAllBased();

        return $this->render('pages/users/index.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/{id<\d+>}', methods: ['GET', 'HEAD'], name: 'pages.users.single')]
    public function showSingle(int $id): Response
    {
        $user = $this->userService->findByIdOrNull($id);

        if ($user === null) {
            throw new NotFoundHttpException();
        }

        $isSelf = $id === $this->getUser()->getId();

        return $this->render('pages/users/single.twig', [
            'is_self' => $isSelf,
            'user' => $user,
        ]);
    }
}
