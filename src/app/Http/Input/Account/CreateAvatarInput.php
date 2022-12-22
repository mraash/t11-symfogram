<?php

declare(strict_types=1);

namespace App\Http\Input\Account;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use SymfonyExtension\Http\Input\AbstractBaseInput;
use SymfonyExtension\Validator\NotEmpty;
use SymfonyExtension\Validator\ParamType\Constraint\FileParamType;

class CreateAvatarInput extends AbstractBaseInput
{
    protected function fields(): array
    {
        return [
            'avatar' => [
                new NotEmpty(
                    message: 'Request is invalid.'
                ),
                new FileParamType(),
            ],
        ];
    }

    public function getAvatarParam(): UploadedFile
    {
        /** @var UploadedFile */
        return $this->param('avatar');
    }
}
