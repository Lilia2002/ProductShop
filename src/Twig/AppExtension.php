<?php


namespace App\Twig;


use App\Entity\Category;
use App\Entity\Order;
use App\Repository\ProductRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    /** @var ProductRepository  */
    private $productRepo;

    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('statusToBadgeClass', [$this, 'statusToBadgeClass']),
            new TwigFilter('categoryImage', [$this, 'getCategoryImage']),
        ];
    }


    public function statusToBadgeClass($status)
    {
        $statuses = [
            Order::STATUS_BASKET     => 'badge-warning',
            Order::STATUS_COMPLETED  => 'badge-success',
            Order::STATUS_PROCESSING => 'badge-primary',
            Order::STATUS_SENT       => 'badge-info',
            Order::STATUS_CANCELED   => 'badge-danger',

        ];
        return $statuses[$status];
    }

    public function getCategoryImage(Category $category)
    {
        $img = $this->productRepo->findMaxProductRating($category);

        $img = $img['img'] ?? null;

        return str_contains($img, 'http') ? $img : '/uploads/product/' . $img;
    }
}