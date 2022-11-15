<?php

declare(strict_types=1);

namespace App\Library\Token;

class RandomStringGenerator
{
    /**
     * Generate random string that contains 0-1, a-z, A-Z charactars
     */
    public function generateSimpleToken(int $length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
     
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
     
        return $randomString;
    }
}
