<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\ExpenseCreateEditService;
use App\Application\ExpensesPrintWeekService;
use App\Repository\ExpenseRepository;
use App\Utils\Pagination;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExpenseController extends AbstractController
{
    private ExpenseRepository $expenseRepository;
    private Pagination $pagination;
    private ExpensesPrintWeekService $expensesPrintWeekService;

    public function __construct(
        Pagination $pagination,
        ExpenseRepository $expenseRepository,
        ExpensesPrintWeekService $expensesPrintWeekService
    ) {
        $this->expenseRepository = $expenseRepository;
        $this->pagination = $pagination;
        $this->expensesPrintWeekService = $expensesPrintWeekService;
    }

    #[Route('/api/expenses', methods: 'POST')]
    public function create(
        Request $request,
        ExpenseCreateEditService $expenseCreateEditService,
    ): JsonResponse {
        try {
            $user = $this->getUser();
            $requestData = json_decode($request->getContent(), true);
            $confirmation = $expenseCreateEditService->createExpense($requestData, $user);
            if (!$confirmation) {
                return $this->json(['message' => 'Expense created successfully'], Response::HTTP_CREATED);
            }
            return $this->json($confirmation, Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage(), 'message' => 'ovo radi'], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/expenses/{id}', methods: 'PUT')]
    public function update(
        int $id,
        Request $request,
        ExpenseCreateEditService $expenseCreateEditService,
    ): JsonResponse {
        try {
            $user = $this->getUser();
            $requestData = json_decode($request->getContent(), true);

            $confirmation = $expenseCreateEditService->updateExpense($id, $user, $requestData);
            if (!$confirmation) {
                return $this->json(['message' => 'Expense updated successfully'], Response::HTTP_CREATED);
            }
            return $this->json($confirmation, Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/expenses/week', methods: 'GET')]
    public function weekList(): JsonResponse
    {
        try {
            $user = $this->getUser();
            $userId = $user->getId();
            $expenses = $this->expensesPrintWeekService->getWeekExpenses($userId);
            return $this->json($expenses, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/expenses/print', methods: 'GET')]
    public function printList(Request $request,): JsonResponse
    {
        try {
            $user = $this->getUser();
            $userId = $user->getId();
            $expenses = $this->expensesPrintWeekService->getExpensesForPrint(
                $userId,
                $request->query->get('word'),
                $request->query->get('sort'),
                $request->query->get('order'),
                $request->query->get('startDate'),
                $request->query->get('endDate'),
            );
            return $this->json($expenses, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }


    #[Route('/api/expenses', methods: 'GET')]
    public function index(
        Request $request,
    ): JsonResponse {
        try {
            $page = (int)$request->query->get('page', 1);
            if ($page < 1) {
                $page = 1;
            }
            $perPage = 5;

            $user = $this->getUser();
            $userId = $user->getId();

            $query = $this->expenseRepository->getExpensesQueryBuilderForUser(
                $userId,
                $request->query->get('word'),
                $request->query->get('month'),
                $request->query->get('sort'),
                $request->query->get('order'),
                $request->query->get('startDate'),
                $request->query->get('endDate'),
            );

            $expenses = $this->pagination->paginate($query, $page, $perPage);

            if (empty($expenses)) {
                return $this->json(['message' => 'No expenses'], Response::HTTP_NOT_FOUND);
            }

            $totalExpenses = $this->expenseRepository->getExpensesQueryBuilderForUser(
                $userId,
                $request->query->get('word'),
                $request->query->get('month'),
                $request->query->get('sort'),
                $request->query->get('order'),
                $request->query->get('startDate'),
                $request->query->get('endDate'),
                true,
            );

            $totalExpensesCount = count($totalExpenses);
            $totalPages = ceil($totalExpensesCount / $perPage);

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
                'total' => $totalExpensesCount,
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

    #[Route('/api/expenses/{id}', methods: 'GET')]
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
