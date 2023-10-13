<?php

namespace App\Application\Expense;

use App\Dto\Expense\ExpenseDTO;
use App\Dto\Expense\PrintExpenseDTO;
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

    /**
     * @return array{startOfWeek: string, endOfWeekWithToday: string}
     */
    private function currentWeek()
    {
        $current = date("l");
        $startOfWeek = date("Y-m-d", strtotime('monday this week'));
        $endOfWeekWithToday = date("Y-m-d", strtotime("$current this week"));
        return ['startOfWeek' => $startOfWeek, 'endOfWeekWithToday' => $endOfWeekWithToday];
    }

    /**
     * @return PrintExpenseDTO
     */
    public function getExpensesForPrint(
        int $userId,
        int $id,
        bool $weekExpenses,
        string|null $searchQuery = null,
        string|null $sort = null,
        string|null $sortDirection = null,
        string|null $startDate = null,
        string|null $endDate = null,
    ) {
        $hostUser = $this->userRepository->findOneBy(['id' => $userId]);
        $maybeGuestUser = $this->userRepository->findOneBy(['id' => $id]);
        $guestUser = $hostUser->getGuests()->contains($maybeGuestUser);
        $currentWeek = $this->currentWeek();

        $expensesData = $weekExpenses ? $this->expenseRepository->getExpenses(
            $guestUser ? $id : $userId,
            true,
            $currentWeek['startOfWeek'],
            $currentWeek['endOfWeekWithToday'],
        ) : $this->expenseRepository->getExpenses(
            $guestUser ? $id : $userId,
            true,
            $startDate,
            $endDate,
            $searchQuery,
            $sort,
            $sortDirection,
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
        return PrintExpenseDTO::create(
            $expenses,
            round($amount, 2),
            $averagePerDayAmount,
            $startDate,
            $endDate
        );
    }
}
