<?php

namespace App\Application\Expense;

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

    public function getExpenses(
        int $userId,
        int $id,
        string | null $searchQuery,
        string | null $sort,
        string | null $sortDirection,
        string | null $startDate,
        string | null $endDate,
        int $page,
        int $perPage = 5
    ): array {
        $page = ($page < 1) ? 1 : $page;

        $hostUser = $this->userRepository->findOneBy(['id' => $userId]);
        $maybeGuestUser = $this->userRepository->findOneBy(['id' => $id]);
        $guestUser = $hostUser->getHosts()->contains($maybeGuestUser);

        $query = $this->expenseRepository->getExpenses(
            $guestUser ? $id : $userId,
            false,
            $startDate,
            $endDate,
            $searchQuery,
            $sort,
            $sortDirection,
        );

        $expenses = $this->pagination->paginate($query, $page, $perPage);

        $totalExpenses = $this->expenseRepository->getExpenses(
            $guestUser ? $id : $userId,
            true,
            $startDate,
            $endDate,
            $searchQuery,
            $sort,
            $sortDirection,
        );

        $totalExpensesCount = count($totalExpenses);
        $totalPages = ceil($totalExpensesCount / $perPage);

        $data = array_map(function ($expense) {
            return ExpenseDTO::createFromArray($expense);
        }, $expenses);

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
