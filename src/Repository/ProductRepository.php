<?php

namespace App\Repository;

use App\Entity\Order;
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

    public function findProductsLimited(int $limit = 3)
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->setMaxResults(3)
        ;

        return $qb->getQuery()->getResult();
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
}

