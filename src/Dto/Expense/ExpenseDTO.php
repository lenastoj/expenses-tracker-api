<?php

namespace App\Dto\Expense;

use App\Entity\Expense;

class ExpenseDTO
{
    private int $id;
    private \DateTimeInterface | string $date;
    private \DateTimeInterface | string | null $time = null;
    private string $description;
    private float $amount;
    private ?string $comment = null;

    public function __construct(
        int $id,
        \DateTimeInterface | string $date,
        string $description,
        float $amount,
        \DateTimeInterface | string | null $time = null,
        string $comment = null,
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->time = $time;
        $this->description = $description;
        $this->amount = $amount / 100;
        $this->comment = $comment;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): string
    {
        if (is_string($this->date)) {
            return $this->date;
        }
        return $this->date->format('Y-m-d');
    }

    public function getTime(): ?string
    {
        if (is_string($this->time)) {
            return $this->time;
        }
        return $this->time?->format('H:i:s');
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public static function createFromArray(array $expense): self
    {
        return new self(
            $expense['id'],
            $expense['date'],
            $expense['description'],
            $expense['amount'],
            $expense['time'],
            $expense['comment'],
        );
    }
    public static function createFromEntity(Expense $expense): self
    {
        return new self(
            $expense->getId(),
            $expense->getDate(),
            $expense->getDescription(),
            $expense->getAmount(),
            $expense->getTime(),
            $expense->getComment(),
        );
    }
}
