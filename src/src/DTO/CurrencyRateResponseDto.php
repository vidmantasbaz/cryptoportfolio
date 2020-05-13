<?php


namespace App\DTO;


class CurrencyRateResponseDto
{
    /** @var string */
    private  $rawResponse;

    /** @var array|null */
    private  $response;


    public  function __construct(string $rawResponse)
    {
        $this->rawResponse = $rawResponse;
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
    public function setResponse(array $response = null ): void
    {
        $this->response = $response;
    }

}