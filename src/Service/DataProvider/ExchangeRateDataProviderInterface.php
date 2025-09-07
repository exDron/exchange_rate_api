<?php

namespace App\Service\DataProvider;

interface ExchangeRateDataProviderInterface
{
    public function getRates(array $symbols, array $intervals): array;
}
