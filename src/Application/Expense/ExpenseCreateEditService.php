<?php

namespace App\Application\Expense;

use App\Factory\ExpenseFactory;
use App\Repository\ExpenseRepository;
use App\Repository\UserRepository;
use App\Validator\ExpenseValidator;

class ExpenseCreateEditService
{
    private ExpenseRepository $expenseRepository;
    private ExpenseFactory $expenseFactory;
    private ExpenseValidator $expenseValidator;
    private UserRepository $userRepository;


    public function __construct(
        ExpenseRepository $expenseRepository,
        ExpenseFactory $expenseFactory,
        ExpenseValidator $expenseValidator,
        UserRepository $userRepository,
    ) {
        $this->expenseRepository = $expenseRepository;
        $this->expenseFactory = $expenseFactory;
        $this->expenseValidator = $expenseValidator;
        $this->userRepository = $userRepository;
    }

    public function createExpense(
        int $userId,
        array $requestData,
    ): bool|array {
        $errors = $this->expenseValidator->validate($requestData);

        if (!empty($errors)) {
            return $errors;
        }
        $user = $this->userRepository->findOneBy(['id' => $userId]);
        $expense = $this->expenseFactory->createExpense($requestData, $user);
        $this->expenseRepository->save($expense);

        return false;
    }

    public function updateExpense(int $userId, int $id, array $requestData): bool|array
    {
        $user = $this->userRepository->findOneBy(['id' => $userId]);
        $expense = $this->expenseRepository->findOneBy(['id' => $id, 'user' => $user]);

        if (!$expense) {
            return ['message' => 'Expense not found'];
        }

        $errors = $this->expenseValidator->validate($requestData);

        if (!empty($errors)) {
            return $errors;
        }

        $this->expenseFactory->updateExpense($expense, $requestData, $user);
        $this->expenseRepository->save($expense);
        return false;
    }
}
