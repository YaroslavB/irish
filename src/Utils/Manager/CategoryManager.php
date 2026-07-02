<?php

namespace App\Utils\Manager;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Persistence\ObjectRepository;

class CategoryManager extends AbstractManager
{
    public function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(Category::class);
    }

    public function remove(object $entity): void
    {
        /** @var Category $entity */
        $entity->setIsDeleted(true);

        /** @var Product $product */
        foreach ($entity->getProducts()->getValues() as $product) {
            $product->setIsDeleted(true);
        }
        $this->save($entity);
    }

}

