<?php

namespace App\Application;

use App\Dto\Expense\ExpenseDTO;
use App\Dto\Expense\WeekExpenseDTO;
use App\Repository\ExpenseRepository;
use App\Repository\UserRepository;

class ExpensesPrintWeekService
{
    private ExpenseRepository $expenseRepository;
    private UserRepository $userRepository;

    public function __construct(
        ExpenseRepository $expenseRepository,
        UserRepository $userRepository,
    ) {
        $this->expenseRepository = $expenseRepository;
        $this->userRepository = $userRepository;
    }

    public function getExpensesForPrint(
        $userId,
        $id,
        $word,
        $sort,
        $order,
        $startDate,
        $endDate
    ): WeekExpenseDTO {

        $hostUser = $this->userRepository->findOneBy(['id' => $userId]);
        $maybeGuestUser = $this->userRepository->findOneBy(['id' => $id]);
        $guestUser = $hostUser->getGuests()->contains($maybeGuestUser);

        $expensesData = $this->expenseRepository->getExpensesQueryBuilderForUser(
            $guestUser ? $id : $userId,
            $word,
            $sort,
            $order,
            $startDate,
            $endDate,
            true
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

    public function getWeekExpenses(int $userId, $id): WeekExpenseDTO
    {
        $hostUser = $this->userRepository->findOneBy(['id' => $userId]);
        $maybeGuestUser = $this->userRepository->findOneBy(['id' => $id]);
        $guestUser = $hostUser->getGuests()->contains($maybeGuestUser);

        $current = date("l");
        $startOfWeek = date("Y-m-d", strtotime('monday this week'));
        $endOfWeekWithToday = date("Y-m-d", strtotime("$current this week"));

        $expensesData = $this->expenseRepository->getWeekExpensesQueryBuilder(
            $startOfWeek,
            $endOfWeekWithToday,
            $guestUser ? $id : $userId,
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
