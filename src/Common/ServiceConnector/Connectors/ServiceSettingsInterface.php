<?php

declare(strict_types=1);

namespace App\Common\ServiceConnector\Connectors;

/**
 * Interface ServiceConnectorInterface
 * @package App\Common\ServiceConnector\Connectors
 */
interface ServiceSettingsInterface
{
    /**
     * @return array
     */
    public function readSettings(): array;

    /**
     * @param array $jsonSettings
     *
     * @return bool
     */
    public function updateSettings(array $jsonSettings): bool;
}
