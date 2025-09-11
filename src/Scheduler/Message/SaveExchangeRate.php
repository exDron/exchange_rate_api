<?php

declare(strict_types=1);

namespace App\Scheduler\Message;

readonly class SaveExchangeRate
{
    /**
     * @param string[] $pairs
     */
    public function __construct(private array $pairs)
    {
    }

    /**
     * @return string[]
     */
    public function getPairs(): array
    {
        return $this->pairs;
    }
}
