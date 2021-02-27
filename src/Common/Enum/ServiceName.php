<?php

declare(strict_types=1);

namespace App\Common\Enum;

/**
 * Class ServiceName
 * @package App\Common\Enum
 */
abstract class ServiceName
{
    public const GET_SERVICE = 'GetService';

    public const POST_SERVICE = 'PostService';

    public const GRPC_SERVICE = 'GrpcService';

    /**
     * @param string $inputName
     *
     * @return bool
     */
    public static function isValidName(string $inputName): bool
    {
        switch ($inputName) {
            case self::GET_SERVICE:
            case self::POST_SERVICE:
            case self::GRPC_SERVICE:
                return true;
            default:
                return false;
        }
    }
}
