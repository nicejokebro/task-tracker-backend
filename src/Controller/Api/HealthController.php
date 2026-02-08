<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class HealthController extends AbstractController
{
	#[Route('/api/health', name: 'api_health', methods: ['GET'])]
	public function healthCheck(Connection $connection): JsonResponse
	{
		try {
			$connection->executeQuery('SELECT 1');
			$dbStatus = 'ok';
		} catch (\Exception $e) {
			$dbStatus = 'error: ' . $e->getMessage();
		}

		return new JsonResponse([
			'status' => 'ok',
			'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
			'environment' => $this->getParameter('kernel.environment'),
			'php_version' => PHP_VERSION,
			'database' => $dbStatus,
			'xdebug' => extension_loaded('xdebug') ? phpversion('xdebug') : 'disabled',
		]);
	}
}