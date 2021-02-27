<?php

declare(strict_types=1);

namespace App\Common\ServiceConnector\Connectors;

use App\Common\ServiceConnector\FieldValidators\FieldValidatorInterface;
use App\Common\ServiceConnector\FieldValidators\PostFieldValidator;
use Exception;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class PostServiceConnector
 * @package App\Common\ServiceConnector\Connectors
 */
final class PostServiceSettings implements ServiceSettingsInterface
{
    protected const REQUEST_METHOD = 'POST';

    /** @var string */
    private string $address;

    /** @var HttpClientInterface */
    private $client;

    /** @var array */
    protected array $serviceSettingsArray = [];

    /** @var FieldValidatorInterface */
    protected FieldValidatorInterface $validator;

    /**
     * GetServiceConnector constructor.
     * @param string $address
     * @param HttpClientInterface|null $client
     */
    public function __construct(string $address, HttpClientInterface $client = null)
    {
        $this->address = $address;
        $this->client = $client ?? new CurlHttpClient();

        $this->validator = new PostFieldValidator();
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    public function readSettings(): array
    {
        try {
            $response = $this->client->request(self::REQUEST_METHOD, $this->address);

            if (200 !== $response->getStatusCode()) {
                $message = sprintf(
                    'Connection with %s is failed. Status code is %d',
                    $this->address,
                    $response->getStatusCode()
                );

                throw new Exception($message);
            }

            $responseDecoded = json_decode($response->getContent(), true);
            if (empty($responseDecoded) || true !== $this->validator->validate($responseDecoded)) {
                $message = sprintf(
                    "Validation of settings from %s is failed.\nErrors: %s",
                    $this->address,
                    json_encode($this->validator->errors())
                );

                throw new Exception($message);
            }

            $this->serviceSettingsArray = $responseDecoded;
        } catch (TransportExceptionInterface | ServerExceptionInterface | RedirectionExceptionInterface | ClientExceptionInterface | Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

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
