<?php

declare(strict_types=1);

namespace App\Http\Input\Auth\Register;

use App\Extension\Http\Input\AbstractBaseInput;
use App\Extension\Support\Validator\NotEmpty;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;

class RegisterInput extends AbstractBaseInput
{
    protected function fields(): array
    {
        return [
            'email' => [
                new NotEmpty(
                    message: 'Email is required.'
                ),
                new Email(
                    message: 'Email is invalid.'
                ),
                new Length(
                    max: 180,
                    maxMessage: 'Email should have {{ limit }} characters or less.'
                ),
            ],
            'password' => [
                new NotEmpty(
                    message: 'Password is requried.'
                ),
                new Length(
                    min: 3,
                    max: 100,
                    minMessage: 'Password should have more than {{ limit }} characters.',
                    maxMessage: 'Password should have {{ limit }} characters or less.',
                ),
            ],
            'password_repeat' => [
                // new NotEmpty(
                //     message: 'Password repeat is required.'
                // ),
            ],
        ];
    }

    public function getEmailParam(): string
    {
        return $this->param('email');
    }

    public function getPasswordParam(): string
    {
        return $this->param('password');
    }

    public function getPasswordRepeatParam(): string
    {
        return $this->param('password_repeat');
    }
}
