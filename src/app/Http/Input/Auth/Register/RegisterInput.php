<?php

declare(strict_types=1);

namespace App\Http\Input\Auth\Register;

use App\Domain\Entity\User;
use SymfonyExtension\Http\Input\AbstractBaseInput;
use SymfonyExtension\Validator\EntityExists;
use SymfonyExtension\Validator\EntityMissing;
use SymfonyExtension\Validator\NotEmpty;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use SymfonyExtension\Validator\ParamType\Constraint\StringParamType;

class RegisterInput extends AbstractBaseInput
{
    protected function fields(): array
    {
        return [
            'email' => [
                new NotEmpty(
                    message: 'Email is required.'
                ),
                new StringParamType(),
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
                new StringParamType(),
                new Length(
                    min: 3,
                    max: 100,
                    minMessage: 'Password should have more than {{ limit }} characters.',
                    maxMessage: 'Password should have {{ limit }} characters or less.',
                ),
            ],
            'password_repeat' => [
                new NotEmpty(
                    message: 'Password repeat is required.'
                ),
                new StringParamType(),
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
