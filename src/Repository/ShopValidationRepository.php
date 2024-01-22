<?php

namespace App\Repository;

use App\Entity\ShopValidation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShopValidation>
 *
 * @method ShopValidation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShopValidation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShopValidation[]    findAll()
 * @method ShopValidation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopValidationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShopValidation::class);
    }

//    /**
//     * @return ShopValidation[] Returns an array of ShopValidation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ShopValidation
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
