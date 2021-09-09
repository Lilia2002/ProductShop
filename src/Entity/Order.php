<?php


namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Table(name="orders")
 */
class Order
{
    const STATUS_BASKET     = 'basket';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SENT       = 'sent';
    const STATUS_COMPLETED  = 'completed';
    const STATUS_CANCELED   = 'canceled';


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $total;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clientInfo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $img;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $uniqueId;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $sentAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $completedAt;


    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="orders", cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $processedAt;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $status; //  - статус продукта "basket", "processing", "sent", "completed"
    // корзина, в обработке, отправлен, завершен

    /**
     * по умолчанию Ордер создается со статусом basket, при добавлении clientInfo и сохранении - переключаем в статус processing
     * и отправляем на рабочую почту письмо о том, что заказ принят в обработку
    */

    /**
     * @var OrderProduct[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\OrderProduct", mappedBy="order", cascade={"persist"})
     */
    private $orderProducts;

    public function __construct()
    {
        $this->orderProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getProcessedAt()
    {
        return $this->processedAt;
    }

    /**
     * @param mixed $processedAt
     * @return Order
     */
    public function setProcessedAt($processedAt)
    {
        $this->processedAt = $processedAt;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
    {
        $this->img = $img;
        return $this;
    }

    public function getSentAt(): ?\DateTime
    {
        return $this->sentAt;
    }


    public function setSentAt(?\DateTime $sentAt): self
    {
        $this->sentAt = $sentAt;
        return $this;
    }

    public function getCompletedAt(): ?\DateTime
    {
        return $this->completedAt;
    }


    public function setCompletedAt(?\DateTime $completedAt): self
    {
        $this->completedAt = $completedAt;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }


    public function setCreatedAt()
    {
        $this->createdAt = new DateTime();
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }


    public function setUpdatedAt()
    {
        $this->updatedAt = new DateTime();
    }

    public function getClientInfo(): ?string
    {
        return $this->clientInfo;
    }

    public function setClientInfo(?string $clientInfo): self
    {
        $this->clientInfo = $clientInfo;
        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(?int $total): self
    {
        $this->total = $total;
        return $this;
    }

    public function getUniqueId(): ?string
    {
        return $this->uniqueId;
    }

    public function setUniqueId(?string $uniqueId): self
    {
        $this->uniqueId = $uniqueId;
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
     * @return Order
     */
    public function setOrderProducts($orderProducts)
    {
        $this->orderProducts = $orderProducts;
        return $this;
    }

    public function addOrderProduct(OrderProduct $orderProduct)
    {
        $orderProduct->setOrder($this);
        $this->orderProducts->add($orderProduct);
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
     * @return Order
     */
    public function setUser(?User $user): Order
    {
        $this->user = $user;
        return $this;
    }
}