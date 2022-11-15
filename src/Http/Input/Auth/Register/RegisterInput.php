<?php

declare(strict_types=1);

namespace App\Http\Input\Auth\Register;

use App\Extension\Http\Input\AbstractBaseInput;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;

class RegisterInput extends AbstractBaseInput
{
    protected function fields(): array
    {
        return [
            'email' => new Required([
                new NotBlank(
                    message: 'Email should be not blank'
                ),
                new Email(
                    null,
                    'Email is invalid'
                ),
                new Length(
                    max: 180,
                    maxMessage: 'Email should have {{ limit }} characters or less.'
                ),
            ]),
            'password' => new Required([
                new Length(
                    min: 3,
                    max: 100,
                    minMessage: 'Password should have more than {{ limit }} characters.',
                    maxMessage: 'Password should have {{ limit }} characters or less.',
                ),
            ]),
            'password_repeat' => new Required([
                // new NotBlank(
                //     message: 'Password repeat should be not blank'
                // ),
            ]),
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
