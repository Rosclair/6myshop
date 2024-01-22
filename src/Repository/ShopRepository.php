<?php

namespace App\Repository;

use App\Entity\Shop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Shop>
 *
 * @method Shop|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shop|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shop[]    findAll()
 * @method Shop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shop::class);
    }

    /**
     * Permet de récupérer les 10 derniers boutiques pour le caroussel de l'acceuil
     * @return Shop[]
     */
    public function findAllShop(): array
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('s', 'p', 'c', 'cy', 'ad')
            ->leftJoin('s.shopValidation', 'ad')
            ->innerJoin('s.products', 'p')
            ->innerJoin('s.category', 'c')
            ->innerJoin('s.city', 'cy')
            ->where('ad IS NOT NULL')
            ->andWhere('ad.visibility = :blocked')
            ->setParameter('blocked', true)
            ->orderBy('s.id', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

    /**
     * Permet de récupérer les 10 derniers boutiques pour le caroussel de l'acceuil
     * @return Shop[]
     */
    public function findAllShopByUser($user): array
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('s', 'p', 'c', 'cy', 'a', 'ad')
            ->leftJoin('s.shopValidation', 'ad')
            ->leftJoin('s.products', 'p')
            ->innerJoin('s.category', 'c')
            ->innerJoin('s.city', 'cy')
            ->innerJoin('s.author', 'a')
            ->where('s.author = :author')
            ->setParameter('author', $user)
            ->orderBy('s.id', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }
    /**
     * Permet de recupérer toutes les boutique d'une même catégory
     * @return Shop[]
     */
    public function findAllShopByCat($shop_categorySlug): array
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('s', 'p', 'c', 'cy', 'a', 'ad')
            ->innerJoin('s.shopValidation', 'ad')
            ->innerJoin('s.products', 'p')
            ->innerJoin('s.category', 'c')
            ->innerJoin('s.city', 'cy')
            ->innerJoin('s.author', 'a')
            ->where('c.slug = :categorySlug')
            ->setParameter('categorySlug', $shop_categorySlug)
        ;
        return $query->getQuery()->getResult();
    }

    /**
     * Récupère un boutique en fonction de son slug
     * @return Shop[]
     */
    public function findOneShopBySlug($shopSlug): array
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('s', 'p', 'c', 'cy', 'ad')
            ->leftJoin('s.shopValidation', 'ad')
            ->leftJoin('s.products', 'p')
            ->innerJoin('s.category', 'c')
            ->innerJoin('s.city', 'cy')
            ->where('s.slug = :slug')
            ->setParameter('slug', $shopSlug)
        ;
        return $query->getQuery()->getResult();
    }    
    
    /**
    * Récupère les boutiques sans décision
    * @return Shop[]
    */
   public function findAllShopByShopValidationToValidate(): array
   {
       $query = $this
           ->createQueryBuilder('s')
           ->select('s', 'p', 'c', 'cy', 'ad')
           ->leftJoin('s.shopValidation', 'ad')
           ->leftJoin('s.products', 'p')
           ->innerJoin('s.category', 'c')
           ->innerJoin('s.city', 'cy')
           ->where('ad.id IS NULL')
       ;
       return $query->getQuery()->getResult();
   }

       /**
    * Récupère les boutiques sans décision
    * @return Shop[]
    */
    public function findAllShopByShopValidationDismiss(): array
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('s', 'p', 'c', 'cy', 'a', 'ad')
            ->leftJoin('s.shopValidation', 'ad')
            ->leftJoin('s.products', 'p')
            ->innerJoin('s.category', 'c')
            ->innerJoin('s.city', 'cy')
            ->innerJoin('s.author', 'a')
            ->where('ad IS NOT NULL')
            ->andWhere('ad.visibility = :blocked')
            ->setParameter('blocked', false)
        ;
        return $query->getQuery()->getResult();
    }
}
