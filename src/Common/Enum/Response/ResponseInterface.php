<?php

declare(strict_types=1);

namespace App\Common\Enum\Response;

/**
 * Interface ResponseInterface
 * @package App\Common\Enum\Response
 */
interface ResponseInterface
{
    /**
     * @param string $key
     * @return int|null
     */
    public static function code(string $key): ?int;
}
