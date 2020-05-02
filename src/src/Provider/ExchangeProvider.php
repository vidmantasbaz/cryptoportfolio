<?php


namespace App\Provider;


use App\DTO\CurrencyRateResponseDto;

interface ExchangeProvider
{

    public function getExchangeRate(string $from, string $to) : CurrencyRateResponseDto;

    public function isValid(CurrencyRateResponseDto $currencyRateResponseDto): bool;

    public function setRate(CurrencyRateResponseDto $currencyRateResponseDto): CurrencyRateResponseDto;

}