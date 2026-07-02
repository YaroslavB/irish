<?php

namespace App\Utils\Manager;

use App\Entity\User;
use Doctrine\Persistence\ObjectRepository;

class UserManager extends AbstractManager
{

    /**
     * @inheritDoc
     */
    public function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(User::class);
    }

    public function remove(object $entity): void
    {
        /** @var User $entity */
        $entity->setIsDeleted(true);
        $this->save($entity);
    }

}
