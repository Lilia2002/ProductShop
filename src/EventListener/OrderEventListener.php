<?php


namespace App\EventListener;

use App\Entity\Order;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class OrderEventListener
{

    public function preUpdate(Order  $order, LifecycleEventArgs $args)
    {
        $order->setUpdatedAt();
    }

    public function prePersist(Order $order, LifecycleEventArgs $args)
    {
        $order->setCreatedAt();
    }
}