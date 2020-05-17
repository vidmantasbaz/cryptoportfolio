<?php

declare(strict_types=1);

namespace App\DTO;

class CurrencyRateResponseDto
{
    /** @var string */
    private $currency;

    /** @var string */
    private $rawResponse;

    /** @var array|null */
    private $response;


    public function __construct(string $currency, string $rawResponse)
    {
        $this->currency = $currency;
        $this->rawResponse = $rawResponse;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return array|null
     */
    public function getResponse(): ?array
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getRawResponse(): string
    {
        return $this->rawResponse;
    }

    /**
     * @param array|null $response
     */
    public function setResponse(array $response = null): void
    {
        $this->response = $response;
    }

}