<?php

namespace App\Form\DTO;

use App\Entity\Category;
use Symfony\Component\Validator\Constraints as Assert;

class EditCategoryDto
{
    public ?int $id = null;

    #[Assert\NotBlank(message: 'Title is required')]
    public string $title = '';

    public static function makeFromCategory(?Category $category): self
    {
        $dto = new self();
        if (!$category) {
            return $dto;
        }
        $dto->id = $category->getId();
        $dto->title = $category->getTitle() ?? '';

        return $dto;
    }
}

