<?php

namespace App\Application;

use App\Dto\Expense\ExpenseDTO;
use App\Repository\ExpenseRepository;
use App\Repository\UserRepository;
use App\Utils\Pagination;

class ExpensesIndexService
{
    private ExpenseRepository $expenseRepository;
    private Pagination $pagination;
    private UserRepository $userRepository;

    public function __construct(
        ExpenseRepository $expenseRepository,
        Pagination $pagination,
        UserRepository $userRepository,
    ) {
        $this->expenseRepository = $expenseRepository;
        $this->pagination = $pagination;
        $this->userRepository = $userRepository;
    }

    public function getExpenses($page, $userId, $id, $word, $sort, $order, $startDate, $endDate, $perPage = 5): array
    {
        if ($page < 1) {
            $page = 1;
        }
        $hostUser = $this->userRepository->findOneBy(['id' => $userId]);
        $maybeGuestUser = $this->userRepository->findOneBy(['id' => $id]);
        $guestUser = $hostUser->getHosts()->contains($maybeGuestUser);

        $query = $this->expenseRepository->getExpensesQueryBuilderForUser(
            $guestUser ? $id : $userId,
            $word,
            $sort,
            $order,
            $startDate,
            $endDate,
        );

        $expenses = $this->pagination->paginate($query, $page, $perPage);

        $totalExpenses = $this->expenseRepository->getExpensesQueryBuilderForUser(
            $guestUser ? $id : $userId,
            $word,
            $sort,
            $order,
            $startDate,
            $endDate,
            true,
        );

        $totalExpensesCount = count($totalExpenses);
        $totalPages = ceil($totalExpensesCount / $perPage);

        $data = [];
        foreach ($expenses as $expense) {
            $expenseDto = ExpenseDTO::createFromArray($expense);
            $data[] = $expenseDto;
        }
        $metadata = [
            'page' => $page,
            'paginationLimit' => $perPage,
            'count' => count($data),
            'total' => $totalExpensesCount,
            'totalPages' => $totalPages,
        ];
        return [
            'data' => $data,
            'metadata' => $metadata,
        ];
    }
}
