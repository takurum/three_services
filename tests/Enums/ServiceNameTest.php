<?php

declare(strict_types=1);

namespace App\Tests\Enums;

use App\Common\Enum\ServiceName;
use App\Tests\BaseTest;
use ReflectionClass;

/**
 * Class ServiceNameTest
 * @package App\Tests\Enums
 */
class ServiceNameTest extends BaseTest
{
    /** @var array */
    protected array $constantsServicesNames;

    /**
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

    public function testInvalidServiceName()
    {
        static::assertFalse(ServiceName::isValidName('test'));
    }

    public function testValidServiceName()
    {
        foreach ($this->constantsServicesNames as $const => $name) {
            static::assertTrue(ServiceName::isValidName($name));
        }
    }

    public function testServicesNamesCount()
    {
        static::assertSameSize($this->constantsServicesNames, $this->projectParams);
    }
}
