<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Expense\ExpenseCreateEditService;
use App\Application\Expense\ExpenseDeleteService;
use App\Application\Expense\ExpenseShowService;
use App\Application\Expense\ExpensesIndexService;
use App\Application\Expense\ExpensesPrintWeekService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExpenseController extends AbstractController
{
    private ExpenseCreateEditService $expenseCreateEditService;
    private ExpensesPrintWeekService $expensesPrintWeekService;

    public function __construct(
        ExpenseCreateEditService $expenseCreateEditService,
        ExpensesPrintWeekService $expensesPrintWeekService
    ) {
        $this->expenseCreateEditService = $expenseCreateEditService;
        $this->expensesPrintWeekService = $expensesPrintWeekService;
    }

    #[Route('/api/expenses', methods: 'POST')]
    public function create(
        Request $request
    ): JsonResponse {
        try {
            $userId = $this->getUser()->getId();
            $requestData = json_decode($request->getContent(), true);
            $confirmation = $this->expenseCreateEditService->createExpense($userId, $requestData);
            if (!$confirmation) {
                return $this->json(['message' => 'Expense created successfully'], Response::HTTP_CREATED);
            }
            return $this->json($confirmation, Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/expenses/{id}', methods: 'PUT')]
    public function update(
        int $id,
        Request $request,
    ): JsonResponse {
        try {
            $userId = $this->getUser()->getId();
            $requestData = json_decode($request->getContent(), true);

            $confirmation = $this->expenseCreateEditService->updateExpense($userId, $id, $requestData);
            if (!$confirmation) {
                return $this->json(['message' => 'Expense updated successfully'], Response::HTTP_CREATED);
            }
            return $this->json($confirmation, Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/expenses/print', methods: 'GET')]
    public function printList(Request $request): JsonResponse
    {
        try {
            $userId = $this->getUser()->getId();
            $id = (int)$request->query->get('id');
            $weekExpenses = $request->query->get('week');
            if ($weekExpenses === 'true') {
                $expenses = $this->expensesPrintWeekService->getExpensesForPrint($userId, $id, true);
                return $this->json($expenses, Response::HTTP_OK);
            }
            $expenses = $this->expensesPrintWeekService->getExpensesForPrint(
                $userId,
                $id,
                false,
                $request->query->get('searchQuery'),
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
        ExpensesIndexService $expensesIndexService,
    ): JsonResponse {
        try {
            $userId = $this->getUser()->getId();
            $page = (int)$request->query->get('page', 1);
            $id = (int)$request->query->get('id');

            $response = $expensesIndexService->getExpenses(
                $userId,
                $id,
                $request->query->get('searchQuery'),
                $request->query->get('sort'),
                $request->query->get('order'),
                $request->query->get('startDate'),
                $request->query->get('endDate'),
                $page,
            );
            return $this->json($response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/expenses/{id}', methods: 'GET')]
    public function show(int $id, ExpenseShowService $expenseShowService): JsonResponse
    {
        try {
            $userId = $this->getUser()->getId();
            $response = $expenseShowService->showExpense($userId, $id);
            if (!$response) {
                return $this->json(['message' => 'Expense not found'], Response::HTTP_NOT_FOUND);
            }
            return $this->json($response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/expenses/{id}', methods: 'DELETE')]
    public function delete(int $id, ExpenseDeleteService $expenseDeleteService): JsonResponse
    {
        try {
            $userId = $this->getUser()->getId();
            $response = $expenseDeleteService->deleteExpense($userId, $id);
            if (!$response) {
                return $this->json(['message' => 'Expense not found'], Response::HTTP_NOT_FOUND);
            }
            return $this->json(['message' => 'Expense deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
