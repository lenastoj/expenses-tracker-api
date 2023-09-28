<?php

namespace App\Application;

use App\Dto\ExpenseDTO;
use App\Dto\WeekExpenseDTO;
use App\Repository\ExpenseRepository;

class ExpensesPrintWeekService
{
    private ExpenseRepository $expenseRepository;

    public function __construct(
        ExpenseRepository $expenseRepository,
    ) {
        $this->expenseRepository = $expenseRepository;
    }

    public function getExpensesForPrint(
        $userId,
        $word,
        $sort,
        $order,
        $startDate,
        $endDate
    ): WeekExpenseDTO {
        $expensesData = $this->expenseRepository->getPrintExpensesQueryBuilder(
            $userId,
            $word,
            $sort,
            $order,
            $startDate,
            $endDate,
        );
        $expenses = [];
        $amount = 0;

        foreach ($expensesData as $data) {
            $expenseDto = ExpenseDTO::createFromArray($data);
            $amount += $data['amount'] / 100;
            $expenses[] = $expenseDto;
        }

        $averagePerDayAmount = 0;
        if (count($expenses) > 0) {
            $averagePerDayAmount = round($amount / count($expenses), 2);
        }
        return WeekExpenseDTO::create(
            $expenses,
            round($amount, 2),
            $averagePerDayAmount,
            $startDate,
            $endDate
        );
    }

    public function getWeekExpenses(int $userId): WeekExpenseDTO
    {
        $current = date("l");
        $startOfWeek = date("Y-m-d", strtotime('monday this week'));
        $endOfWeekWithToday = date("Y-m-d", strtotime("$current this week"));

        $expensesData = $this->expenseRepository->getWeekExpensesQueryBuilder(
            $startOfWeek,
            $endOfWeekWithToday,
            $userId
        );
        $expenses = [];
        $amount = 0;

        foreach ($expensesData as $data) {
            $expenseDto = ExpenseDTO::createFromArray($data);
            $amount += $data['amount'] / 100;
            $expenses[] = $expenseDto;
        }

        return WeekExpenseDTO::create(
            $expenses,
            round($amount, 2),
            round($amount / date('w'), 2),
            $startOfWeek,
            $endOfWeekWithToday
        );
    }
}
