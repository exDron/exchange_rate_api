<?php

declare(strict_types=1);

namespace App\Service\DataProvider;

interface ExchangeRateDataProviderInterface
{
    /**
     * @param string[] $symbols
     *
     * @return array<int, array{symbol: string, price: string}>
     */
    public function getRates(array $symbols): array;
}
