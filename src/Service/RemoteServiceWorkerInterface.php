<?php

declare(strict_types=1);

namespace App\Service;

use App\Common\Enum\Response\ResponseTransformerInterface;
use App\Common\ServiceConnector\Connectors\ServiceSettingsInterface;

/**
 * Interface RemoteServiceWorkerInterface
 * @package App\Service
 */
interface RemoteServiceWorkerInterface
{
    /**
     * @param string $serviceName
     * @param string $serviceAddress
     * @return array
     */
    public function getServiceSettings(string $serviceName, string $serviceAddress): array;

    /**
     * @param array $settingsArray
     * @param ServiceSettingsInterface $classConnector
     * @return bool
     */
    public function validateServiceSettings(array $settingsArray, ServiceSettingsInterface $classConnector): bool;

    /**
     * @param string $serviceName
     * @param string $serviceAddress
     * @param array $serviceSettingsArray
     * @return ResponseTransformerInterface
     */
    public function updateSettings(string $serviceName, string $serviceAddress, array $serviceSettingsArray): ResponseTransformerInterface;
}
