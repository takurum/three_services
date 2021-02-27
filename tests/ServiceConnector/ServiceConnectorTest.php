<?php

declare(strict_types=1);

namespace App\Tests\ServiceConnector;

use App\Common\Enum\ServiceName;
use App\Common\ServiceConnector\Connectors\GrpcServiceSettings;
use App\Common\ServiceConnector\Connectors\PostServiceSettings;
use App\Common\ServiceConnector\Connectors\ServiceSettingsInterface;
use App\Common\ServiceConnector\Connectors\FabricServiceConnector;
use App\Common\ServiceConnector\Connectors\GetServiceSettings;
use App\Common\ServiceConnector\FieldValidators\FieldValidatorInterface;
use App\Common\ServiceConnector\FieldValidators\GetFieldValidator;
use App\Common\ServiceConnector\FieldValidators\GrpcFieldValidator;
use App\Common\ServiceConnector\FieldValidators\PostFieldValidator;
use App\Tests\BaseTest;
use ReflectionClass;

/**
 * Class ServiceConnectorTest
 * @package App\Tests\ServiceConnector
 */
class ServiceConnectorTest extends BaseTest
{
    private array $constantsServicesNames;

    /**
     * ServiceConnectorTest constructor.
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $reflectionClass = new ReflectionClass(ServiceName::class);
        $this->constantsServicesNames = $reflectionClass->getConstants();
    }

    public function testServiceAddressUrl()
    {
        foreach ($this->projectParams as $serviceName => $address) {
            static::assertEquals(filter_var($address, FILTER_VALIDATE_URL), $address);
        }
    }

    public function testFabricClass()
    {
        foreach ($this->projectParams as $serviceName => $address) {
            $connectorObject = FabricServiceConnector::getConnectorObject($serviceName, $address);
            static::assertTrue($connectorObject instanceof ServiceSettingsInterface);
        }
    }

    public function testFailFabricClass()
    {
        static::assertNull(FabricServiceConnector::getConnectorObject('test', 'test'));
    }

    public function testReadingGetServiceSettings()
    {
        $connectorObject = FabricServiceConnector::getConnectorObject(ServiceName::GET_SERVICE, $this->projectParams[ServiceName::GET_SERVICE]);
        static::assertInstanceOf(GetServiceSettings::class, $connectorObject);

        $serviceSettingsArray = $connectorObject->readSettings();

        static::assertIsArray($serviceSettingsArray);
        static::assertNotEmpty($serviceSettingsArray);
        static::assertTrue((new GetFieldValidator())->validate($serviceSettingsArray));
    }

    public function testFailGetValidator()
    {
        $validator = new GetFieldValidator();

        $settings = [
            'field1' => false,
            'field2' => true,
            'field3' => [
                0 => 'string',
            ],
        ];
        $this->checkServiceFields($validator, $settings);

        $settings = [
            'field1' => 'string',
            'field2' => false,
            'field3' => [
                0 => 'string',
            ],
        ];
        $this->checkServiceFields($validator, $settings);

        $settings = [
            'field1' => 'string',
            'field2' => true,
            'field3' => 1,
        ];
        $this->checkServiceFields($validator, $settings);

        $settings = [
            'field1' => 'string',
            'field2' => true,
            'field3' => [],
        ];
        $this->checkServiceFields($validator, $settings);

        $settings = [
            'field1' => 'string',
            'field2' => true,
            'field3' => [
                0 => 1,
            ],
        ];
        $this->checkServiceFields($validator, $settings);
    }

    public function testReadingPostServiceSettings()
    {
        $connectorObject = FabricServiceConnector::getConnectorObject(
            ServiceName::POST_SERVICE,
            $this->projectParams[ServiceName::POST_SERVICE]
        );

        static::assertInstanceOf(PostServiceSettings::class, $connectorObject);

        $serviceSettingsArray = $connectorObject->readSettings();
        static::assertIsArray($serviceSettingsArray);
        static::assertNotEmpty($serviceSettingsArray);

        static::assertTrue((new PostFieldValidator())->validate($serviceSettingsArray));
    }

    public function testFailPostValidator()
    {
        $validator = new PostFieldValidator();

        $settings = [
            'field1' => 1.0,
            'field2' => true,
            'field3' => 1,
        ];
        $this->checkServiceFields($validator, $settings);

        $settings = [
            'field1' => 'string',
            'field2' => true,
            'field3' => []
        ];
        $this->checkServiceFields($validator, $settings);

        $settings = [
            'field1' => 'string',
            'field2' => true,
            'field3' => 2.5,
        ];
        $this->checkServiceFields($validator, $settings);

        $settings = [
            'field1' => 'string',
            'field2' => false,
            'field3' => 1
        ];
        $this->checkServiceFields($validator, $settings);

        $settings = [
            'field1' => 'string',
            'field2' => true,
        ];
        $this->checkServiceFields($validator, $settings);
    }

    public function testReadingGrpcServiceSettings()
    {
        $connectorObject = FabricServiceConnector::getConnectorObject(
            ServiceName::GRPC_SERVICE,
            $this->projectParams[ServiceName::GRPC_SERVICE]
        );

        static::assertInstanceOf(GrpcServiceSettings::class, $connectorObject);

        $serviceSettingsArray = $connectorObject->readSettings();
        static::assertIsArray($serviceSettingsArray);
        static::assertNotEmpty($serviceSettingsArray);

        static::assertTrue((new GrpcFieldValidator())->validate($serviceSettingsArray));
    }

    public function testFailGrpcValidator()
    {
        $validator = new GrpcFieldValidator();

        $settings = [
            'field1' => false,
            'field2' => 1,
            'field3' => [
                0 => 'string',
                1 => 1
              ]
        ];
        $this->checkServiceFields($validator, $settings);

        $settings = [
            'field1' => true,
            'field2' => 'test',
            'field3' => [
                0 => 'string',
                1 => 1
            ]
        ];
        $this->checkServiceFields($validator, $settings);

        $settings = [
            'field1' => true,
            'field2' => 1,
            'field3' => 1,
        ];
        $this->checkServiceFields($validator, $settings);

        $settings = [
            'field1' => true,
            'field2' => 1,
            'field3' => [
                0 => 'string',
            ],
        ];
        $this->checkServiceFields($validator, $settings);

        $settings = [
            'field1' => true,
            'field2' => 1,
            'field3' =>  [
                1 => 1
            ]
        ];
        $this->checkServiceFields($validator, $settings);

        $settings = [
            'field1' => true,
            'field2' => 1,
            'field3' => [],
        ];
        $this->checkServiceFields($validator, $settings);

        $settings = [
            'field1' => true,
            'field2' => 1,
        ];
        $this->checkServiceFields($validator, $settings);
    }

    /**
     * @param FieldValidatorInterface $validator
     * @param array $settings
     */
    private function checkServiceFields(FieldValidatorInterface $validator, array $settings)
    {
        static::assertFalse($validator->validate($settings));
        static::assertIsArray($validator->errors());
        static::assertNotEmpty($validator->errors());
    }
}
