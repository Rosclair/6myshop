<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * Récupérer les categories pour la nav
     * @return Category[] Returns an array of Category objects
     */
    public function findAll(): array
    {
        $query = $this
            ->createQueryBuilder('c')
            ->select('c', 'sc')
            ->innerJoin('c.category', 'sc')
            ->where('c.parent IS NULL')
            ->orderBy('c.name', 'ASC')
        ;
        return $query->getQuery()->getResult();
    }

    /**
     * @return Category[] Returns an array of SousCategory objects
     */
    public function findSousCatInCat($categoryId): array
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.parent = :id')
            ->setParameter('id', $categoryId)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
