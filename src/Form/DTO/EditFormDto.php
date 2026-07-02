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
     * @var string
     */
    #[Assert\NotBlank(message: 'Title is required')]
    public $title;

    /**
     * @var string|null
     */
    #[Assert\NotBlank(message: 'Price is required')]
    #[Assert\GreaterThanOrEqual(value: '0')]
    public $price;

    /**
     * @var int
     */
    #[Assert\NotBlank(message: 'Quantity is required')]
    #[Assert\GreaterThan(value: '0')]
    public $quantity;

    /**
     * @var string|null
     */
    public ?string $description;


    /**
     * @var Category
     */
    #[Assert\NotBlank(message: 'Category is required')]
    public $category;

    /**
     * @var UploadedFile|null
     */
    #[Assert\File(maxSize: '5024k', mimeTypes: ['image/jpeg', 'image/png'], mimeTypesMessage: 'Please upload a valid image')]
    public $newImage;
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
        $dto->title = $product->getTitle() ?? '';
        $dto->price = $product->getPrice();
        $dto->quantity = $product->getQuantity() ?? 0;
        $dto->description = $product->getDescription();
        $dto->isPublished = $product->getIsPublished() ?? false;
        $dto->isDeleted = $product->getIsDeleted() ?? false;

        return $dto;
    }

}
