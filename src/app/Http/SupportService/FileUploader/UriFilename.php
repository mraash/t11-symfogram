<?php

declare(strict_types=1);

namespace App\Http\SupportService\FileUploader;

class UriFilename
{
    private string $path;
    private string $name;
    private string $extension;

    public function __construct(string $fullFilename)
    {
        $folders = explode('/', $fullFilename);
        $filename = array_pop($folders);
        $this->path = implode('/', $folders);

        $splitedFilename = explode('.', $filename);

        $this->extension = count($splitedFilename) > 1 ? array_pop($splitedFilename) : '';
        $this->name = implode('.', $splitedFilename);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFilenameBase(): string
    {
        return $this->name;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getFilename(): string
    {
        return $this->getFileNameBase() . '.' . $this->getExtension();
    }

    public function getFullUri(): string
    {
        return $this->getPath() . '/' . $this->getFileName();
    }
}
