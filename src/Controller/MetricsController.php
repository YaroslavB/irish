<?php

declare(strict_types=1);

namespace App\Controller;

use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MetricsController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/metrics', name: 'app_metrics', methods: ['GET'])]
    public function metrics(): Response
    {
        $metrics = [];
        
        // Application info
        $metrics[] = '# HELP symfony_app_info Symfony application information';
        $metrics[] = '# TYPE symfony_app_info gauge';
        $metrics[] = 'symfony_app_info{app="irish",php_version="' . PHP_VERSION . '"} 1';
        
        // Database connection check
        $metrics[] = '# HELP symfony_database_connected Database connection status';
        $metrics[] = '# TYPE symfony_database_connected gauge';
        try {
            $this->entityManager->getConnection()->executeQuery('SELECT 1');
            $metrics[] = 'symfony_database_connected 1';
        } catch (Exception $e) {
            $metrics[] = 'symfony_database_connected 0';
        }
        
        // PHP memory usage
        $metrics[] = '# HELP php_memory_usage_bytes Current PHP memory usage';
        $metrics[] = '# TYPE php_memory_usage_bytes gauge';
        $metrics[] = 'php_memory_usage_bytes ' . memory_get_usage(true);
        
        // OPcache status
        if (function_exists('opcache_get_status')) {
            $opcacheStatus = @opcache_get_status(false);
            if ($opcacheStatus !== false) {
                $metrics[] = '# HELP php_opcache_hit_rate OPcache hit rate';
                $metrics[] = '# TYPE php_opcache_hit_rate gauge';
                $metrics[] = 'php_opcache_hit_rate ' . ($opcacheStatus['opcache_statistics']['opcache_hit_rate'] ?? 0);
            }
        }
        
        $response = new Response(implode("\n", $metrics) . "\n");
        $response->headers->set('Content-Type', 'text/plain; charset=utf-8');
        
        return $response;
    }
}
