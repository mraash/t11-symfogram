<?php

declare(strict_types=1);

namespace Library\Token;

class RandomStringGenerator
{
    /**
     * Generate random uri friendly string.
     */
    public function generateUriString(int $length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return $this->generateRandomString($characters, $length);
    }

    public function generateRandomString(string $allowedCharacters, int $length): string
    {
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($allowedCharacters) - 1);
            $randomString .= $allowedCharacters[$index];
        }

        return $randomString;
    }
}
