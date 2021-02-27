<?php

declare(strict_types=1);

namespace App\Common\ServiceConnector\Connectors;

/**
 * Interface FabricServiceConnectorInterface
 * @package App\Common\ServiceConnector\Connectors
 */
interface FabricServiceConnectorInterface
{
    /**
     * @param string $serviceName
     * @param string $serviceAddress
     *
     * @return ServiceSettingsInterface|null
     */
    public static function getConnectorObject(string $serviceName, string $serviceAddress): ?ServiceSettingsInterface;
}
