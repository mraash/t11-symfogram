<?php

declare(strict_types=1);

namespace App\Extension\Http\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as AbstractSymfonyController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractController extends AbstractSymfonyController
{
    protected Request $currentRequest;

    public function __construct(RequestStack $requestStack)
    {
        $this->currentRequest = $requestStack->getCurrentRequest();
    }

    protected function redirectBack(int $status = 302): RedirectResponse
    {
        $path = $this->currentRequest->headers->get('referer');
        return $this->redirect($path, $status);
    }
}
