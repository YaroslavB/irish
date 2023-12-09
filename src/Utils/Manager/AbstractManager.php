<?php

namespace App\Utils\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

abstract class AbstractManager
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return ObjectRepository
     */
    abstract public function getRepository(): ObjectRepository;


    /**
     * @param object $entity
     *
     * @return void
     */
    public function remove(object $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * @param object $entity
     */
    public function save(object $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * @param int $id
     *
     * @return mixed|object|null
     */
    public function find(int $id)
    {
        return $this->getRepository()->find($id);
    }

}
