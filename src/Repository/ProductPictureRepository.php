<?php

namespace App\Repository;

use App\Entity\ProductPicture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductPicture>
 *
 * @method ProductPicture|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductPicture|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductPicture[]    findAll()
 * @method ProductPicture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductPictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductPicture::class);
    }

    /**
     * @return ProductPicture[] Returns an array of ProductPicture objects
     */
    public function findAllByProductId($value): array
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'pro')
            ->innerJoin('p.product', 'pro')
            ->where('pro.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?ProductPicture
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
