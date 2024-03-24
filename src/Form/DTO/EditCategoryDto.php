<?php

namespace App\Form\DTO;

use App\Entity\Category;
use Symfony\Component\Validator\Constraints as Assert;

class EditCategoryDto
{


    /**
     * @var int|null
     */
    public ?int $id;

    /**
     * @Assert\NotBlank(message="Title is required")
     * @var string
     */
    public string $title;

    /**
     * @param Category|null $category
     *
     * @return self
     */
    public static function makeFromCategory(?Category $category): self
    {
        $dto = new self();
        if (!$category) {
            return $dto;
        }
        $dto->id = $category->getId();
        $dto->title = $category->getTitle();

        return $dto;
    }
}

