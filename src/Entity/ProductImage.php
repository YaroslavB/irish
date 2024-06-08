<?php

namespace App\Entity;

use App\Repository\ProductImageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductImageRepository::class)
 */
class ProductImage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="productImages")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Product $product;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $fileNameBig;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $fileNameMiddle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $fileNameSmall;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getFileNameBig(): ?string
    {
        return $this->fileNameBig;
    }

    public function setFileNameBig(string $fileNameBig): self
    {
        $this->fileNameBig = $fileNameBig;

        return $this;
    }

    public function getFileNameMiddle(): ?string
    {
        return $this->fileNameMiddle;
    }

    public function setFileNameMiddle(string $fileNameMiddle): self
    {
        $this->fileNameMiddle = $fileNameMiddle;

        return $this;
    }

    public function getFileNameSmall(): ?string
    {
        return $this->fileNameSmall;
    }

    public function setFileNameSmall(string $fileNameSmall): self
    {
        $this->fileNameSmall = $fileNameSmall;

        return $this;
    }
}
