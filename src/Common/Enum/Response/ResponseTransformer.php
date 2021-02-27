<?php

declare(strict_types=1);

namespace App\Common\Enum\Response;

/**
 * Class ResponseTransformer
 * @package App\Common\Enum\Response
 */
class ResponseTransformer implements ResponseTransformerInterface
{
    /** @var int */
    private int $code;

    /** @var string */
    private string $message;

    /**
     * ResponseTransformer constructor.
     * @param int $code
     * @param string $message
     */
    public function __construct(int $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function makeResponse(): array
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
        ];
    }
}
