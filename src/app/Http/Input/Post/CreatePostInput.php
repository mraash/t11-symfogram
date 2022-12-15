<?php

declare(strict_types=1);

namespace App\Http\Input\Post;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Optional;
use SymfonyExtension\Http\Input\AbstractBaseInput;
use SymfonyExtension\Support\Validator\ParamType\Constraint\FileParamType;

class CreatePostInput extends AbstractBaseInput
{
    protected function fields(): array
    {
        return [
            'title' => [
                new Optional(),
            ],
            'images' => new All([
                new FileParamType(),
            ]),
        ];
    }

    public function getTitleParam(): string
    {
        /** @var string */
        return $this->param('title');
    }

    /**
     * @return UploadedFile[]
     */
    public function getImageParams(): array
    {
        /** @var UploadedFile[] */
        return $this->param('images');
    }
}
