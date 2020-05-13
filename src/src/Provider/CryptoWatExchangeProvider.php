<?php

declare(strict_types=1);

namespace App\Provider;

use App\DTO\CurrencyRateResponseDto;

class CryptoWatExchangeProvider implements ExchangeProvider
{

    private const URL = 'https://api.cryptowat.ch/markets/kraken';
    private const PRICE_ENDPOINT = 'price';

    /**
     * @param string $from
     * @param string $to
     * @return CurrencyRateResponseDto
     */
    public function getResponse(string $from, string $to): CurrencyRateResponseDto
    {
        $pair = $from . $to;
        $url = $this->getEndpoint($pair, self::PRICE_ENDPOINT);
        $output = $this->request($url);
        $currencyRateDto = new CurrencyRateResponseDto($to, $output);
        $currencyRateDto->setResponse(json_decode($currencyRateDto->getRawResponse(), true));

        return $currencyRateDto;
    }

    public function isResponseValid(CurrencyRateResponseDto $currencyRateResponseDto): bool
    {
        $response = $currencyRateResponseDto->getResponse();

        if (is_array($response)
            && array_key_exists('result', $response)
            && array_key_exists('price', $response['result'])) {
            return true;
        }
        return false;
    }

    public function setRate(CurrencyRateResponseDto $currencyRateResponseDto): CurrencyRateResponseDto
    {
        $response = $currencyRateResponseDto->getResponse();
        $currencyRateResponseDto->setRate($response['result']['price']);

        return $currencyRateResponseDto;
    }

    /**
     * @param string $url
     * @return bool|string
     */
    private function request(string $url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $output = curl_exec($ch);

        curl_close($ch);

        return $output;
    }

    /**
     * @param string $pair
     * @param string $endpoint
     * @return string
     */
    private function getEndpoint(string $pair, string $endpoint): string
    {
        return sprintf(
            '%s/%s/%s',
            self::URL,
            $pair,
            $endpoint);
    }
}