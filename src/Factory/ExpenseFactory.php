<?php

namespace App\Factory;

use App\Entity\Expense;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

class ExpenseFactory
{
    private function setExpenseProperties(Expense $expense, array $requestData, UserInterface $user): void
    {
        $expense
            ->setDate(new DateTime($requestData['date']))
            ->setDescription($requestData['description'])
            ->setAmount(intval($requestData['amount'] * 100))
            ->setTime($requestData['time'] ? new DateTime($requestData['time']) : null)
            ->setComment($requestData['comment'] ?? null)
            ->setUser($user);
    }

    public function createExpense(array $requestData, UserInterface $user): Expense
    {
        $expense = new Expense();
        $this->setExpenseProperties($expense, $requestData, $user);

        return $expense;
    }

    public function updateExpense(Expense $expense, array $requestData, UserInterface $user): void
    {
        $this->setExpenseProperties($expense, $requestData, $user);
    }
}
