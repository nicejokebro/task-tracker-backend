<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\Request\Project\ProjectCreateRequestDTO;
use App\Service\ProjectService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
#[Route('/api/projects', name: 'api_projects_')]
class ProjectController extends AbstractApiController
{
	public function __construct(
		private readonly ProjectService $projectService,
		private readonly SerializerInterface $serializer,
		private readonly ValidatorInterface $validator
	) {}

	#[Route('', methods: ['GET'])]
	public function index(): JsonResponse
	{
		$projects = $this->projectService->findAll();
		return $this->json($projects, 200, [], [
			'groups' => ['project:read']
		]);
	}

	#[Route('', methods: ['POST'])]
	public function create(Request $request): JsonResponse
	{
		$data = json_decode($request->getContent(), true);

		$dto = $this->serializer->denormalize(
			$data,
			ProjectCreateRequestDTO::class,
			'json'
		);

		$errors = $this->validator->validate($dto);
		if (count($errors) > 0) {
			return $this->jsonValidationErrors($errors, 422);
		}

		$project = $this->projectService->create($dto->name, $dto->description);

		return $this->json($project, 201, [], [
			'groups' => ['project:read']
		]);
	}

	#[Route('/{id}', methods: ['GET'])]
	public function show(int $id): JsonResponse
	{
		$project = $this->projectService->find($id);

		if (!$project) {
			return $this->json(['error' => 'Project not found'], 404);
		}

		return $this->json($project, 200, [], [
			'groups' => ['project:read']
		]);
	}

	#[Route('/{id}', methods: ['PATCH'])]
	public function update(int $id, Request $request): JsonResponse
	{
		$data = json_decode($request->getContent(), true);

		$dto = $this->serializer->denormalize(
			$data,
			ProjectCreateRequestDTO::class,
			'json'
		);

		$errors = $this->validator->validate($dto);
		if (count($errors) > 0) {
			return $this->jsonValidationErrors($errors, 422);
		}

		$project = $this->projectService->update($id, $dto->name, $dto->description);

		if (!$project) {
			return $this->json(['error' => 'Project not found'], 404);
		}

		return $this->json($project, 200, [], [
			'groups' => ['project:read']
		]);
	}

	#[Route('/{id}', methods: ['DELETE'])]
	public function delete(int $id): JsonResponse
	{
		$deleted = $this->projectService->delete($id);

		if (!$deleted) {
			return $this->json(['error' => 'Project not found'], 404);
		}

		return $this->json(null, 204);
	}
}