<?php

declare(strict_types=1);

namespace SymfonyExtension\Http\Input;

use Library\Exceptions\UnexpectedReturnTypeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use SymfonyExtension\Validator\ParamType\AbstractParamType;
use SymfonyExtension\Validator\ParamType\ParamTypeConverterFactory;

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

        $params = $this->pullRawParams($currentRequest);
        $params = $this->convertEmptyStringsToNull($params);
        $params = $this->convertAllParamsType($params, $rules);

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
     * @param array<string,mixed> $params
     *
     * @return array<string,mixed>
     */
    private function convertEmptyStringsToNull(array $params): array
    {
        $newParams = $params;

        foreach ($newParams as &$paramValue) {
            if ($paramValue === '') {
                $paramValue = null;
            }

            $paramValue = $paramValue === '' ? null : $paramValue;

            if (is_array($paramValue)) {
                foreach ($paramValue as &$paramValueItem) {
                    $paramValueItem = $paramValueItem === '' ? null : $paramValueItem;
                }
            }
        }

        return $newParams;
    }

    /**
     * @param array<string,mixed> $params
     *
     * @return array<string,mixed>
     */
    private function convertAllParamsType(array $params, Collection $rules): array
    {
        $newParams = $params;

        foreach ($rules->fields as $paramName => $paramConstraints) {
            /** @var string $paramName */

            $paramConstraints = is_array($paramConstraints) ? $paramConstraints : [$paramConstraints];
            $paramValue = $params[$paramName] ?? null;

            $newParams[$paramName] = $this->convertParamType($paramValue, $paramConstraints);
        }

        return $newParams;
    }

    /**
     * @param Constraint[] $constraints
     */
    private function convertParamType(mixed $paramValue, array $constraints): mixed
    {
        $converted = $paramValue;

        foreach ($constraints as $constraint) {
            if ($constraint instanceof AbstractParamType) {
                $converted = $this->convertValue($paramValue, $constraint);
                continue;
            }

            if ($constraint instanceof All) {
                if (!is_array($converted)) {
                    continue;
                }

                foreach ($constraint->constraints as $allItemsConstraint) {
                    if ($allItemsConstraint instanceof AbstractParamType) {
                        foreach ($converted as $i => $paramValueItem) {
                            $converted[$i] = $this->convertValue($paramValueItem, $allItemsConstraint);
                        }
                    }
                }
            }
        }

        return $converted;
    }

    private function convertValue(mixed $paramValue, AbstractParamType $typeConstraint): mixed
    {
        return $this->paramTypeConverterFactory
            ->getInstance($typeConstraint)
            ->convertIfPossible($paramValue)
        ;
    }
}
