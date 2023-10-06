<?php

namespace App\Dto\User;

use App\Entity\User;

class UserDto
{
    private int $id;
    private string $firstName;
    private string $lastName;

    public function __construct(
        int $id,
        string $firstName,
        string $lastName,
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getFirstName(): string
    {
        return $this->firstName;
    }
    public function getLastName(): string
    {
        return $this->lastName;
    }

    public static function createFromEntity(User $user): self
    {
        return new self(
            $user->getId(),
            $user->getFirstName(),
            $user->getLastName(),
        );
    }
}
