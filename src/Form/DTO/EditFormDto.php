<?php

namespace App\Form\DTO;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class EditFormDto
{
    /**
     * @var int|null
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Title is required")
     * @var string
     */
    public $title;

    /**
     * @Assert\NotBlank(message="Price is required")
     * @Assert\GreaterThanOrEqual(value="0")
     * @var string|null
     */
    public $price;

    /**
     * @Assert\NotBlank(message="Quantity is required")
     * @Assert\GreaterThan(value="0")
     * @var int
     */
    public $quantity;

    /**
     * @var string|null
     */
    public ?string $description;


    /**
     * @Assert\NotBlank(message="Category is required")
     * @var Category
     */
    public $category;

    /**
     * @Assert\File(maxSize="5024k",mimeTypes={image/jpeg, image/png}, mimeTypesMessage="Please upload a valid image")
     *
     * @var uploadedFile
     */
    public UploadedFile $newImage;
    /**
     * @var bool
     */
    public bool $isPublished;
    /**
     * @var  bool
     */
    public bool $isDeleted;

    /**
     *  Create a DTO object from a Product object
     *
     * @param Product|null $product
     *
     * @return self The created DTO object
     */
    public static function fromProduct(?Product $product): self
    {
        $dto = new self();
        // If no product is provided, return an empty DTO
        if (!$product) {
            return $dto;
        }
        // Set the properties of the DTO
        $dto->id = $product->getId();
        $dto->title = $product->getTitle();
        $dto->price = $product->getPrice();
        $dto->quantity = $product->getQuantity();
        $dto->description = $product->getDescription();
        $dto->isPublished = $product->isIsPublished();
        $dto->isDeleted = $product->isIsDeleted();

        return $dto;
    }

}
