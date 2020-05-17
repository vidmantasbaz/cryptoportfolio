<?php


namespace App\Tests\Provider;


use App\Controller\AssetController;
use App\DTO\CurrencyRateResponseDto;
use App\Provider\CryptoWatExchangeProvider;
use App\Service\CurlRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;

class CryptoWatExchangeProviderTest extends TestCase
{
    /** @test */
    public function getResponseTest()
    {

        $url = 'https://api.cryptowat.ch/markets/kraken/BTCUSD/price';
        $output  = '{"BTC":{"value":"19","USD":180405},"Total":180405}';

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
}