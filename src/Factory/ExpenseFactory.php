<?php

namespace App\Factory;

use App\Entity\Expense;
use DateTime;

class ExpenseFactory
{
    private function setExpenseProperties(Expense $expense, array $requestData): void
    {
        $expense
            ->setDate(new DateTime($requestData['date']))
            ->setDescription($requestData['description'])
            ->setAmount(intval($requestData['amount'] * 100))
            ->setTime($requestData['time'] ? new DateTime($requestData['time']) : null)
            ->setComment($requestData['comment'] ?? null);
    }

    public function createExpense(array $requestData): Expense
    {
        $expense = new Expense();
        $this->setExpenseProperties($expense, $requestData);

        return $expense;
    }

    public function updateExpense(Expense $expense, array $requestData): void
    {
        $this->setExpenseProperties($expense, $requestData);
    }
}
