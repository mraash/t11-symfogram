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

    public function createFilename(UploadedFile $file, string $path, int $filenameLength = 50): UriFilename
    {
        $filenameBase = $this->randomStringGenerator->generateUriString($filenameLength);
        $extension = $file->guessExtension();

        $path = $this->addEndSlash($path);
        $filename = $filenameBase . '.' . $extension;

        $uri = $path . $filename;

        return new UriFilename($uri);
    }

    /**
     * @throws FileException
     */
    public function upload(UploadedFile $file, UriFilename $uri): void
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

    public function uploadAndReturnFilename(UploadedFile $file, string $path, int $filenameLength = 50): UriFilename
    {
        $uri = $this->createFilename($file, $path, $filenameLength);
        $this->upload($file, $uri);

        return $uri;
    }

    /**
     * @param UploadedFile[] $files
     *
     * @return UriFilename[]
     */
    public function uploadListAndReturnFilenames(array $files, string $path, int $filenameLength = 50): array
    {
        $uriList = [];

        foreach ($files as $file) {
            $uri = $this->createFilename($file, $path, $filenameLength);
            $this->upload($file, $uri);

            $uriList[] = $uri;
        }

        return $uriList;
    }

    private function addEndSlash(string $path): string
    {
        return preg_match('/\/$/', $path) === 1 ? $path : $path . '/';
    }
}
