<?php

declare(strict_types=1);

namespace App\Http\SupportService\FileUploader;

use Library\Token\RandomStringGenerator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private const FILE_NAME_LENGTH = 40;

    public function __construct(
        private RandomStringGenerator $randomStringGenerator
    ) {
    }

    /**
     * @throws FileException
     */
    public function upload(string $path, UploadedFile $file): PublicFilename
    {
        $name = $this->randomStringGenerator->generateUriString(self::FILE_NAME_LENGTH);
        $extension = $file->guessExtension();

        $path = $this->addEndSlash($path);

        $filename = $name . '.' . $extension;

        try {
            $file->move($path, $filename);
        }
        catch (FileException $err) {
            throw $err;
        }

        return new PublicFilename($path . $filename);
    }

    private function addEndSlash(string $path): string
    {
        return preg_match('/\/$/', $path) === 1 ? $path : $path . '/';
    }
}
