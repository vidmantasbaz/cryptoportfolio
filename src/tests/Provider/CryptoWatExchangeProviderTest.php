<?php


namespace App\Tests\Provider;


use App\Controller\AssetController;
use App\DTO\CurrencyRateResponseDto;
use App\Provider\CryptoWatExchangeProvider;
use App\Service\CurlRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\ItemInterface;

class CryptoWatExchangeProviderTest extends TestCase
{
    /** @test */
    public function getResponseTest()
    {

        $url = 'https://api.cryptowat.ch/markets/kraken/BTCUSD/price';
        $output = '{"BTC":{"value":"19","USD":180405},"Total":180405}';

        $currencyRateDto = new CurrencyRateResponseDto('USD', $output);
        $currencyRateDto->setResponse(json_decode($output, true));

        /** @var MockObject|AdapterInterface $cache */
        $cache = $this->createMock(AdapterInterface::class);

        /** @var MockObject|CurlRequest $curlRequest */
        $curlRequest = $this->createMock(CurlRequest::class);
        $curlRequest->expects($this->at(0))->method('setHandel')->with($url);
        $curlRequest->expects($this->at(1))->method('setOption');
        $curlRequest->expects($this->at(2))->method('setOption');
        $curlRequest->expects($this->at(3))->method('execute')->willReturn($output);
        $curlRequest->expects($this->at(3))->method('close');

        $provider = new CryptoWatExchangeProvider($cache, $curlRequest);
        $dtoTest = $provider->getResponse('BTC', 'USD');

        $this->assertSame($currencyRateDto->getRawResponse(), $dtoTest->getRawResponse());
        $this->assertSame($currencyRateDto->getCurrency(), $dtoTest->getCurrency());
        $this->assertSame($currencyRateDto->getResponse(), $dtoTest->getResponse());
    }

    /** @test */
    public function getRateTestShouldSetCache()
    {
        $output = '{"BTC":{"value":"19","USD":180405},"Total":180405}';

        $array = ['result' => ['price' => 100.0]];
        $currencyRateDto = new CurrencyRateResponseDto('USD', $output);
        $currencyRateDto->setResponse($array);

        /** @var MockObject|ItemInterface $cacheItem */
        $cacheItem = $this->createMock(ItemInterface::class);
        $cacheItem->expects($this->at(0))->method('isHit')->willReturn(false);
        $cacheItem->expects($this->at(1))->method('set')->with(100.0);
        $cacheItem->expects($this->at(2))->method('expiresAfter');
        $cacheItem->expects($this->at(3))->method('get')->willReturn(100.0);

        /** @var MockObject|AdapterInterface $cache */
        $cache = $this->createMock(AdapterInterface::class);
        $cache->expects($this->at(0))->method('getItem')->with('rate_USD')->willReturn($cacheItem);
        $cache->expects($this->at(1))->method('save')->with($cacheItem);
        /** @var MockObject|CurlRequest $curlRequest */
        $curlRequest = $this->createMock(CurlRequest::class);

        $provider = new CryptoWatExchangeProvider($cache, $curlRequest);
        $this->assertSame(100.0, $provider->getRate($currencyRateDto));;
    }

    /** @test */
    public function getRateTestShouldReturnFRomCacheIfCacheIsSet()
    {
        $output = '{"BTC":{"value":"19","USD":180405},"Total":180405}';

        $array = ['result' => ['price' => 100.0]];
        $currencyRateDto = new CurrencyRateResponseDto('USD', $output);
        $currencyRateDto->setResponse($array);

        /** @var MockObject|ItemInterface $cacheItem */
        $cacheItem = $this->createMock(ItemInterface::class);
        $cacheItem->expects($this->at(0))->method('isHit')->willReturn(true);
        $cacheItem->expects($this->at(1))->method('get')->willReturn(100.0);

        /** @var MockObject|AdapterInterface $cache */
        $cache = $this->createMock(AdapterInterface::class);
        $cache->expects($this->at(0))->method('getItem')->with('rate_USD')->willReturn($cacheItem);
        /** @var MockObject|CurlRequest $curlRequest */
        $curlRequest = $this->createMock(CurlRequest::class);

        $provider = new CryptoWatExchangeProvider($cache, $curlRequest);
        $this->assertSame(100.0, $provider->getRate($currencyRateDto));;
    }
}