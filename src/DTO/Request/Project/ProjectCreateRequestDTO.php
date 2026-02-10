<?php declare(strict_types=1);

namespace App\DTO\Request\Project;

use Symfony\Component\Validator\Constraints as Assert;

class ProjectCreateRequestDTO
{
	#[Assert\NotBlank(message: "Name cannot be blank")]
	#[Assert\Length(max: 255, maxMessage: "Name cannot be longer than 255 characters")]
	public string $name;

	#[Assert\Length(max: 65535, maxMessage: "Description is too long")]
	public ?string $description = null;
}