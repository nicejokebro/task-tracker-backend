<?php declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTestCase extends WebTestCase
{
	protected KernelBrowser $client;
	protected EntityManagerInterface $entityManager;
	protected ProjectRepository $projectRepository;

	/**
	 * @throws Exception
	 */
	protected function setUp(): void
	{
		$this->client = static::createClient();
		$this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
		$this->projectRepository = static::getContainer()->get(ProjectRepository::class);

		// Очищаем базу перед каждым тестом
		$this->truncateDatabase();
	}

	/**
	 * @throws Exception
	 */
	protected function truncateDatabase(): void
	{
		$connection = $this->entityManager->getConnection();
		$connection->executeStatement('TRUNCATE TABLE project RESTART IDENTITY CASCADE');
	}

	protected function createProject(string $name, ?string $description = null): Project
	{
		$project = new Project();
		$project->setName($name);
		$project->setDescription($description);

		$this->entityManager->persist($project);
		$this->entityManager->flush();

		return $project;
	}
}