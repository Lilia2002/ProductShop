<?php


namespace App\Repository;


use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
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
    public function findOrdersSorted(string $fieldName = 'o.status', string $direction = 'ASC', ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('o');

            $qb
                ->addSelect('SUM(op.amount) as HIDDEN amount')
            ;

            if ($status) {
                $qb
                    ->andWhere('o.status = :status')
                    ->setParameter('status', $status)
                ;
            }

            $qb
                ->groupBy('o')
                ->leftJoin('o.orderProducts', 'op')
                ->addOrderBy($fieldName, $direction)
            ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Order[]|array
     */
    public function findUserOrdersSorted(string $fieldName = 'o.status', string $direction = 'ASC', ?User $user = null): array
    {
        $qb = $this->createQueryBuilder('o');

        $qb
            ->addSelect('SUM(op.amount) as HIDDEN amount')
            ->groupBy('o')
            ->leftJoin('o.orderProducts', 'op')
            ->addOrderBy($fieldName, $direction)
        ;

        if ($user) {
            $qb
                ->andWhere('o.user = :user')
                ->setParameter('user', $user)
            ;
        }

        return $qb->getQuery()->getResult();
    }

    public function findOrderWithOrderProducts($uniqueId)
    {
        $qb = $this->createQueryBuilder('o');  // === $qb->select('o')->from(Order::class, 'o');

        $qb
            ->addSelect('op')
            ->leftJoin('o.orderProducts', 'op')

            ->andWhere('o.uniqueId = :uniqueId')
            ->setParameter('uniqueId', $uniqueId)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function sumTotalOrderProducts()
    {
        $qb = $this->createQueryBuilder('o');

        $qb
            ->select('SUM(o.total) as total, ou.email')
            ->leftJoin('o.user', 'ou')
            ->groupBy('ou.email')
        ;

        return $qb->getQuery()->getResult();
    }
}

