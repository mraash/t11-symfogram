<?php

declare(strict_types=1);

namespace App\Http\Input\Account;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Optional;
use SymfonyExtension\Http\Input\AbstractBaseInput;
use SymfonyExtension\Validator\NotEmpty;
use SymfonyExtension\Validator\ParamType\Constraint\FileParamType;
use SymfonyExtension\Validator\ParamType\Constraint\StringParamType;

class EditAccountInput extends AbstractBaseInput
{
    protected function fields(): array
    {
        return [
            'first_name' => [
                new NotEmpty(
                    message: 'First name is required.'
                ),
                new StringParamType(),
                new Length(
                    max: 40,
                    maxMessage: 'First name should have {{ limit }} characters or less.'
                ),
            ],
            'last_name' => [
                new NotEmpty(
                    message: 'Last name is required.'
                ),
                new StringParamType(),
                new Length(
                    max: 40,
                    maxMessage: 'Last name should have {{ limit }} characters or less.'
                ),
            ],
            'bio' => new Optional([
                new StringParamType(),
                new Length(
                    max: 350,
                    maxMessage: 'Biography should have {{ limit }} characters or less.'
                ),
            ]),
        ];
    }

    public function getFirstNameParam(): string
    {
        /** @var string */
        return $this->param('first_name');
    }

    public function getLastNameParam(): string
    {
        /** @var string */
        return $this->param('last_name');
    }

    public function getBioParamOrNull(): ?string
    {
        /** @var ?string */
        return $this->param('bio');
    }
}
