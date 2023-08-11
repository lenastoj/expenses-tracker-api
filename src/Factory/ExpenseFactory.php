<?php

namespace App\Factory;

use App\Entity\Expense;
use DateTime;

class ExpenseFactory
{
    public function createExpense(array $requestData): Expense
    {
        $expense = new Expense();

        $expense
            ->setDate(new DateTime($requestData['date']))
            ->setTime(new DateTime($requestData['time']))
            ->setDescription($requestData['description'])
            ->setAmount($requestData['amount'])
            ->setComment($requestData['comment']);

        return $expense;
    }
}