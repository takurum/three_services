<?php

declare(strict_types=1);

namespace App\Service;

use App\Common\Enum\Response\ErrorResponse;
use App\Common\Enum\Response\ResponseTransformer;
use App\Common\Enum\Response\ResponseTransformerInterface;
use App\Common\Enum\Response\SuccessResponse;
use App\Common\ServiceConnector\Connectors\FabricServiceConnector;
use App\Common\ServiceConnector\Connectors\ServiceSettingsInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class RemoteServiceWorker
 * @package App\Service
 */
class RemoteServiceWorker implements RemoteServiceWorkerInterface
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var array */
    private array $cacheOptions;

    private const CACHE_OPTIONS_KEY = 'cache_options';

    private const CACHE_KEY_PREFIX = 'cacheKeyPrefix';

    private const CACHE_TTL = 'cacheTtl';

    /**
     * RemoteServiceWorker constructor.
     * @param LoggerInterface $logger
     * @param ParameterBagInterface $projectParameters
     *
     * @throws Exception
     */
    public function __construct(LoggerInterface $logger, ParameterBagInterface $projectParameters)
    {
        $this->logger = $logger;
        $this->setCacheOptions($projectParameters);
    }

    /**
     * @param ParameterBagInterface $projectParameters
     * @throws Exception
     */
    private function setCacheOptions(ParameterBagInterface $projectParameters): void
    {
        try {
            $this->cacheOptions = $projectParameters->get(self::CACHE_OPTIONS_KEY);

            if (!array_key_exists(self::CACHE_KEY_PREFIX, $this->cacheOptions) || !array_key_exists(self::CACHE_TTL, $this->cacheOptions)) {
                throw new Exception('Cache options are required.');
            }
        } catch (InvalidArgumentException $e) {
            $this->logger->error("__ERROR__  " . __METHOD__, [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param string $serviceName
     * @param string $serviceAddress
     *
     * @return array
     *
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws Exception
     */
    public function getServiceSettings(string $serviceName, string $serviceAddress): array
    {
        $cache = new FilesystemAdapter();

        $wholeName = $this->cacheOptions[self::CACHE_KEY_PREFIX] . $serviceName;

        return $cache->get($wholeName, function (ItemInterface $item) use ($serviceName, $serviceAddress) {
            $item->expiresAfter((int)$this->cacheOptions[self::CACHE_TTL]);

            $connectorObject = FabricServiceConnector::getConnectorObject($serviceName, $serviceAddress);
            if (!$connectorObject instanceof ServiceSettingsInterface) {
                throw new Exception("Service '{$serviceName}' does not exist.");
            }

            return $connectorObject->readSettings();
        });
    }

    /**
     * @param array $settingsArray
     * @param ServiceSettingsInterface $classConnector
     *
     * @return bool
     */
    public function validateServiceSettings(array $settingsArray, ServiceSettingsInterface $classConnector): bool
    {
        if (empty($settingsArray) || json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        return true;
    }

    /**
     * @param string $key
     */
    protected function clearCache(string $key): void
    {
        $cache = new FilesystemAdapter();
        $cache->clear($key);
    }

    /**
     * @param string $serviceName
     * @param string $serviceAddress
     * @param array $serviceSettingsArray
     * @return ResponseTransformerInterface
     */
    public function updateSettings(string $serviceName, string $serviceAddress, array $serviceSettingsArray): ResponseTransformerInterface
    {
        $connectorObject = FabricServiceConnector::getConnectorObject($serviceName, $serviceAddress);
        if (!$connectorObject instanceof ServiceSettingsInterface) {
            return new ResponseTransformer(
                ErrorResponse::code(ErrorResponse::ERROR_INVALID_SETTINGS),
                ErrorResponse::ERROR_INVALID_SETTINGS
            );
        }

        if (!$this->validateServiceSettings($serviceSettingsArray, $connectorObject)) {
            return new ResponseTransformer(
                ErrorResponse::code(ErrorResponse::ERROR_INVALID_SETTINGS),
                ErrorResponse::ERROR_INVALID_SETTINGS
            );
        }

        if (!$connectorObject->updateSettings($serviceSettingsArray)) {
            return new ResponseTransformer(
                ErrorResponse::code(ErrorResponse::ERROR_UPDATING_SETTINGS),
                ErrorResponse::ERROR_UPDATING_SETTINGS
            );
        }

        $this->clearCache($this->cacheOptions[self::CACHE_KEY_PREFIX] . $serviceName);

        return new ResponseTransformer(
            SuccessResponse::code(SuccessResponse::SUCCESS_UPDATING_SETTINGS),
            SuccessResponse::SUCCESS_UPDATING_SETTINGS
        );
    }
}
