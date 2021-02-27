<?php

declare(strict_types=1);

namespace App\Common\ServiceConnector\FieldValidators;

use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator;

/**
 * Class AbstractValidator
 * @package App\Common\ServiceConnector\FieldValidators
 */
abstract class AbstractValidator implements FieldValidatorInterface
{
    /** @var array */
    private array $errorList = [];

    /**
     * @param array $serviceConfig
     * @return bool
     */
    public function validate(array $serviceConfig): bool
    {
        $this->errorList = [];

        foreach ($this->rules() as $field => $validators) {
            $value = $serviceConfig[$field] ?? null;

            try {
                /** @var Validator $v */
                foreach ($validators as $v) {
                    $v->assert($value);
                }
            } catch (ValidationException $e) {
                $this->errorList[$field] = $e->getMessage();
            }
        }

        return empty($this->errorList);
    }

    /**
     * @return array
     */
    public function errors(): array
    {
        return $this->errorList;
    }

    /**
     * @return array
     */
    abstract protected function rules(): array;
}
