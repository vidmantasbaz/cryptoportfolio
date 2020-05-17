<?php

declare(strict_types=1);

namespace App\Provider;

use App\DTO\CurrencyRateResponseDto;
use App\Service\CurlRequest;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class CryptoWatExchangeProvider implements ExchangeProvider
{

    private const URL = 'https://api.cryptowat.ch/markets/kraken';
    private const PRICE_ENDPOINT = 'price';

    /** @var AdapterInterface */
    private $cache;

    /** @var CurlRequest */
    private $curlRequest;

    public function __construct(AdapterInterface $cache, CurlRequest $curlRequest)
    {
        $this->cache = $cache;
        $this->curlRequest = $curlRequest;
    }

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

    /**
     * @param CurrencyRateResponseDto $currencyRateResponseDto
     * @return float
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getRate(CurrencyRateResponseDto $currencyRateResponseDto): float
    {
        $response = $currencyRateResponseDto->getResponse();

        $rate = $this->cache->getItem('rate_' . $currencyRateResponseDto->getCurrency());
        if (!$rate->isHit()) {
            $rate->set($response['result']['price']);
            $rate->expiresAfter(\DateInterval::createFromDateString('1 hour'));
            $this->cache->save($rate);
        }
        return $response['result']['price'];
    }

    /**
     * @param string $url
     * @return bool|string
     */
    private function request(string $url)
    {
        $this->curlRequest->setHandel($url);
        $this->curlRequest->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->curlRequest->setOption(CURLOPT_FOLLOWLOCATION, true);
        $output = $this->curlRequest->execute();

        $this->curlRequest->close();

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