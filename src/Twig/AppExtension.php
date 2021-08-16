<?php


namespace App\Twig;


use App\Entity\Order;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('statusToBadgeClass', [$this, 'statusToBadgeClass']),
        ];
    }


    public function statusToBadgeClass($status)
    {
        $statuses = [
            Order::STATUS_BASKET => 'badge-warning',
            Order::STATUS_COMPLETED => 'badge-success',
            Order::STATUS_PROCESSING => 'badge-primary',
            Order::STATUS_SENT => 'badge-info',
            Order::STATUS_CANCELED => 'badge-danger',

        ];
        return $statuses[$status];
    }
}