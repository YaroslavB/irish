<?php

namespace App\Utils\Manager;

use App\Entity\Category;
use Doctrine\Persistence\ObjectRepository;

class CategoryManager extends AbstractManager
{

    public function getRepository(): ObjectRepository
    {
        $this->entityManager->getRepository(Category::class);
    }


}
