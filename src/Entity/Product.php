<?php

namespace App\Entity;

use App\EventListener\ProductEventListener;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ORM\Table(name="products")
 * @ORM\EntityListeners({ProductEventListener::class})
 */
class Product
{
    const DISCOUNT_NO               = 0;
    const DISCOUNT_SALE             = 1;
    const DISCOUNT_TWO_FOR_ONE      = 2;
    const DISCOUNT_MORE_THAN_THREE  = 3;
    const SALE_PERCENTAGE           = 0.3;
    const SALE_PERCENTAGE_OPT       = 0.15;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $img;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @var int
     * @ORM\Column(type="smallint", nullable=true, length=1, nullable=true)
     */
    private $discount = self::DISCOUNT_NO;

    /**
     * @var Category|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="products", cascade={"persist"})
     */
    private $category;

    /**
     * @var OrderProduct[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\OrderProduct", mappedBy="product")
     */
    private $orderProducts;

    /**
     * @var PriceHistory[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\PriceHistory", mappedBy="product")
     */
    private $priceHistories;

    /**
     * @var ProductSpecification[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ProductSpecification", mappedBy="product", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $productSpecifications;

    public function __construct()
    {
        $this->orderProducts = new ArrayCollection();
        $this->priceHistories = new ArrayCollection();
        $this->productSpecifications = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Product
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * @param mixed $img
     * @return Product
     */
    public function setImg($img)
    {
        $this->img = $img;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     * @return Product
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    /**
     * @param int $discount
     * @return Product
     */
    public function setDiscount(int $discount): Product
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category|null $category
     * @return Product
     */
    public function setCategory(?Category $category): Product
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return OrderProduct[]|ArrayCollection
     */
    public function getOrderProducts()
    {
        return $this->orderProducts;
    }

    /**
     * @param OrderProduct[]|ArrayCollection $orderProducts
     * @return Product
     */
    public function setOrderProducts($orderProducts)
    {
        $this->orderProducts = $orderProducts;
        return $this;
    }

    /**
     * @return PriceHistory[]|ArrayCollection
     */
    public function getPriceHistories()
    {
        return $this->priceHistories;
    }

    /**
     * @param PriceHistory[]|ArrayCollection $priceHistories
     * @return Product
     */
    public function setPriceHistories($priceHistories)
    {
        $this->priceHistories = $priceHistories;
        return $this;
    }

    /**
     * @return ProductSpecification[]|ArrayCollection
     */
    public function getProductSpecifications()
    {
        return $this->productSpecifications;
    }

    /**
     * @param ProductSpecification[]|ArrayCollection $productSpecifications
     * @return Product
     */
    public function setProductSpecifications($productSpecifications)
    {
        $this->productSpecifications = $productSpecifications;
        return $this;
    }

    public function addProductSpecification(ProductSpecification $productSpecification): void
    {
        $productSpecification->setProduct($this);
        $this->productSpecifications->add($productSpecification);
    }

    public function removeProductSpecification(ProductSpecification $productSpecification): void
    {
        $this->productSpecifications->removeElement($productSpecification);
    }
}
