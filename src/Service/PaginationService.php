<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Expense;
use App\Repository\ExpenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;

class PaginationService
{
    /**
     * @throws NonUniqueResultException
     */

    private ExpenseRepository $expenseRepository;

    public function __construct(ExpenseRepository $expenseRepository)
    {
        $this->expenseRepository = $expenseRepository;
    }

    public function paginate(int $page = 1, int $paginationLimit = 5): array
    {
        if ($page < 1) {
            $page = 1;
        }

//        $queryBuilder
//            ->setFirstResult(($page - 1) * $paginationLimit)
//            ->setMaxResults($paginationLimit);

        $queryBuilder = $this->expenseRepository->queryExpenses($page, $paginationLimit);

        $expenses = $queryBuilder
            ->getQuery()
            ->getResult();

        //            $data = [];
//            foreach ($expenses as $expense) {
//                $data[] = [
//                    'id' => $expense->getId(),
//                    'date' => $expense->getDate(),
//                    'time' => $expense->getTime(),
//                    'description' => $expense->getDescription(),
//                    'amount' => $expense->getAmount() / 100,
//                    'comment' => $expense->getComment(),
//                ];
//            }
        $totalCountQuery = clone $queryBuilder;
        $totalCountQuery
            ->resetDQLPart('orderBy')
            ->resetDQLPart('groupBy')
            ->select('COUNT(e.id)') // Assuming your primary key is "id"
            ->setMaxResults(null)
            ->setFirstResult(null);

        $total = (int)$totalCountQuery->getQuery()->getSingleScalarResult();

        return [
            'data' => $paginated,
            'metadata' => [
                'page' => $page,
                'paginationLimit' => $paginationLimit,
                'count' => count($paginated),
                'total' => $total,
            ],
        ];
    }
}