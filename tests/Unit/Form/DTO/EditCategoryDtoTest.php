<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form\DTO;

use App\Entity\Category;
use App\Form\DTO\EditCategoryDto;
use PHPUnit\Framework\TestCase;

class EditCategoryDtoTest extends TestCase
{
    public function testMakeFromCategoryWithNull(): void
    {
        $dto = EditCategoryDto::makeFromCategory(null);

        $this->assertNull($dto->id);
        $this->assertEquals('', $dto->title);
    }

    public function testMakeFromCategoryWithCategory(): void
    {
        $category = $this->createMock(Category::class);
        $category->method('getId')->willReturn(42);
        $category->method('getTitle')->willReturn('Test Category');

        $dto = EditCategoryDto::makeFromCategory($category);

        $this->assertEquals(42, $dto->id);
        $this->assertEquals('Test Category', $dto->title);
    }

    public function testMakeFromCategoryWithNullTitle(): void
    {
        $category = $this->createMock(Category::class);
        $category->method('getId')->willReturn(1);
        $category->method('getTitle')->willReturn(null);

        $dto = EditCategoryDto::makeFromCategory($category);

        $this->assertEquals(1, $dto->id);
        $this->assertEquals('', $dto->title);
    }

    public function testDefaultValues(): void
    {
        $dto = new EditCategoryDto();

        $this->assertNull($dto->id);
        $this->assertEquals('', $dto->title);
    }
}

