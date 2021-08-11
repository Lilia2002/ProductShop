<?php


namespace App\Repository;


namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }


    /**
     * @return Order[]|array
     */
    public function findOrdersSorted(string $fieldName = 'o.status', string $direction = 'ASC', string $status = 'basket'): array
    {
        $qb = $this->createQueryBuilder('o');

        $qb

            ->addSelect('SUM(op.amount) as HIDDEN amount')
            ->andWhere('o.status = :status')
            ->setParameter('status', $status)
            ->groupBy('o')
            ->leftJoin('o.orderProducts', 'op')
            ->addOrderBy($fieldName, $direction)
        ;

        return $qb->getQuery()->getResult();
    }
}

