<?php

declare(strict_types=1);

namespace App\Common\ServiceConnector\Connectors;

use App\Common\Enum\ServiceName;

/**
 * Class FabricServiceConnector
 * @package App\Common\ServiceConnector
 */
final class FabricServiceConnector implements FabricServiceConnectorInterface
{
    /**
     * @param string $serviceName
     * @param string $serviceAddress
     *
     * @return ServiceSettingsInterface|null
     */
    public static function getConnectorObject(string $serviceName, string $serviceAddress): ?ServiceSettingsInterface
    {
        switch ($serviceName) {
            case ServiceName::GET_SERVICE:
                return new GetServiceSettings($serviceAddress);
            case ServiceName::POST_SERVICE:
                return new PostServiceSettings($serviceAddress);
            case ServiceName::GRPC_SERVICE:
                return new GrpcServiceSettings($serviceAddress);
            default:
                return null;
        }
    }
}
