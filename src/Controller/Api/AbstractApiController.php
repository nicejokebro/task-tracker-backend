<?php declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class AbstractApiController extends AbstractController
{
	protected function jsonValidationErrors(
		ConstraintViolationListInterface $errors,
		int $statusCode = 422
	): JsonResponse {
		$formattedErrors = [];
		foreach ($errors as $error) {
			$propertyPath = $error->getPropertyPath();
			$key = $propertyPath ?: 'global';
			$formattedErrors[$key][] = $error->getMessage();
		}

		return $this->json([
			'errors' => $formattedErrors
		], $statusCode);
	}
}