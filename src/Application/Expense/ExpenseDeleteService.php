<?php

namespace App\Application\Expense;

use App\Repository\ExpenseRepository;
use App\Repository\UserRepository;

class ExpenseDeleteService
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
     * @return bool
     */
    public function deleteExpense(int $userId, int $id): bool
    {
        $user = $this->userRepository->findOneBy(['id' => $userId]);
        $expense = $this->expenseRepository->findOneBy(['id' => $id, 'user' => $user]);

        if (!$expense) {
            return false;
        }
        $this->expenseRepository->delete($expense);
        return true;
    }
}
