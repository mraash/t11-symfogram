<?php

namespace App\Extension\Http\Input;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractInput
{
    protected ValidatorInterface $validator;
    private array $parameters;

    public function __construct(RequestStack $requestStack, ValidatorInterface $validator)
    {
        $request = $requestStack->getCurrentRequest();

        $this->validator = $validator;
        $this->parameters = $this->pullParams($request);
    }

    abstract protected function rules(): Collection;

    public function validate(): ConstraintViolationListInterface
    {
        return $this->validator->validate($this->parameters, $this->rules());
    }

    protected function param(string $name): mixed
    {
        return $this->parameters[$name] ?? null;
    }

    protected function pullParams(Request $request): array
    {
        return $request->query->all() + $request->request->all();
    }
}
