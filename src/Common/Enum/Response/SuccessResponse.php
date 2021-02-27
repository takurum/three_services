<?php

declare(strict_types=1);

namespace App\Common\Enum\Response;

/**
 * Class SuccessResponse
 * @package App\Common\Enum
 */
class SuccessResponse implements ResponseInterface
{
    public const SUCCESS_UPDATING_SETTINGS = 'The update was successful';

    private const CODES = [
        self::SUCCESS_UPDATING_SETTINGS => 200,
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
