<?php


namespace App\Event;


use App\Entity\Product;
use Symfony\Contracts\EventDispatcher\Event;

class AddReviewOrChangeRatingEvent extends Event
{
    public const NAME = 'product.review';

    /**
     * @var Product
     */
    private $ratingProduct;

    /**
     * @param Product $ratingProduct
     */
    public function __construct(Product $ratingProduct)
    {
        $this->ratingProduct = $ratingProduct;
    }

    /**
     * @return Product
     */
    public function getRatingProduct(): Product
    {
        return $this->ratingProduct;
    }
}