<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
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
     * @return Product[]|array
     */
    public function findProductsSearch(?string $query, string $fieldName = 'p.name', string $direction = 'ASC'): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($query) {
            $qb
                ->orWhere('p.name        LIKE :query')
                ->orWhere('p.description LIKE :query')
                ->orWhere('c.name        LIKE :query')

                ->setParameter('query', '%'.$query.'%')
            ;
        }

        $qb
            ->leftJoin('p.category', 'c')
            ->orderBy($fieldName, $direction)
        ;

        return $qb->getQuery()->getResult();
    }

    public function findProductsSearchLimit(?string $query): array
    {
        $qb = $this->createQueryBuilder('p');

            if ($query) {
                $qb
                    ->leftJoin('p.category', 'c')
                    ->orWhere('p.name LIKE :query')
                    ->orWhere('p.description LIKE :query')
                    ->orWhere('c.name LIKE :query')
                    ->setMaxResults(15)
                    ->setParameter('query', '%'.$query.'%')
                ;
            }

        return $qb->getQuery()->getResult();
    }

    public function findMaxProductRating(Category $category)
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->select('p.img')
            ->andWhere('p.category = :category')
            ->setParameter('category', $category)
            ->andWhere('p.img IS NOT NULL')

            ->orderBy('p.rating', 'DESC')
            ->setMaxResults(1)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findProductRating()
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->select('p')
            ->andWhere('p.img IS NOT NULL')

            ->orderBy('p.rating', 'DESC')
            ->setMaxResults(6)
        ;

        return $qb->getQuery()->getResult();
    }
}

