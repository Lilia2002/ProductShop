<?php


namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Review
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", length=30000, nullable=true)
     */
    private $review;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rating;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reviews", cascade={"persist"})
     */
    private $user;

    /**
     * @var Product|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="reviews", cascade={"persist"})
     */
    private $product;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Review
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product|null $product
     * @return Review
     */
    public function setProduct(?Product $product): Review
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * @param mixed $review
     * @return Review
     */
    public function setReview($review)
    {
        $this->review = $review;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param mixed $rating
     * @return Review
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return Review
     */
    public function setUser(?User $user): Review
    {
        $this->user = $user;
        return $this;
    }
}