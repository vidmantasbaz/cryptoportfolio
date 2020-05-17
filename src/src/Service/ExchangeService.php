<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CurrencyRateResponseDto;
use App\Entity\Asset;
use App\Provider\ExchangeProvider;
use App\Service\Exceptions\ValidProviderNotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class ExchangeService
{
    /** @var  ExchangeProvider[] */
    private $exchangeProviders;

    /** @var EntityManagerInterface */
    private $em;

    /** @var AdapterInterface */
    private $cache;

    public function __construct(array $exchangeProviders, EntityManagerInterface $em, AdapterInterface $cache)
    {
        $this->exchangeProviders = $exchangeProviders;
        $this->em = $em;
        $this->cache = $cache;
    }

    /**
     * @param string $from
     * @param string $to
     * @return bool|float|mixed
     * @throws ValidProviderNotFound
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getExchangeRate(string $from, string $to)
    {
        if ($this->getRateFromCache($to)) {
            return $this->getRateFromCache($to);
        }

        foreach ($this->exchangeProviders as $provider) {
            $rateDto = $provider->getResponse($from, $to);
            if ($provider->isResponseValid($rateDto)) {
                return $provider->getRate($rateDto);
            }
        }

        $message = 'At this time we cant return  exchange rate for %s currency';
        throw new ValidProviderNotFound(sprintf($message, $to));
    }

    /**
     * @param int $id
     * @param string $currency
     * @return array
     * @throws ValidProviderNotFound
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getUserCurrencyValues(int $id, string $currency): array
    {
        $result = [];
        $total = 0;
        $values = $this->em->getRepository(Asset::class)->getAllValuesGroupedByCurrencies($id);
        foreach ($values as $key => $value) {
            $rate = $this->getExchangeRate($value['currency'], $currency);
            $result[$value['currency']]['value'] = $value['value'];
            $result[$value['currency']]['rate_'.strtolower($currency)] = round($rate, 2);
            $exchangeValue = round($value['value'] * $rate, 2);
            $result[$value['currency']][strtolower($currency)] = $exchangeValue;
            $total += $exchangeValue;
        }
        $result['Total'] = $total;
        return $result;
    }

    /**
     * @param string $currency
     * @return bool|mixed
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function getRateFromCache(string $currency)
    {
        $rate = $this->cache->getItem('rate_' . $currency);
        if (!$rate->isHit()) {
            return false;
        }

        return $rate->get();
    }

}