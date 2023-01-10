<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Domain\Entity\User;
use Library\Exceptions\UnexpectedReturnTypeException;
use SymfonyExtension\Http\Controller\AbstractController as AbstractBaseController;

abstract class AbstractController extends AbstractBaseController
{
    protected function getUserOrNull(): ?User
    {
        /** @var ?User */
        return parent::getUser();
    }

    protected function getUser(): User
    {
        return $this->getUserOrNull() ?? throw new UnexpectedReturnTypeException();
    }

    protected function isLogged(): bool
    {
        return $this->getUserOrNull() !== null;
    }
}
