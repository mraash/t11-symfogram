<?php

declare(strict_types=1);

namespace App\Http\Input\Auth\Register;

use App\Extension\Http\Input\AbstractBaseInput;
use Symfony\Component\Validator\Constraints\Required;

class RegisterInput extends AbstractBaseInput
{
    protected function fields(): array
    {
        return [
            'email' => new Required(),
            'password' => new Required(),
            'password_repeat' => new Required(),
        ];
    }

    public function getEmailInput(): string
    {
        return $this->param('email');
    }

    public function getPasswordInput(): string
    {
        return $this->param('password');
    }

    public function getPasswordRepeatInput(): string
    {
        return $this->param('password_repeat');
    }
}
