<?php

declare(strict_types=1);

namespace App\Http\Input\Auth\Register;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use SymfonyExtension\Http\Input\AbstractBaseInput;
use SymfonyExtension\Support\Validator\NotEmpty;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Optional;
use SymfonyExtension\Support\Validator\ParamType\Constraint\FileParamType;

class CreateProfileInput extends AbstractBaseInput
{
    protected function fields(): array
    {
        return [
            'first_name' => [
                new NotEmpty(
                    message: 'First name is required.'
                ),
                new Length(
                    max: 40,
                    maxMessage: 'First name should have {{ limit }} characters or less.'
                ),
            ],
            'last_name' => [
                new NotEmpty(
                    message: 'Last name is required.'
                ),
                new Length(
                    max: 40,
                    maxMessage: 'Last name should have {{ limit }} characters or less.'
                ),
            ],
            'bio' => new Optional([
                new Length(
                    max: 350,
                    maxMessage: 'Biography should have {{ limit }} characters or less.'
                ),
            ]),
            'avatar' => [
                new FileParamType(),
            ],
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

    public function getBioParam(): string
    {
        /** @var string */
        return $this->param('bio');
    }

    public function getAvatarParam(): ?UploadedFile
    {
        /** @var ?UploadedFile */
        return $this->param('avatar');
    }
}
