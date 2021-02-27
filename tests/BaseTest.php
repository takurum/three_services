<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BaseTest
 * @package App\Tests
 */
abstract class BaseTest extends WebTestCase
{
    public static $client;

    /** @var ContainerInterface */
    protected ContainerInterface $normalContainer;

    protected array $projectParams;

    /**
     * BaseTest constructor.
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        if (null === self::$client) {
            self::$client = static::createClient();
        }

        $this->normalContainer = self::$client->getContainer();
        $this->projectParams = $this->normalContainer->getParameter('addresses_of_services');
    }
}
