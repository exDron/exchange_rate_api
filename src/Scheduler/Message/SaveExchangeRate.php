<?php

namespace App\Scheduler\Message;

readonly class SaveExchangeRate
{
    public function __construct(private array $pairs)
    {
    }

    public function getPairs(): array
    {
        return $this->pairs;
    }
}
