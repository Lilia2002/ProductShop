<?php


namespace App\Repository;


use App\Entity\PriceHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PriceHistoryRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceHistory::class);
    }

    /**
     * @param $productId
     * @return PriceHistory[]
     */
    public function findPricesInRange($productId): array
    {
        $qb = $this->createQueryBuilder('ph');

        $qb
            ->andWhere('ph.product = :product')
            ->setParameter('product', $productId)
            ->andWhere('ph.priceDate in (:dates)')
            ->setParameter('dates', $this->findDates($productId))
        ;

        return $qb->getQuery()->getResult();
    }

    private function findDates(int $productId)
    {
        $qb = $this->createQueryBuilder('phh');

        $qb
            ->select('MAX(phh.priceDate) as maxD, DATE(phh.priceDate) as priceD')
            ->andWhere('phh.product = :product')
            ->setParameter('product', $productId)
            ->groupBy('priceD')
        ;

        $result = [];

        foreach ($qb->getQuery()->getArrayResult() as $item) {
            $result[] = $item['maxD'];
        }

        return $result;
    }

//    'select max(price_date) as maxD, date(price_date) as HIDDEN pricD from price_history where product_id = 1 group by pricD;'

}