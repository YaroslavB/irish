<?php
declare(strict_types=1);
namespace App\Tests\Unit\Controller\Main;
use App\Controller\Main\CategoryController;
use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
class CategoryControllerTest extends TestCase
{
    public function testShowRendersCategory(): void
    {
        $product = new Product();
        $product->setTitle('Test Product');
        $category = $this->createMock(Category::class);
        $category->method('getProducts')
            ->willReturn(new ArrayCollection([$product]));
        $controller = $this->getMockBuilder(CategoryController::class)
            ->onlyMethods(['render'])
            ->getMock();
        $controller->expects($this->once())
            ->method('render')
            ->with(
                'main/category/show.html.twig',
                $this->callback(function ($params) use ($category, $product) {
                    return $params['category'] === $category 
                        && $params['products'] === [$product];
                })
            )
            ->willReturn(new Response());
        $controller->show($category);
    }
    public function testShowWithEmptyProducts(): void
    {
        $category = $this->createMock(Category::class);
        $category->method('getProducts')
            ->willReturn(new ArrayCollection([]));
        $controller = $this->getMockBuilder(CategoryController::class)
            ->onlyMethods(['render'])
            ->getMock();
        $controller->expects($this->once())
            ->method('render')
            ->with(
                'main/category/show.html.twig',
                ['category' => $category, 'products' => []]
            )
            ->willReturn(new Response());
        $controller->show($category);
    }
}
