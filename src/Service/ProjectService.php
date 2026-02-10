<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class ProjectService
{
	public function __construct(
		private EntityManagerInterface $entityManager,
		private ProjectRepository $repository
	) {}

	public function create(string $name, ?string $description = null): Project
	{
		$project = new Project();
		$project->setName($name);
		$project->setDescription($description);

		$this->entityManager->persist($project);
		$this->entityManager->flush();

		return $project;
	}

	public function update(int $id, string $name, ?string $description = null): ?Project
	{
		$project = $this->repository->find($id);
		if (!$project) {
			return null;
		}

		$project->setName($name);
		$project->setDescription($description);

		$this->entityManager->flush();

		return $project;
	}

	public function delete(int $id): bool
	{
		$project = $this->repository->find($id);
		if (!$project) {
			return false;
		}

		$this->entityManager->remove($project);
		$this->entityManager->flush();

		return true;
	}

	public function findAll(): array
	{
		return $this->repository->findAll();
	}

	public function find(int $id): ?Project
	{
		return $this->repository->find($id);
	}
}