<?php


namespace App\Provider;


use App\DTO\CurrencyRateResponseDto;

interface ExchangeProvider
{

    public function getResponse(string $from, string $to) : CurrencyRateResponseDto;

    public function isResponseValid(CurrencyRateResponseDto $currencyRateResponseDto): bool;

    public function setRate(CurrencyRateResponseDto $currencyRateResponseDto): CurrencyRateResponseDto;

}