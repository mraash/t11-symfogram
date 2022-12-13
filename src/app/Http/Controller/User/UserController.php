<?php

declare(strict_types=1);

namespace App\Http\Controller\User;

use App\Domain\Repository\UserRepository;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        RequestStack $requestStack
    ) {
        parent::__construct($requestStack);
    }

    #[Route('/users', methods: ['HEAD', 'GET'], name: 'pages.users.index')]
    public function showIndex(): Response
    {
        $users = $this->userRepository->findAll();

        return $this->render('pages/users/index.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/{id<\d+>}', methods: ['HEAD', 'GET'], name: 'pages.users.single')]
    public function showSingle(int $id): Response
    {
        $user = $this->userRepository->findByIdOrNull($id);

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
