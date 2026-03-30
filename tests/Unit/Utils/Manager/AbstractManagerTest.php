<?php

namespace App\Tests\Unit\Utils\Manager;

use App\Utils\Manager\AbstractManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AbstractManagerTest extends TestCase
{
    private MockObject&EntityManagerInterface $entityManager;
    private MockObject&ObjectRepository $repository;
    private AbstractManager $manager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(ObjectRepository::class);

        // Create a concrete implementation of the abstract class for testing
        $this->manager = new class($this->entityManager, $this->repository) extends AbstractManager {
            private ObjectRepository $repo;

            public function __construct(EntityManagerInterface $entityManager, ObjectRepository $repository)
            {
                parent::__construct($entityManager);
                $this->repo = $repository;
            }

            public function getRepository(): ObjectRepository
            {
                return $this->repo;
            }
        };
    }

    public function testSavePersistsAndFlushesEntity(): void
    {
        $entity = new \stdClass();

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($entity);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->manager->save($entity);
    }

    public function testRemoveRemovesAndFlushesEntity(): void
    {
        $entity = new \stdClass();

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($entity);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->manager->remove($entity);
    }

    public function testFindReturnsEntityFromRepository(): void
    {
        $entityId = 42;
        $expectedEntity = new \stdClass();

        $this->repository->expects($this->once())
            ->method('find')
            ->with($entityId)
            ->willReturn($expectedEntity);

        $result = $this->manager->find($entityId);

        $this->assertSame($expectedEntity, $result);
    }

    public function testFindReturnsNullWhenEntityNotFound(): void
    {
        $entityId = 999;

        $this->repository->expects($this->once())
            ->method('find')
            ->with($entityId)
            ->willReturn(null);

        $result = $this->manager->find($entityId);

        $this->assertNull($result);
    }

    public function testGetRepositoryReturnsRepository(): void
    {
        $result = $this->manager->getRepository();

        $this->assertSame($this->repository, $result);
    }

    public function testSaveAndRemoveCallsAreIndependent(): void
    {
        $entity1 = new \stdClass();
        $entity2 = new \stdClass();

        // Test that multiple saves work independently
        $this->entityManager->expects($this->exactly(2))
            ->method('persist')
            ->withConsecutive([$entity1], [$entity2]);

        $this->entityManager->expects($this->exactly(2))
            ->method('flush');

        $this->manager->save($entity1);
        $this->manager->save($entity2);
    }
}

