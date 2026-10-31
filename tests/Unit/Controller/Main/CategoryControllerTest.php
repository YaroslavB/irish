<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller\Main;

use App\Controller\Main\CategoryController;
use App\Entity\Category;
use App\Entity\Product;
use App\Repository\ProductRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryControllerTest extends TestCase
{
    public function testShowQueriesPublishedProductsOfCategoryForPage(): void
    {
        $category = new Category();
        $product = new Product();
        $repository = $this->createMock(ProductRepository::class);
        $repository->expects($this->once())
            ->method('findBy')
            ->with(
                ['category' => $category, 'isDeleted' => false, 'isPublished' => true],
                ['id' => 'DESC'],
                CategoryController::PER_PAGE + 1,
                CategoryController::PER_PAGE // page 2 offset
            )
            ->willReturn([$product]);

        $controller = $this->getMockBuilder(CategoryController::class)->onlyMethods(['render'])->getMock();
        $controller->expects($this->once())
            ->method('render')
            ->with('main/category/show.html.twig', [
                'category' => $category,
                'products' => [$product],
                'page'     => 2,
                'hasNext'  => false,
            ])
            ->willReturn(new Response());

        $controller->show($category, new Request(['page' => '2']), $repository);
    }

    public function testShowDetectsNextPageAndTrimsExtraRow(): void
    {
        $products = array_map(static fn () => new Product(), range(0, CategoryController::PER_PAGE));
        $repository = $this->createMock(ProductRepository::class);
        $repository->method('findBy')->willReturn($products);

        $controller = $this->getMockBuilder(CategoryController::class)->onlyMethods(['render'])->getMock();
        $controller->expects($this->once())
            ->method('render')
            ->with('main/category/show.html.twig', $this->callback(
                static fn (array $params): bool => $params['hasNext'] === true
                    && count($params['products']) === CategoryController::PER_PAGE
                    && $params['page'] === 1
            ))
            ->willReturn(new Response());

        $controller->show(new Category(), new Request(['page' => '-5']), $repository);
    }
}
