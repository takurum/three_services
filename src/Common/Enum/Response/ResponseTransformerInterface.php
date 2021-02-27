<?php

declare(strict_types=1);

namespace App\Common\Enum\Response;

/**
 * Interface ResponseTransformerInterface
 * @package App\Common\Enum\Response
 */
interface ResponseTransformerInterface
{
    /**
     * @return array
     */
    public function makeResponse(): array;
}
