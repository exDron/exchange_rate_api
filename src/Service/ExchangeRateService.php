<?php

namespace App\Service;

use App\Entity\ExchangeRate;
use App\Service\DataProvider\ExchangeRateDataProviderInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class ExchangeRateService
{

    public function __construct(private ExchangeRateDataProviderInterface $exchangeRateDataProvider, private readonly EntityManagerInterface $entityManager)
    {
    }

    public function getRates(array $symbols = []): array
    {
        $rates = $this->exchangeRateDataProvider->getRates($symbols);

        return $rates;
    }

    public function saveRates(array $rates): void
    {
        foreach ($rates as $rate) {
            $exchangeRate = new ExchangeRate($rate['symbol'], $rate['price']);
            $this->entityManager->persist($exchangeRate);
        }

        $this->entityManager->flush();
    }
}
