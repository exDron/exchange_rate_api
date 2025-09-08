<?php

namespace App\Scheduler\Message;

readonly class SaveExchangeRate
{
    public function __construct(private array $currencies)
    {
    }

    public function getCurrencies(): array
    {
        return $this->currencies;
    }
}
