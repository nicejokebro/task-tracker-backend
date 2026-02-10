<?php declare(strict_types=1);

namespace App\Tests\Functional\Api;

use App\Tests\Functional\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProjectControllerTest extends ApiTestCase
{
	public function testCreateProject(): void
	{
		$payload = [
			'name' => 'Test Project',
			'description' => 'Test Description'
		];

		$this->client->request(
			'POST',
			'/api/projects',
			[],
			[],
			['CONTENT_TYPE' => 'application/json'],
			json_encode($payload)
		);

		$this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

		$data = json_decode($this->client->getResponse()->getContent(), true);

		$this->assertArrayHasKey('id', $data);
		$this->assertEquals('Test Project', $data['name']);
		$this->assertEquals('Test Description', $data['description']);
		$this->assertArrayHasKey('createdAt', $data);
	}

	public function testCreateProjectWithInvalidData(): void
	{
		$payload = ['name' => ''];

		$this->client->request(
			'POST',
			'/api/projects',
			[],
			[],
			['CONTENT_TYPE' => 'application/json'],
			json_encode($payload)
		);

		$this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

		$data = json_decode($this->client->getResponse()->getContent(), true);

		$this->assertArrayHasKey('errors', $data);
		$this->assertArrayHasKey('name', $data['errors']);
	}

	public function testGetProjectList(): void
	{
		$this->createProject('Project 1');
		$this->createProject('Project 2');

		$this->client->request('GET', '/api/projects');

		$this->assertResponseIsSuccessful();

		$data = json_decode($this->client->getResponse()->getContent(), true);

		$this->assertIsArray($data);
		$this->assertCount(2, $data);

		$names = array_column($data, 'name');
		$this->assertContains('Project 1', $names);
		$this->assertContains('Project 2', $names);
	}

	public function testGetSingleProject(): void
	{
		$project = $this->createProject('Single Project', 'Description');
		$id = $project->getId();

		$this->client->request('GET', "/api/projects/{$id}");

		$this->assertResponseIsSuccessful();

		$data = json_decode($this->client->getResponse()->getContent(), true);

		$this->assertEquals($id, $data['id']);
		$this->assertEquals('Single Project', $data['name']);
		$this->assertEquals('Description', $data['description']);
	}

	public function testGetNonExistentProject(): void
	{
		$this->client->request('GET', '/api/projects/999');

		$this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
	}

	public function testUpdateProject(): void
	{
		$project = $this->createProject('Old Name', 'Old Description');
		$id = $project->getId();

		$payload = [
			'name' => 'Updated Name',
			'description' => 'Updated Description'
		];

		$this->client->request(
			'PATCH',
			"/api/projects/{$id}",
			[],
			[],
			['CONTENT_TYPE' => 'application/json'],
			json_encode($payload)
		);

		$this->assertResponseIsSuccessful();

		$data = json_decode($this->client->getResponse()->getContent(), true);

		$this->assertEquals($id, $data['id']);
		$this->assertEquals('Updated Name', $data['name']);
		$this->assertEquals('Updated Description', $data['description']);
	}

	public function testUpdateNonExistentProject(): void
	{
		$payload = ['name' => 'Updated'];

		$this->client->request(
			'PATCH',
			'/api/projects/999',
			[],
			[],
			['CONTENT_TYPE' => 'application/json'],
			json_encode($payload)
		);

		$this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
	}

	public function testDeleteProject(): void
	{
		$project = $this->createProject('To Delete');
		$id = $project->getId();

		$this->client->request('DELETE', "/api/projects/{$id}");

		$this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

		// Проверяем, что проект удалён
		$this->client->request('GET', "/api/projects/{$id}");
		$this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
	}

	public function testDeleteNonExistentProject(): void
	{
		$this->client->request('DELETE', '/api/projects/999');

		$this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
	}
}