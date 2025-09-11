<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ExchangeRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExchangeRate>
 */
class ExchangeRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeRate::class);
    }

    /**
     * @return ExchangeRate[] Returns an array of ExchangeRate objects
     *
     * @throws \DateMalformedStringException
     */
    public function getLast24hRates(string $symbols): array
    {
        $last24H = new \DateTimeImmutable()->modify('-24 hours')->format('Y-m-d H:i:s');

        return $this->createQueryBuilder('er')
            ->andWhere('er.symbol = :symbols')
            ->andWhere('er.createdAt > :last24h')
            ->setParameter('symbols', $symbols)
            ->setParameter('last24h', $last24H)
            ->orderBy('er.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ExchangeRate[] Returns an array of ExchangeRate objects
     *
     * @throws \DateMalformedStringException
     */
    public function getSelectedDayRates(string $symbols, \DateTimeImmutable $day): array
    {
        $nextDay = $day->modify('+1 day')->format('Y-m-d H:i:s');

        return $this->createQueryBuilder('er')
            ->andWhere('er.symbol = :symbols')
            ->andWhere('er.createdAt > :day')
            ->andWhere('er.createdAt < :nextDay')
            ->setParameter('symbols', $symbols)
            ->setParameter('day', $day)
            ->setParameter('nextDay', $nextDay)
            ->orderBy('er.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
