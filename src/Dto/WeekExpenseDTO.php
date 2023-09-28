<?php

declare(strict_types=1);

namespace App\Dto;

class WeekExpenseDTO
{
    /** @var ExpenseDTO[] */
    private array $expenseDTOs;
    private float $totalAmount;
    private float $averagePerDayAmount;
    private ?string $startDateRange;
    private ?string $endDateRange;

    public function __construct(
        array $expenses,
        $totalAmount,
        $averagePerDayAmount,
        $startDateRange = null,
        $endDateRange = null
    ) {
        $this->expenseDTOs = $expenses;
        $this->totalAmount = $totalAmount;
        $this->averagePerDayAmount = $averagePerDayAmount;
        $this->startDateRange = $startDateRange;
        $this->endDateRange = $endDateRange;
    }

    public function getData(): array
    {
        return $this->expenseDTOs;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function getAveragePerDayAmounts(): float
    {
        return $this->averagePerDayAmount;
    }

    public function getStartDateRange(): string|null
    {
        return $this->startDateRange;
    }

    public function getEndDateRange(): string|null
    {
        return $this->endDateRange;
    }

    public static function create(
        array $expenses,
        $totalAmount,
        $averagePerDayAmount,
        $startDateRange,
        $endDateRange
    ): self {
        return new self(
            $expenses,
            $totalAmount,
            $averagePerDayAmount,
            $startDateRange,
            $endDateRange,
        );
    }
}
