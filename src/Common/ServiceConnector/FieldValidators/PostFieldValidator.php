<?php

declare(strict_types=1);

namespace App\Common\ServiceConnector\FieldValidators;

use Respect\Validation\Validator as v;

/**
 * Class PostFieldValidator
 * @package App\Common\ServiceConnector\FieldValidators
 */
final class PostFieldValidator extends AbstractValidator
{
    /**
     * @return array[]
     */
    protected function rules(): array
    {
        return [
            'field1' => [
                v::notEmpty(),
                v::stringType(),
            ],
            'field2' => [
                v::notEmpty(),
                v::boolType(),
                v::boolVal(),
                v::equals(true)
            ],
            'field3' => [
                v::notEmpty(),
                v::intVal(),
            ],
        ];
    }
}
