<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CartRepository::class)
 */
class Cart
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sessionId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAd;

    /**
     * @ORM\OneToMany(targetEntity=CartProduct::class, mappedBy="cart", orphanRemoval=true)
     */
    private $product;

    public function __construct()
    {
        $this->product = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): self
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function getCreatedAd(): ?\DateTimeInterface
    {
        return $this->createdAd;
    }

    public function setCreatedAd(\DateTimeInterface $createdAd): self
    {
        $this->createdAd = $createdAd;

        return $this;
    }

    /**
     * @return Collection<int, CartProduct>
     */
    public function getProduct(): Collection
    {
        return $this->product;
    }

    public function addProduct(CartProduct $product): self
    {
        if (!$this->product->contains($product)) {
            $this->product[] = $product;
            $product->setCart($this);
        }

        return $this;
    }

    public function removeProduct(CartProduct $product): self
    {
        // set the owning side to null (unless already changed)
        if ($this->product->removeElement($product)
            && $product->getCart() === $this
        ) {
            $product->setCart(null);
        }

        return $this;
    }
}
