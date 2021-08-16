<?php


namespace App\EventListener;


use App\Entity\PriceHistory;
use App\Entity\Product;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ProductEventListener
{
    public function postUpdate(Product $product, LifecycleEventArgs $args)
    {
        $entityManager = $args->getObjectManager();
        $entity = $args->getObject();


        if ($entity instanceof Product) {
            /** @var PriceHistory $priceHistory */
            $currentPriceHistory = $product->getPriceHistories()->first();
             if (!$currentPriceHistory || ($currentPriceHistory->getPrice() !== $product->getPrice())) {
                $priceHistory = new PriceHistory();
                $priceHistory->setPriceDate(new \DateTime());
                $priceHistory->setPrice($entity->getPrice());
                $priceHistory->setProduct($product);

                $entityManager->persist($priceHistory);
                $entityManager->flush();
            }

        }
    }
}