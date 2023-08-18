<?php

declare(strict_types=1);

namespace App\Controller;

use App\Factory\ExpenseFactory;
use App\Repository\ExpenseRepository;
use App\Validator\ExpenseValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExpenseController extends AbstractController
{
    private ExpenseRepository $expenseRepository;
    private ExpenseFactory $expenseFactory;

    public function __construct(ExpenseRepository $expenseRepository, ExpenseFactory $expenseFactory)
    {
        $this->expenseRepository = $expenseRepository;
        $this->expenseFactory = $expenseFactory;
    }

    #[Route('/expenses', methods: 'POST')]
    public function create(
        Request $request,
        ExpenseValidator $expenseValidator,
    ): JsonResponse {
        try {
            $requestData = json_decode($request->getContent(), true);
            $errors = $expenseValidator->validate($requestData);

            if (!empty($errors)) {
                return $this->json($errors, 400);
            }

            $expense = $this->expenseFactory->createExpense($requestData);
            $this->expenseRepository->save($expense);

            return $this->json(['message' => 'Expense created successfully']);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()]);
        }
    }

    #[Route('/expenses/{id}', methods: 'PUT')]
    public function update(
        int $id,
        Request $request,
        ExpenseValidator $expenseValidator,
    ): JsonResponse {
        try {
            $expense = $this->expenseRepository->find($id);

            if (!$expense) {
                return $this->json(['message' => 'Expense not found'], 404);
            }

            $requestData = json_decode($request->getContent(), true);
            $errors = $expenseValidator->validate($requestData);

            if (!empty($errors)) {
                return $this->json($errors, 400);
            }

            $this->expenseFactory->updateExpense($expense, $requestData);
            $this->expenseRepository->save($expense);

            return $this->json(['message' => 'Expense updated successfully']);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()]);
        }
    }

    #[Route('/expenses', methods: 'GET')]
    public function index(): JsonResponse
    {
        try {
            $expenses = $this->expenseRepository->getAll();
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
            return $this->json($data);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()]);
        }
    }

    #[Route('/expenses/{id}', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        try {
            $expense = $this->expenseRepository->find($id);

            if (!$expense) {
                return $this->json(['message' => 'Expense not found'], 404);
            }

            $data = [
                'id' => $expense->getId(),
                'date' => $expense->getDate(),
                'time' => $expense->getTime(),
                'description' => $expense->getDescription(),
                'amount' => $expense->getAmount() / 100,
                'comment' => $expense->getComment(),
            ];

            return $this->json($data);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()]);
        }
    }

    #[Route('/expenses/{id}', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        try {
            $expense = $this->expenseRepository->find($id);

            if (!$expense) {
                return $this->json(['message' => 'Expense not found'], 404);
            }

            $this->expenseRepository->delete($expense);

            return $this->json(['message' => 'Expense deleted successfully']);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()]);
        }
    }
}
