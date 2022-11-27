<?php

declare(strict_types=1);

namespace SymfonyExtension\Http\Input;

use Library\Exceptions\UnexpectedReturnTypeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use SymfonyExtension\Support\Validator\ParamType\AbstractParamType;
use SymfonyExtension\Support\Validator\ParamType\ParamTypeConverterFactory;

abstract class AbstractInput
{
    private Collection $rules;
    /** @var array<string,mixed> */
    private array $params;

    public function __construct(
        private ValidatorInterface $validator,
        private ParamTypeConverterFactory $paramTypeConverterFactory,
        RequestStack $requestStack
    ) {
        $this->validator = $validator;

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
        return $request->query->all() + $request->request->all() + $request->files->all();
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
            // $param should be string, but phpstan says that it can be int.
            $paramName = (string)$param;

            /** @var AbstractParamType|null */
            $typeConstraint = null;

            foreach ($paramConstraints->constraints as $constraint) {
                if ($constraint instanceof AbstractParamType) {
                    $typeConstraint = $constraint;
                    break;
                }
            }

            if (isset($typeConstraint)) {
                $paramValue = $params[$param] ?? null;
                $converter = $this->paramTypeConverterFactory->getInstance($typeConstraint);

                $newParams[$paramName] = $converter->convertIfPossible($paramValue);
            }
        }
        return $newParams;
    }
}
