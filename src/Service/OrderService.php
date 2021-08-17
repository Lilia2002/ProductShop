<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Product;

class OrderService
{
    public function calculateOrderTotal(Order $order)
    {
        $orderTotal = 0;

        foreach ($order->getOrderProducts() as $orderProduct) {

            switch ($orderProduct->getProduct()->getDiscount()) {
                case Product::DISCOUNT_SALE:
                    $orderTotal += $orderProduct->getProduct()->getPrice() * $orderProduct->getAmount() * (1 - Product::SALE_PERCENTAGE);
                    break;
                case Product::DISCOUNT_MORE_THAN_THREE:
                    if ($orderProduct->getAmount() >= 3) {
                        $orderTotal += $orderProduct->getProduct()->getPrice() * $orderProduct->getAmount() * (1 - Product::SALE_PERCENTAGE_OPT);
                    } else {
                        $orderTotal += $orderProduct->getProduct()->getPrice() * $orderProduct->getAmount();
                    }
                    break;
                case Product::DISCOUNT_TWO_FOR_ONE:
                    if ($orderProduct->getAmount() >= 2) {
                        $result = intdiv($orderProduct->getAmount(), 2);
                        if ($orderProduct->getAmount() % 2 != 0) {
                            $orderTotal += $orderProduct->getProduct()->getPrice() * $result + $orderProduct->getProduct()->getPrice();
                        } else {
                            $orderTotal += $orderProduct->getProduct()->getPrice() * $result;
                        }
                    } else {
                        $orderTotal += $orderProduct->getProduct()->getPrice() * $orderProduct->getAmount();
                    }
                    break;
                case Product::DISCOUNT_NO:
                default:
                    $orderTotal += $orderProduct->getProduct()->getPrice() * $orderProduct->getAmount();
                    break;
            }
        }
        $order->setTotal($orderTotal);
    }

}