<?php

namespace App\Repository;

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
    public function findProductsSorted(string $fieldName = 'p.name', string $direction = 'ASC'): array
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->orderBy($fieldName, $direction)
            ->leftJoin('p.category', 'c')
        ;

        return $qb->getQuery()->getResult();
    }

    public function findProductsLimited(int $limit = 3)
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->setMaxResults(3)
        ;

        return $qb->getQuery()->getResult();
    }
}
