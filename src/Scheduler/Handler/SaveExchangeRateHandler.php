<?php

namespace App\Scheduler\Handler;

use App\Scheduler\Message\SaveExchangeRate;
use App\Service\ExchangeRateService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class SaveExchangeRateHandler
{
    public function __construct(private ExchangeRateService $exchangeRateService)
    {
    }

    public function __invoke(SaveExchangeRate $message): void
    {
        $this->exchangeRateService->saveRates($message->getCurrencies());
    }
}
