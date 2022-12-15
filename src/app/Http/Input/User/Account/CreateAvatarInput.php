<?php

declare(strict_types=1);

namespace App\Http\Input\User\Account;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use SymfonyExtension\Http\Input\AbstractBaseInput;
use SymfonyExtension\Support\Validator\ParamType\Constraint\FileParamType;

class CreateAvatarInput extends AbstractBaseInput
{
    protected function fields(): array
    {
        return [
            'avatar' => new FileParamType(),
        ];
    }

    public function getAvatar(): UploadedFile
    {
        /** @var UploadedFile */
        return $this->param('avatar');
    }
}
