<?php

declare(strict_types=1);

namespace App\Common\ServiceConnector\FieldValidators;

/**
 * Interface FieldValidatorInterface
 * @package App\Common\ServiceConnector\FieldValidators
 */
interface FieldValidatorInterface
{
    /**
     * @param array $serviceConfig
     * @return bool
     */
    public function validate(array $serviceConfig): bool;

    /**
     * @return array
     */
    public function errors(): array;
}
