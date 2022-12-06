<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Domain\Entity\User;
use SymfonyExtension\Http\Controller\AbstractController as AbstractBaseController;

abstract class AbstractController extends AbstractBaseController
{
    protected function getUser(): User
    {
        /** @var User */
        return parent::getUser();
    }
}
