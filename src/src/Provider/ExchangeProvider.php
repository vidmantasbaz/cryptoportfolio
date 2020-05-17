<?php

declare(strict_types=1);

namespace App\Provider;

use App\DTO\CurrencyRateResponseDto;

interface ExchangeProvider
{

    public function getResponse(string $from, string $to) : CurrencyRateResponseDto;

    public function isResponseValid(CurrencyRateResponseDto $currencyRateResponseDto): bool;

    public function getRate(CurrencyRateResponseDto $currencyRateResponseDto): float;
}