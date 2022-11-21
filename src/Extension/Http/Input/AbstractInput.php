<?php

declare(strict_types=1);

namespace App\Extension\Http\Input;

use App\Extension\Support\Validator\ParamType;
use App\Extension\Support\Validator\ParamTypeValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractInput
{
    private ValidatorInterface $validator;
    private ParamTypeValidator $paramTypeValidator;  // Needed only for parameter conversion.
    private Collection $rules;
    private array $params;

    public function __construct(
        ValidatorInterface $validator,
        ParamTypeValidator $paramTypeValidator,
        RequestStack $requestStack
    ) {
        $this->validator = $validator;
        $this->paramTypeValidator = $paramTypeValidator;

        $currentRequest = $requestStack->getCurrentRequest();
        $rules = $this->rules();

        $rawParams = $this->pullParams($currentRequest);
        $params = $this->convertParamTypes($rawParams, $rules);

        $this->rules = $rules;
        $this->params = $params;
    }

    public function validate(): ConstraintViolationListInterface
    {
        return $this->validator->validate($this->params, $this->rules);
    }

    abstract protected function rules(): Collection;

    protected function param(string $key): mixed
    {
        return $this->params[$key] ?? null;
    }

    private function pullParams(Request $request): array
    {
        return $request->query->all() + $request->request->all();
    }

    private function convertParamTypes(array $params, Collection $rules): array
    {
        $newParams = $params;

        foreach ($rules->fields as $param => $paramConstraints) {
            /** @var ?ParamType */
            $paramType = null;

            foreach ($paramConstraints->constraints as $constraint) {
                if ($constraint instanceof ParamType) {
                    $paramType = $constraint;
                    break;
                }
            }

            if ($constraint instanceof ParamType) {
                $type = $paramType->type;
                $value = $newParams[$param] ?? null;
    
                $newParams[$param] = $this->paramTypeValidator->convertIfPossible($type, $value);
            }
        }

        return $newParams;
    }
}
