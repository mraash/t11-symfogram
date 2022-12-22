<?php

declare(strict_types=1);

namespace App\Http\Input\Post;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Optional;
use SymfonyExtension\Http\Input\AbstractBaseInput;
use SymfonyExtension\Validator\ParamType\Constraint\ArrayParamType;
use SymfonyExtension\Validator\ParamType\Constraint\FileParamType;

class CreatePostInput extends AbstractBaseInput
{
    protected function fields(): array
    {
        return [
            'title' => new Optional([
                new Length(
                    max: 255,
                    maxMessage: 'Title should have {{ limit }} characters or less.'
                ),
            ]),
            'images' => new Optional([
                new ArrayParamType(
                    message: 'Request is invalid.'
                ),
                new All([
                    new FileParamType(
                        message: 'Request is invalid.'
                    ),
                ])
            ]),
        ];
    }

    public function getTitleParamOrNull(): ?string
    {
        /** @var string */
        return $this->param('title');
    }

    /**
     * @return UploadedFile[]|null
     */
    public function getImageParamsOrNull(): ?array
    {
        /** @var ?UploadedFile[] */
        return $this->param('images');
    }
}
