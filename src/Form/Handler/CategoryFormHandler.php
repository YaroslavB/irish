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

    /**
     * @param EditCategoryDto $editCategoryDto
     *
     * @return Category|mixed
     */
    public function processEditForm(EditCategoryDto $editCategoryDto)
    {
        $category = new Category();
        if ($editCategoryDto->id) {
            $category = $this->categoryManager->find($editCategoryDto->id);
        }
        $category->setTitle($editCategoryDto->title);
        $this->categoryManager->save($category);

        return $category;
    }

}
