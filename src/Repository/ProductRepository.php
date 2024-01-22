<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Récupére tous les produit de la basse de donnée
     * @return Product[]
     */
    public function findAllProduct(): array
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p', 's', 'cat', 'ad', 'i')
            ->innerJoin('p.shop', 's')
            ->innerJoin('p.sousCategory', 'cat')
            ->leftJoin('s.shopValidation', 'ad')
            ->leftJoin('p.images', 'i')
            ->where('ad.visibility IS NOT NULL')
            ->andWhere('ad.decision = :val')
            ->setParameter('val', 'Publier')
            ->orderBy('p.id', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }
    
    /**
     * Récupére tous les produit de la basse de donnée
     * @return Product[]
     */
    public function findAllProductByUser($user): array
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p', 'i', 's', 'cat', 'ad')
            ->innerJoin('p.shop', 's')
            ->leftJoin('s.shopValidation', 'ad')
            ->innerJoin('p.images', 'i')
            ->innerJoin('p.sousCategory', 'cat')
            ->where('s.author = :author')
            ->setParameter('author', $user)
            ->orderBy('p.id', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

    /**
     * Récupérer un produit en fonction de l'id et du slug
     * @return Product[]
     */
    public function findOneProductById($productId, $productSlug): array
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p', 'i', 's', 'cat', 'ad')
            ->innerJoin('p.shop', 's')
            ->leftJoin('s.shopValidation', 'ad')
            ->innerJoin('p.images', 'i')
            ->innerJoin('p.sousCategory', 'cat')
            ->where('p.id = :id')
            ->andWhere('p.slug = :slug')
            ->setParameter('slug', $productSlug)
            ->setParameter('id', $productId)
        ;
        return $query->getQuery()->getResult();
    }

    /**
     * @return Product[]
     */
    public function findAllProductByCategory($categoryId): array
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p', 'i', 's', 'cat', 'c', 'ad')
            ->innerJoin('p.shop', 's')
            ->leftJoin('s.shopValidation', 'ad')
            ->innerJoin('s.city', 'c')
            ->innerJoin('p.sousCategory', 'cat')
            ->innerJoin('p.images', 'i')
            ->where('ad.visibility IS NOT NULL')
            ->andWhere('ad.decision = :val')
            ->setParameter('val', 'Publier')
            ->andwhere('p.sousCategory = :val1')
            ->setParameter('val1', $categoryId)
            ->orderBy('p.id', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

    /**
     * @return Product[]
     */
    public function findAllProductByCategoryAndCity($categorySlug, $cityId): array
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p', 'i', 's', 'cat', 'c', 'ad')
            ->innerJoin('p.shop', 's')
            ->innerJoin('s.shopValidation', 'ad')
            ->innerJoin('s.city', 'c')
            ->innerJoin('p.sousCategory', 'cat')
            ->innerJoin('p.images', 'i')
            ->Where('cat.slug = :slug')
            ->andWhere('c.id = :id')
            ->setParameter('slug', $categorySlug)
            ->setParameter('id', $cityId)
            ->orderBy('p.id', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

    /**
     * @return Product[]
     */
    public function findAllProductByShop($shopId): array
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p', 'i', 's', 'cat', 'c', 'ad')
            ->innerJoin('p.shop', 's')
            ->leftJoin('s.shopValidation', 'ad')
            ->innerJoin('s.city', 'c')
            ->innerJoin('p.sousCategory', 'cat')
            ->innerJoin('p.images', 'i')
            ->Where('s.id = :id')
            ->setParameter('id', $shopId)
            ->orderBy('p.id', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }
}
