<?php

declare(strict_types=1);

namespace App\Http\Controller\Auth;

use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', methods: ['GET', 'HEAD'], name: 'pages.login')]
    public function showLogin(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUserOrNull()?->hasBasedRole()) {
            return $this->redirectToRoute('pages.index');
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        if ($error !== null) {
            $this->addErrorFlash($error->getMessage());
        }

        return $this->render('pages/auth/login.twig');
    }

    #[Route('/login', methods: ['POST'], name: 'actions.login')]
    public function login(): void
    {
        throw new \LogicException('This method should not be executed.');
    }

    #[Route('/logout', methods: ['GET', 'HEAD'], name: 'pactions.logout')]
    public function logout(): void
    {
        throw new \LogicException('This method should not be executed.');
    }
}
