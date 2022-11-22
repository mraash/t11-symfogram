<?php

declare(strict_types=1);

namespace App\Http\Input\Auth\Register;

use App\Domain\Entity\User;
use App\Extension\Http\Input\AbstractBaseInput;
use App\Extension\Support\Validator\EntityExists;
use App\Extension\Support\Validator\EntityMissing;
use App\Extension\Support\Validator\NotEmpty;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

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
                new EntityMissing(
                    field: 'email',
                    entityClass: User::class,
                    message: 'Email is taken.'
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
        /** @var string */
        return $this->param('email');
    }

    public function getPasswordParam(): string
    {
        /** @var string */
        return $this->param('password');
    }

    public function getPasswordRepeatParam(): string
    {
        /** @var string */
        return $this->param('password_repeat');
    }
}
