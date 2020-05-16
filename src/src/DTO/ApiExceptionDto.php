<?php

declare(strict_types=1);

namespace App\DTO;

class ApiExceptionDto
{

    /** @var int */
    private $statusCode;

    /** @var string */
    private $message;

    /** @var array */
    private $errors;

    /**
     * ApiExceptionDto constructor.
     * @param string $statusCode
     * @param string $message
     * @param array $errors
     */
    public function __construct(string $statusCode, string $message, array $errors = [])
    {
        $this->statusCode = $statusCode;
        $this->message = $message;
        $this->errors = $errors;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            $this->statusCode,
            $this->message,
            $this->errors
        ];

    }
}