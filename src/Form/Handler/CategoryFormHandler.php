<?php

namespace App\Form\Handler;

use App\Entity\Category;
use App\Form\DTO\EditCategoryDto;
use App\Utils\Manager\CategoryManager;

class CategoryFormHandler
{
    private CategoryManager $categoryManager;

    public function __construct(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    public function processEditForm(EditCategoryDto $editCategoryDto): Category
    {
        $category = new Category();
        if ($editCategoryDto->id !== null) {
            /** @var Category|null $existingCategory */
            $existingCategory = $this->categoryManager->find($editCategoryDto->id);
            if ($existingCategory) {
                $category = $existingCategory;
            }
        }
        $category->setTitle($editCategoryDto->title);
        $this->categoryManager->save($category);

        return $category;
    }

}
