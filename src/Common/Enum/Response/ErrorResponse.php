<?php

declare(strict_types=1);

namespace App\Common\Enum\Response;

/**
 * Class Response
 * @package App\Common\Enum
 */
final class ErrorResponse implements ResponseInterface
{
    public const ERROR_INVALID_INPUT_PARAMETERS = 'Invalid input parameters';

    public const ERROR_INVALID_SETTINGS = 'Invalid service settings';

    public const ERROR_UPDATING_SETTINGS = 'The update is failed';

    public const FAILED_SERVICE_SETTINGS = 'Getting settings is failed';

    private const CODES = [
        self::ERROR_INVALID_INPUT_PARAMETERS => 400,
        self::ERROR_INVALID_SETTINGS => 400,
        self::ERROR_UPDATING_SETTINGS => 400,
    ];

    /**
     * @param string $key
     *
     * @return int|null
     */
    public static function code(string $key): ?int
    {
        return self::CODES[$key] ?? null;
    }
}
