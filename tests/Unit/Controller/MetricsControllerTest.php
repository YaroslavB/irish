<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use Exception;
use App\Controller\MetricsController;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class MetricsControllerTest extends TestCase
{
    private MetricsController $controller;
    private MockObject&EntityManagerInterface $entityManager;
    private MockObject&Connection $connection;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->connection = $this->createMock(Connection::class);
        
        $this->entityManager
            ->method('getConnection')
            ->willReturn($this->connection);

        $this->controller = new MetricsController($this->entityManager);
    }

    public function testMetricsReturnsPrometheusFormat(): void
    {
        $this->connection
            ->expects($this->once())
            ->method('executeQuery')
            ->with('SELECT 1');

        $response = $this->controller->metrics();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('text/plain; charset=utf-8', $response->headers->get('Content-Type'));
    }

    public function testMetricsContainsAppInfo(): void
    {
        $response = $this->controller->metrics();
        $content = $response->getContent();

        $this->assertStringContainsString('# HELP symfony_app_info', $content);
        $this->assertStringContainsString('# TYPE symfony_app_info gauge', $content);
        $this->assertStringContainsString('symfony_app_info{app="irish"', $content);
        $this->assertStringContainsString('php_version="' . PHP_VERSION . '"', $content);
    }

    public function testMetricsContainsDatabaseStatusWhenConnected(): void
    {
        $this->connection
            ->method('executeQuery')
            ->with('SELECT 1');

        $response = $this->controller->metrics();
        $content = $response->getContent();

        $this->assertStringContainsString('# HELP symfony_database_connected', $content);
        $this->assertStringContainsString('# TYPE symfony_database_connected gauge', $content);
        $this->assertStringContainsString('symfony_database_connected 1', $content);
    }

    public function testMetricsContainsDatabaseStatusWhenDisconnected(): void
    {
        $this->connection
            ->method('executeQuery')
            ->willThrowException(new Exception('Connection failed'));

        $response = $this->controller->metrics();
        $content = $response->getContent();

        $this->assertStringContainsString('symfony_database_connected 0', $content);
    }

    public function testMetricsContainsMemoryUsage(): void
    {
        $response = $this->controller->metrics();
        $content = $response->getContent();

        $this->assertStringContainsString('# HELP php_memory_usage_bytes', $content);
        $this->assertStringContainsString('# TYPE php_memory_usage_bytes gauge', $content);
        $this->assertMatchesRegularExpression('/php_memory_usage_bytes \d+/', $content);
    }

    public function testMetricsResponseEndsWithNewline(): void
    {
        $response = $this->controller->metrics();
        $content = $response->getContent();

        $this->assertStringEndsWith("\n", $content);
    }
}

