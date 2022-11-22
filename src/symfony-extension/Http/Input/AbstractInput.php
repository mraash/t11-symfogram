<?php

declare(strict_types=1);

namespace SymfonyExtension\Http\Input;

use Library\Exceptions\UnexpectedReturnTypeException;
use SymfonyExtension\Support\Validator\ParamType;
use SymfonyExtension\Support\Validator\ParamTypeValidator;
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
    /** @var array<string,mixed> */
    private array $params;

    public function __construct(
        ValidatorInterface $validator,
        ParamTypeValidator $paramTypeValidator,
        RequestStack $requestStack
    ) {
        $this->validator = $validator;
        $this->paramTypeValidator = $paramTypeValidator;

        $currentRequest = $requestStack->getCurrentRequest() ?? throw new UnexpectedReturnTypeException();
        $rules = $this->rules();

        $rawParams = $this->pullRawParams($currentRequest);
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

    /**
     * @return array<string,string>
     */
    private function pullRawParams(Request $request): array
    {
        return $request->query->all() + $request->request->all();
    }

    /**
     * @param array<string,string> $params
     *
     * @return array<string,mixed>
     */
    private function convertParamTypes(array $params, Collection $rules): array
    {
        $newParams = $params;

        foreach ($rules->fields as $param => $paramConstraints) {
            // $param will be string, but phpstan says that it can be int.
            $paramKey = (string)$param;

            foreach ($paramConstraints->constraints as $constraint) {
                if ($constraint instanceof ParamType) {
                    /** @var ParamType */
                    $typeConstraint = $constraint;
                    break;
                }
            }

            if (isset($typeConstraint)) {
                $type = $typeConstraint->type;
                $value = $params[$param] ?? null;

                $newParams[$paramKey] = $this->paramTypeValidator->convertIfPossible($type, $value);
            }
        }

        return $newParams;
    }
}
