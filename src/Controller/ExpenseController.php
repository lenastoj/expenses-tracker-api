<?php

declare(strict_types=1);

namespace App\Controller;

use App\Factory\ExpenseFactory;
use App\Repository\ExpenseRepository;
use App\Service\PaginationService;
use App\Utils\Pagination;
use App\Validator\ExpenseValidator;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExpenseController extends AbstractController
{
    private ExpenseRepository $expenseRepository;
    private ExpenseFactory $expenseFactory;
    private Pagination $pagination;

    public function __construct(
        Pagination $pagination,
        ExpenseRepository $expenseRepository,
        ExpenseFactory $expenseFactory
    ) {
        $this->expenseRepository = $expenseRepository;
        $this->expenseFactory = $expenseFactory;
        $this->pagination = $pagination;
    }

    #[Route('/api/expenses', methods: 'POST')]
    public function create(
        Request $request,
        ExpenseValidator $expenseValidator,
    ): JsonResponse {
        try {
            $requestData = json_decode($request->getContent(), true);
            $errors = $expenseValidator->validate($requestData);

            if (!empty($errors)) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            $user = $this->getUser();
            $expense = $this->expenseFactory->createExpense($requestData, $user);
            $this->expenseRepository->save($expense);

            return $this->json(['message' => 'Expense created successfully'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/expenses/{id}', methods: 'PUT')]
    public function update(
        int $id,
        Request $request,
        ExpenseValidator $expenseValidator,
    ): JsonResponse {
        try {
            $user = $this->getUser();
            $expense = $this->expenseRepository->findOneBy(['id' => $id, 'user' => $user]);

            if (!$expense) {
                return $this->json(['message' => 'Expense not found'], Response::HTTP_NOT_FOUND);
            }

            $requestData = json_decode($request->getContent(), true);
            $errors = $expenseValidator->validate($requestData);

            if (!empty($errors)) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            $user = $this->getUser();
            $this->expenseFactory->updateExpense($expense, $requestData, $user);
            $this->expenseRepository->save($expense);

            return $this->json(['message' => 'Expense updated successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/expenses', methods: 'GET')]
    public function index(
        Request $request,
        PaginationService $paginator
    ): JsonResponse {
        try {
            $page = (int)$request->query->get('page', 1);
            if ($page < 1) {
                $page = 1;
            }
            $perPage = 5; // Expenses per page

            $user = $this->getUser();
            $userId = $user->getId();
            $query = $this->expenseRepository->getExpensesQueryBuilderForUser($userId);
            $expenses = $this->pagination->paginate($query, $page, $perPage);

            if (empty($expenses)) {
                return $this->json(['message' => 'No expenses'], Response::HTTP_NOT_FOUND);
            }
            $totalExpenses = count($this->expenseRepository->getAll($userId));
            $totalPages = ceil($totalExpenses / $perPage);

            $data = [];
            foreach ($expenses as $expense) {
                $data[] = [
                    'id' => $expense->getId(),
                    'date' => $expense->getDate(),
                    'time' => $expense->getTime(),
                    'description' => $expense->getDescription(),
                    'amount' => $expense->getAmount() / 100,
                    'comment' => $expense->getComment(),
                ];
            }
            $metadata = [
                'page' => $page,
                'paginationLimit' => $perPage,
                'count' => count($data),
                'total' => $totalExpenses,
                'totalPages' => $totalPages,
            ];

            $response = [
                'data' => $data,
                'metadata' => $metadata,
            ];
            return $this->json($response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[
        Route('/api/expenses/{id}', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        try {
            $user = $this->getUser();
            $expense = $this->expenseRepository->findOneBy(['id' => $id, 'user' => $user]);

            if (!$expense) {
                return $this->json(['message' => 'Expense not found'], Response::HTTP_NOT_FOUND);
            }

            $data = [
                'id' => $expense->getId(),
                'date' => $expense->getDate(),
                'time' => $expense->getTime(),
                'description' => $expense->getDescription(),
                'amount' => $expense->getAmount() / 100,
                'comment' => $expense->getComment(),
            ];

            return $this->json($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/expenses/{id}', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        try {
            $user = $this->getUser();
            $expense = $this->expenseRepository->findOneBy(['id' => $id, 'user' => $user]);

            if (!$expense) {
                return $this->json(['message' => 'Expense not found'], Response::HTTP_NOT_FOUND);
            }

            $this->expenseRepository->delete($expense, Response::HTTP_OK);

            return $this->json(['message' => 'Expense deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
