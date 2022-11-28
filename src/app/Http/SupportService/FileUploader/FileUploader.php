<?php

declare(strict_types=1);

namespace App\Http\SupportService\FileUploader;

use Library\Token\RandomStringGenerator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    public function __construct(
        private RandomStringGenerator $randomStringGenerator
    ) {
    }

    public function createFilename(UploadedFile $file, string $path, int $filenameBaseLength = 50): PublicFilename
    {
        $filenameBase = $this->randomStringGenerator->generateUriString($filenameBaseLength);
        $extension = $file->guessExtension();

        $path = $this->addEndSlash($path);
        $filename = $filenameBase . '.' . $extension;

        $uri = $path . $filename;

        return new PublicFilename($uri);
    }

    /**
     * @throws FileException
     */
    public function upload(UploadedFile $file, PublicFilename $uri): void
    {
        $path = $uri->getPath();
        $filename = $uri->getFilename();

        try {
            $file->move($path, $filename);
        }
        catch (FileException $err) {
            throw $err;
        }
    }

    private function addEndSlash(string $path): string
    {
        return preg_match('/\/$/', $path) === 1 ? $path : $path . '/';
    }
}
