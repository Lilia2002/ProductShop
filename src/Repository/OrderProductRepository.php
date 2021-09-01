<?php


namespace App\Repository;


use App\Entity\OrderProduct;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderProduct::class);
    }

    public function orderProductDynamic(Product $product)
    {
        $qb  = $this->createQueryBuilder('op');
        $qb
            ->select('SUM(op.amount) as amount, DATE(o.createdAt) as day')
            ->leftJoin('op.order', 'o')
            ->andWhere('op.product = :product')
            ->setParameter('product', $product)
            ->groupBy('day')
        ;

        return $qb->getQuery()->getResult();
    }
}