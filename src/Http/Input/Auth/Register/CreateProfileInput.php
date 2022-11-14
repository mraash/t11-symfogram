<?php

declare(strict_types=1);

namespace App\Http\Input\Auth\Register;

use App\Extension\Http\Input\AbstractBaseInput;

class CreateProfileInput extends AbstractBaseInput
{
    public function getFirstNameInput(): string
    {
        return $this->param('first_name');
    }

    public function getLastNameInput(): string
    {
        return $this->param('last_name');
    }

    public function getBioInput(): string
    {
        return $this->param('bio');
    }
}
