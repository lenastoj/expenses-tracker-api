<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 100)]
    private string $firstName;

    #[ORM\Column(length: 100)]
    private string $lastName;

    #[ORM\Column(length: 100)]
    private string $email;

    #[ORM\Column(length: 100)]
    private string $password;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Expense::class)]
    private Collection $expenses;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'myGuests')]
    private Collection $myHosts;

    #[JoinTable(name: 'guests')]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'guest_user_id', referencedColumnName: 'id')]
    #[ManyToMany(targetEntity: 'User', inversedBy: 'myHosts')]
    private Collection $myGuests;

    public function __construct()
    {
        $this->expenses = new ArrayCollection();
        $this->myHosts = new ArrayCollection();
        $this->myGuests = new ArrayCollection();
    }

    public function getGuests(): Collection
    {
        return $this->myGuests;
    }

    public function addGuest(User $guest): bool
    {
        if (!$this->myGuests->contains($guest)) {
            $this->myGuests->add($guest);
            return true;
        }
        return false;
    }

    public function removeGuest(User $guest): bool
    {
        if ($this->myGuests->contains($guest)) {
            $this->myGuests->removeElement($guest);
            return true;
        }
        return false;
    }

    public function getHosts(): Collection
    {
        return $this->myHosts;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(
        string $firstName
    ): self {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(
        string $lastName
    ): self {
        $this->lastName = $lastName;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(
        string $password
    ): self {
        $this->password = $password;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(
        string $email
    ): self {
        $this->email = $email;
        return $this;
    }

    /**
     * @return Collection<int, Expense>
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    public function addExpense(
        Expense $expense
    ): static {
        if (!$this->expenses->contains($expense)) {
            $this->expenses->add($expense);
            $expense->setUser($this);
        }

        return $this;
    }

    public function removeExpense(
        Expense $expense
    ): static {
        if ($this->expenses->removeElement($expense)) {
            if ($expense->getUser() === $this) {
                $expense->setUser(null);
            }
        }

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

}
