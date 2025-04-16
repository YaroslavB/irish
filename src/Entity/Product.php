<?php

namespace App\Entity;


use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use DateTimeInterface;
use DateTimeImmutable;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiFilter(BooleanFilter::class, properties: ['isPublished'])]
#[ApiFilter(SearchFilter::class, properties: ['category' => 'exact'])]
#[ApiResource(
    collectionOperations: [
        'get'  => ['normalization_context' => ['groups' => ['product:read']]],
        'post' => [
            'normalization_context' => ['groups' => ['product:write']],
            'security'              => 'is_granted("ROLE_ADMIN")',
        ],
    ],
    itemOperations: [
        'get'   => ['normalization_context' => ['groups' => ['product:item']]],
        'patch' => [
            'normalization_context' => ['groups' => ['product:item:write']],
            'security'              => 'is_granted("ROLE_ADMIN")'
        ],
    ],
    attributes: [
        'pagination_client_items_per_page' => true,
        'format' => ['json','ld+json'],
    ],
    order: ['id' => 'DESC'],
    paginationEnabled: true,

)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: false)]
    #[Groups(['product:read', 'product:item'])]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    #[Groups(['product:read', 'product:item',])]
    #[ApiProperty(identifier: true)]
    private $uuid;

    /**
     * title of the product
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
        'product:read',
        'product:write',
        'product:item',
        'product:item:write',
    ])]
    private ?string $title = null;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
    #[Groups([
        'product:read',
        'product:write',
        'product:item',
        'product:item:write',
    ])]
    private ?string $price = null;

    #[ORM\Column(type: 'integer')]
    #[Groups([
        'product:read',
        'product:write',
        'product:item',
        'product:item:write',
    ])]
    private ?int $quantity = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['product:read', 'product:item'])]
    private DateTimeInterface $createdAt;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups([
        'product:read',
        'product:write',
        'product:item',
        'product:item:write',
    ])]
    private ?string $description = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['product:write'])]
    private bool $isPublished;

    #[ORM\Column(type: 'boolean')]
    private bool $isDeleted;

    /**
     * @var Collection<int, ProductImage>
     */
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductImage::class, cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['product:read', 'product:item'])]
    private Collection $productImages;

    #[Gedmo\Slug(fields: ["title"])]
    #[ORM\Column(type: 'string', length: 128, unique: true, nullable: true)]
    private ?string $slug = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[Groups([
        'product:read',
        'product:write',
        'product:item',
        'product:item:write',
    ])]
    private ?Category $category = null;

    /**
     * @var Collection<int, CartProduct>
     */
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: CartProduct::class, orphanRemoval: true)]
    private Collection $cartProducts;

    /**
     * @var Collection<int, OrderProduct>
     */
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: OrderProduct::class)]
    private Collection $orderProducts;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
        $this->createdAt = new DateTimeImmutable();
        $this->isPublished = false;
        $this->isDeleted = false;
        $this->productImages = new ArrayCollection();
        $this->cartProducts = new ArrayCollection();
        $this->orderProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * @return Collection|ProductImage[]
     */
    public function getProductImages(): Collection
    {
        return $this->productImages;
    }

    public function addProductImage(ProductImage $productImage): self
    {
        if (!$this->productImages->contains($productImage)) {
            $this->productImages[] = $productImage;
            $productImage->setProduct($this);
        }

        return $this;
    }

    public function removeProductImage(ProductImage $productImage): self
    {
        // set the owning side to null (unless already changed)
        if ($this->productImages->removeElement($productImage)
            && $productImage->getProduct() === $this
        ) {
            $productImage->setProduct(null);
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|CartProduct[]
     */
    public function getCartProducts(): Collection
    {
        return $this->cartProducts;
    }

    public function addCartProduct(CartProduct $cartProduct): self
    {
        if (!$this->cartProducts->contains($cartProduct)) {
            $this->cartProducts[] = $cartProduct;
            $cartProduct->setProduct($this);
        }

        return $this;
    }

    public function removeCartProduct(CartProduct $cartProduct): self
    {
        // set the owning side to null (unless already changed)
        if ($this->cartProducts->removeElement($cartProduct)
            && $cartProduct->getProduct() === $this
        ) {
            $cartProduct->setProduct(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderProduct>
     */
    public function getOrderProducts(): Collection
    {
        return $this->orderProducts;
    }

    public function addOrderProduct(OrderProduct $orderProduct): self
    {
        if (!$this->orderProducts->contains($orderProduct)) {
            $this->orderProducts[] = $orderProduct;
            $orderProduct->setProduct($this);
        }

        return $this;
    }

    public function removeOrderProduct(OrderProduct $orderProduct): self
    {
        // set the owning side to null (unless already changed)
        if ($this->orderProducts->removeElement($orderProduct)
            && $orderProduct->getProduct() === $this
        ) {
            $orderProduct->setProduct(null);
        }

        return $this;
    }


}