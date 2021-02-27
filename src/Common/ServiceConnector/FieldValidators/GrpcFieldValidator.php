<?php

declare(strict_types=1);

namespace App\Common\ServiceConnector\FieldValidators;

use Respect\Validation\Validator as v;

/**
 * Class GrpcFieldValidator
 * @package App\Common\ServiceConnector\FieldValidators
 */
final class GrpcFieldValidator extends AbstractValidator
{
    protected function rules(): array
    {
        return [
            'field1' => [
                v::notEmpty(),
                v::boolType(),
                v::boolVal(),
                v::equals(true)
            ],
            'field2' => [
                v::notEmpty(),
                v::intType(),
            ],
            'field3' => [
                v::notEmpty(),
                v::length(2),
                v::arrayType()
                    ->key(
                        '0',
                        v::oneOf(
                            v::stringType(),
                            v::intType(),
                        )
                    )
                    ->key(
                        '1',
                        v::oneOf(
                            v::stringType(),
                            v::intType()
                        )
                    ),
            ],
        ];
    }
}
