<?php


namespace App\EventListener;


use App\Event\AddReviewOrChangeRatingEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class ProductEventSubscriber implements EventSubscriberInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AddReviewOrChangeRatingEvent::NAME => 'ratingProduct',
        ];
    }

    public function ratingProduct(AddReviewOrChangeRatingEvent $event)
    {
        $product = $event->getRatingProduct();

        $ratings = 0;

        foreach ($product->getReviews() as $review) {
            $ratings += $review->getRating();
        }

        $mediumRating = round($ratings / count($product->getReviews()), 1);

        $product->setRating($mediumRating);

        $this->entityManager->flush();
    }
}