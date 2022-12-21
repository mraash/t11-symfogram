<?php

declare(strict_types=1);

namespace SymfonyExtension\Http\Controller;

use Library\Exceptions\UnexpectedReturnTypeException;
use SymfonyExtension\Http\Input\AbstractInput;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as AbstractSymfonyController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class AbstractController extends AbstractSymfonyController
{
    protected Request $currentRequest;

    public function __construct(RequestStack $requestStack)
    {
        $this->currentRequest = $requestStack->getCurrentRequest() ?? throw new UnexpectedReturnTypeException();
    }

    protected function redirectBack(int $status = 302): RedirectResponse
    {
        $path = $this->currentRequest->headers->get('referer') ?? throw new UnexpectedReturnTypeException();

        return $this->redirect($path, $status);
    }

    protected function addInfoFlash(string $message): void
    {
        $this->addFlash('message', $message);
    }

    protected function addSuccessFlash(string $message): void
    {
        $this->addFlash('success', $message);
    }

    protected function addErrorFlash(string $message): void
    {
        $this->addFlash('error', $message);
    }

    /**
     * Method will add flash message if input is invalid. So if it returns false
     *   you should just redirect back, if true - continue handling request.
     *
     * @return bool True if input is valid and false if not.
     */
    protected function validateInput(AbstractInput $input): bool
    {
        $validationInfo = $input->validate();

        if ($validationInfo->count() === 0) {
            return true;
        }

        $message = (string) $validationInfo->get(0)->getMessage();
        $this->addErrorFlash($message);

        return false;
    }

    public function getStringParameter(string $name): string
    {
        /** @var string */
        return $this->getParameter($name);
    }
}
