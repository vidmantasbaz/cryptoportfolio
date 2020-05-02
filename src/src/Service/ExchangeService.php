<?php


namespace App\Service;

use App\DTO\CurrencyRateResponseDto;
use App\Provider\ExchangeProvider;
use App\Service\Exceptions\ValidProviderNotFound;

class ExchangeService
{

    /** @var  ExchangeProvider[] */
    private $exchangeProviders;

    public function __construct(array $exchangeProviders)
    {
        $this->exchangeProviders = $exchangeProviders;
    }

    /**
     * @param string $from
     * @param string $to
     * @return CurrencyRateResponseDto
     * @throws ValidProviderNotFound
     */
    public function getExchangeRate(string $from, string $to): CurrencyRateResponseDto
    {
        foreach ($this->exchangeProviders as $provider) {

            $rateDto = $provider->getExchangeRate($from, $to);
            if ($provider->isValid($rateDto)) {
                return $provider->setRate($rateDto);
            }
        }

        $message = 'At this time we cant return  exchange rate for %s currency';
        throw new ValidProviderNotFound(sprintf($message, $to));
    }
}