<?php

namespace App\Application;

use App\Factory\ExpenseFactory;
use App\Repository\ExpenseRepository;
use App\Validator\ExpenseValidator;

class ExpenseCreateEditService
{
    private ExpenseRepository $expenseRepository;
    private ExpenseFactory $expenseFactory;
    private ExpenseValidator $expenseValidator;


    public function __construct(
        ExpenseRepository $expenseRepository,
        ExpenseFactory $expenseFactory,
        ExpenseValidator $expenseValidator,
    ) {
        $this->expenseRepository = $expenseRepository;
        $this->expenseFactory = $expenseFactory;
        $this->expenseValidator = $expenseValidator;
    }

    public function createExpense(
        $requestData,
        $user,
    ): bool | array {
        $errors = $this->expenseValidator->validate($requestData);

        if (!empty($errors)) {
            return $errors;
        }
        $expense = $this->expenseFactory->createExpense($requestData, $user);
        $this->expenseRepository->save($expense);

        return false;
    }

    public function updateExpense($id, $user, $requestData): bool | array
    {
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
