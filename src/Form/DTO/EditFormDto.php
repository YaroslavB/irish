<?php

declare(strict_types=1);

namespace App\Form\DTO;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class EditFormDto
{
    public ?int $id = null;

    #[Assert\NotBlank(message: 'Title is required')]
    public string $title = '';

    /** Decimal string (form uses NumberType input=string), never a float. */
    #[Assert\NotBlank(message: 'Price is required')]
    #[Assert\PositiveOrZero]
    public ?string $price = null;

    #[Assert\NotBlank(message: 'Quantity is required')]
    #[Assert\PositiveOrZero]
    public ?int $quantity = null;

    public ?string $description = null;

    #[Assert\NotBlank(message: 'Category is required')]
    public ?Category $category = null;

    #[Assert\File(maxSize: '5024k', mimeTypes: ['image/jpeg', 'image/png'], mimeTypesMessage: 'Please upload a valid image')]
    public ?UploadedFile $newImage = null;

    public bool $isPublished = false;

    public bool $isDeleted = false;

    public static function fromProduct(?Product $product): self
    {
        $dto = new self();
        if (!$product) {
            return $dto;
        }

        $dto->id = $product->getId();
        $dto->title = $product->getTitle() ?? '';
        $dto->price = $product->getPrice();
        $dto->quantity = $product->getQuantity() ?? 0;
        $dto->description = $product->getDescription();
        $dto->category = $product->getCategory();
        $dto->isPublished = $product->getIsPublished() ?? false;
        $dto->isDeleted = $product->getIsDeleted() ?? false;

        return $dto;
    }
}
