<?php

declare(strict_types=1);

namespace App\Http\Input\Auth\Register;

use App\Extension\Http\Input\AbstractBaseInput;
use App\Extension\Support\Validator\NotEmpty;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Optional;

class CreateProfileInput extends AbstractBaseInput
{
    protected function fields(): array
    {
        return [
            'first_name' => [
                new NotEmpty(
                    message: 'First name is required.'
                ),
                new Length(
                    max: 40,
                    maxMessage: 'First name should have {{ limit }} characters or less.'
                ),
            ],
            'last_name' => [
                new NotEmpty(
                    message: 'Last name is required.'
                ),
                new Length(
                    max: 40,
                    maxMessage: 'Last name should have {{ limit }} characters or less.'
                ),
            ],
            'bio' => new Optional([
                new Length(
                    max: 350,
                    maxMessage: 'Biography should have {{ limit }} characters or less.'
                ),
            ]),
        ];
    }

    public function getFirstNameParam(): string
    {
        return $this->param('first_name');
    }

    public function getLastNameParam(): string
    {
        return $this->param('last_name');
    }

    public function getBioParam(): string
    {
        return $this->param('bio');
    }
}
