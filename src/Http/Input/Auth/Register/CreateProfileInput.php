<?php

declare(strict_types=1);

namespace App\Http\Input\Auth\Register;

use App\Extension\Http\Input\AbstractBaseInput;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Required;

class CreateProfileInput extends AbstractBaseInput
{
    protected function fields(): array
    {
        return [
            'first_name' => new Required([
                new NotBlank(
                    message: 'First name is required'
                ),
                new Length(
                    max: 40,
                    maxMessage: 'First name should have {{ limit }} characters or less.'
                ),
            ]),
            'last_name' => new Required([
                new NotBlank(
                    message: 'Last name is required'
                ),
                new Length(
                    max: 40,
                    maxMessage: 'Last name should have {{ limit }} characters or less.'
                ),
            ]),
            'bio' => new Optional([
                new Length(
                    max: 350,
                    maxMessage: 'Biography should have {{ limit }} characters or less.'
                ),
            ]),
        ];
    }

    public function getFirstNameInput(): string
    {
        return $this->param('first_name');
    }

    public function getLastNameInput(): string
    {
        return $this->param('last_name');
    }

    public function getBioInput(): string
    {
        return $this->param('bio');
    }
}
