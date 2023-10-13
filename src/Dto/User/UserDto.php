<?php

namespace App\Dto\User;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class UserDto
{
    private int $id;
    private string $email;
    private string $firstName;
    private string $lastName;

    public function __construct(
        int $id,
        string $email,
        string $firstName,
        string $lastName,
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getFirstName(): string
    {
        return $this->firstName;
    }
    public function getLastName(): string
    {
        return $this->lastName;
    }


    public static function createFromEntity(User | UserInterface $user): self
    {
        return new self(
            $user->getId(),
            $user->getEmail(),
            $user->getFirstName(),
            $user->getLastName(),
        );
    }


    public static function createFromArray(array $user): self
    {

        return new self(
            $user['id'],
            $user['email'],
            $user['firstName'],
            $user['lastName'],
        );
    }
}
