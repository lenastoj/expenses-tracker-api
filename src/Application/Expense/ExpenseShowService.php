<?php

namespace App\Application\Expense;

use App\Dto\Expense\ExpenseDTO;
use App\Repository\ExpenseRepository;
use App\Repository\UserRepository;

class ExpenseShowService
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
     * @return bool|ExpenseDTO
     */
    public function showExpense(int $userId, int $id): bool | ExpenseDTO
    {
        $user = $this->userRepository->findOneBy(['id' => $userId]);
        $expense = $this->expenseRepository->findOneBy(['id' => $id, 'user' => $user]);

        if (!$expense) {
            return false;
        }
        return ExpenseDTO::createFromEntity($expense);
    }
}
