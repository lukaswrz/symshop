<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BasketItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BasketItem>
 *
 * @method BasketItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method BasketItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method BasketItem[]    findAll()
 * @method BasketItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BasketItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BasketItem::class);
    }

    //    /**
    //     * @return BasketItem[] Returns an array of BasketItem objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?BasketItem
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
