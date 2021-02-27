<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\RemoteServiceWorkerInterface;
use App\Common\Enum\Response\ErrorResponse;
use App\Common\Enum\Response\ResponseTransformer;
use App\Common\Enum\ServiceName;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ServiceSettingsController
 * @package App\Controller
 */
final class ServiceSettingsController extends AbstractController
{
    private const SERVICES_ADDRESSES_KEY = 'addresses_of_services';

    /** @var HttpClientInterface */
    protected HttpClientInterface $httpClient;

    /** @var array */
    protected array $servicesAddresses;

    /** @var LoggerInterface */
    protected LoggerInterface $log;

    /** @var RemoteServiceWorkerInterface */
    private RemoteServiceWorkerInterface $rsw;

    /**
     * ServiceSettingsController constructor.
     *
     * @param HttpClientInterface $httpClient
     * @param LoggerInterface $logger
     * @param RemoteServiceWorkerInterface $remoteServiceWorker
     * @param ParameterBagInterface $projectParameters
     */
    public function __construct(
        HttpClientInterface $httpClient,
        LoggerInterface $logger,
        RemoteServiceWorkerInterface $remoteServiceWorker,
        ParameterBagInterface $projectParameters
    )
    {
        $this->httpClient = $httpClient;
        $this->log = $logger;
        $this->rsw = $remoteServiceWorker;

        $this->servicesAddresses = $projectParameters->get(self::SERVICES_ADDRESSES_KEY);
    }

    /**
     * @Route("/", methods={"GET"}, name="home")
     *
     * @return Response
     */
    public function index(): Response
    {
        $servicesSettings = [];

        foreach ($this->servicesAddresses as $serviceName => $serviceAddress) {
            try {
                $settings = $this->rsw->getServiceSettings($serviceName, $serviceAddress);
            } catch (Exception $e) {
                $this->log->error("__ERROR__ " . __METHOD__, [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'trace' => $e->getTraceAsString()
                ]);

                $settings = [$serviceName => ErrorResponse::FAILED_SERVICE_SETTINGS];
            }

            if (empty($settings)) {
                $settings = [$serviceName => ErrorResponse::FAILED_SERVICE_SETTINGS];
            }

            $servicesSettings[$serviceName] = json_encode($settings);
        }

        return $this->render('service_settings/index.html.twig', [
            'services_settings' => $servicesSettings,
        ]);
    }

    /**
     * @Route("/service-settings", methods={"PUT"}, name="service_settings_update")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateServiceSettings(Request $request): JsonResponse
    {
        $inputData = $request->toArray();

        $serviceName = $inputData['serviceName'] ?? null;
        $serviceSettingsArray = $inputData['config'] ?? null;

        if (null === $serviceName || null === $serviceSettingsArray || !ServiceName::isValidName($serviceName)) {
            $response = new ResponseTransformer(
                ErrorResponse::code(ErrorResponse::ERROR_INVALID_INPUT_PARAMETERS),
                ErrorResponse::ERROR_INVALID_INPUT_PARAMETERS
            );

            return $this->json($response->makeResponse());
        }

        $serviceAddress = $this->servicesAddresses[$serviceName] ?? null;
        if (null === $serviceAddress) {
            $response = new ResponseTransformer(
                ErrorResponse::code(ErrorResponse::ERROR_INVALID_INPUT_PARAMETERS),
                ErrorResponse::ERROR_INVALID_INPUT_PARAMETERS
            );

            return $this->json($response->makeResponse());
        }

        $updateResult = $this->rsw->updateSettings($serviceName, $serviceAddress, $serviceSettingsArray);

        return $this->json($updateResult->makeResponse());
    }
}
