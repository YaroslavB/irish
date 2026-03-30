<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form\Handler;

use App\Entity\Category;
use App\Form\DTO\EditCategoryDto;
use App\Form\Handler\CategoryFormHandler;
use App\Utils\Manager\CategoryManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CategoryFormHandlerTest extends TestCase
{
    private CategoryFormHandler $handler;
    private MockObject&CategoryManager $categoryManager;

    protected function setUp(): void
    {
        $this->categoryManager = $this->createMock(CategoryManager::class);
        $this->handler = new CategoryFormHandler($this->categoryManager);
    }

    public function testProcessEditFormCreatesNewCategory(): void
    {
        $dto = new EditCategoryDto();
        $dto->title = 'New Category';

        $this->categoryManager
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Category::class));

        $result = $this->handler->processEditForm($dto);

        $this->assertInstanceOf(Category::class, $result);
    }

    public function testProcessEditFormUpdatesExistingCategory(): void
    {
        $existingCategory = new Category();
        $existingCategory->setTitle('Old Title');

        $dto = new EditCategoryDto();
        $dto->id = 42;
        $dto->title = 'Updated Title';

        $this->categoryManager
            ->expects($this->once())
            ->method('find')
            ->with(42)
            ->willReturn($existingCategory);

        $this->categoryManager
            ->expects($this->once())
            ->method('save')
            ->with($existingCategory);

        $result = $this->handler->processEditForm($dto);

        $this->assertSame($existingCategory, $result);
    }

    public function testProcessEditFormCreatesNewWhenNotFound(): void
    {
        $dto = new EditCategoryDto();
        $dto->id = 999;
        $dto->title = 'New Category';

        $this->categoryManager
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $this->categoryManager
            ->expects($this->once())
            ->method('save');

        $result = $this->handler->processEditForm($dto);

        $this->assertInstanceOf(Category::class, $result);
    }
}

