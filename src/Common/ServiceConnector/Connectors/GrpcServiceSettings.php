<?php

declare(strict_types=1);

namespace App\Common\ServiceConnector\Connectors;

use App\Common\ServiceConnector\FieldValidators\FieldValidatorInterface;
use App\Common\ServiceConnector\FieldValidators\GrpcFieldValidator;

/**
 * Class GrpcServiceConnector
 * @package App\Common\ServiceConnector\Connectors
 */
final class GrpcServiceSettings implements ServiceSettingsInterface
{
    protected const REQUEST_METHOD = 'POST';

    /** @var string */
    private string $address;

    /** @var array */
    protected array $serviceSettingsArray = [];

    /** @var FieldValidatorInterface */
    protected FieldValidatorInterface $validator;

    /**
     * GetServiceConnector constructor.
     * @param string $address
     */
    public function __construct(string $address)
    {
        $this->validator = new GrpcFieldValidator();
        $this->address = $address;
    }

    /* In the other case, these methods will use for connecting to gRPC service. */

    /**
     * @return array
     */
    public function readSettings(): array
    {
        $this->serviceSettingsArray = json_decode('{"field1": true, "field2": 1, "field3": ["string", 1]}', true);

        return $this->serviceSettingsArray;
    }

    /**
     * @param array $jsonSettings
     *
     * @return bool
     */
    public function updateSettings(array $jsonSettings): bool
    {
        if (true !== $this->validator->validate($jsonSettings))  {
            return false;
        }

        return true;
    }
}
