<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PriceHistoryRepository")
 * @ORM\Table(name="price_history")
 */
class PriceHistory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Product|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="priceHistories", cascade={"persist"})
     */
    private $product;

    /**
     * @ORM\Column(type="datetime")
     */
    private $priceDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return PriceHistory
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
     * @return PriceHistory
     */
    public function setProduct(?Product $product): PriceHistory
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPriceDate()
    {
        return $this->priceDate;
    }

    /**
     * @param mixed $priceDate
     * @return PriceHistory
     */
    public function setPriceDate($priceDate)
    {
        $this->priceDate = $priceDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     * @return PriceHistory
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }






}