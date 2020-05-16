<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Asset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Asset|null find($id, $lockMode = null, $lockVersion = null)
 * @method Asset|null findOneBy(array $criteria, array $orderBy = null)
 * @method Asset[]    findAll()
 * @method Asset[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Asset::class);
    }

    public function getAllValuesGroupedByCurrencies($id): array
    {
        return $this->createQueryBuilder('a')
            ->select('SUM(a.value) as value', 'a.currency')
            ->where('a.user = :id')->setParameter('id', $id)
            ->groupBy('a.currency')
            ->getQuery()
            ->getScalarResult();
    }

}
