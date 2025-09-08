<?php

namespace App\Repository;

use App\Entity\ExchangeRate;
use DateMalformedStringException;
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
     * @throws DateMalformedStringException
     */
    public function getLast24hRates(array $symbols): array
    {
        $last24H = new \DateTimeImmutable()->modify('-24 hours')->format('Y-m-d H:i:s');
        return $this->createQueryBuilder('er')
            ->andWhere('er.symbol IN (:symbols)')
            ->andWhere('er.createdAt > :last24h')
            ->setParameter('symbols', $symbols)
            ->setParameter('last24h', $last24H)
            ->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?ExchangeRate
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
