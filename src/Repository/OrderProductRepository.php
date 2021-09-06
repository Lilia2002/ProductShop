<?php


namespace App\Repository;


use App\Entity\OrderProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderProduct::class);
    }

    public function orderProductDynamic(?int $productId)
    {
        $qb  = $this->createQueryBuilder('op');
        $qb
            ->select('SUM(op.amount) as amount, DATE(o.createdAt) as day')
            ->leftJoin('op.order', 'o')
            ->andWhere('op.product = :product')
            ->setParameter('product', $productId)
            ->groupBy('day')
        ;

        return $qb->getQuery()->getResult();
    }
}