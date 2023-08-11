<?php

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
    #[Route('/create-expense', name: 'app_expense', methods: 'POST')]
    public function createExpense(
        ExpenseFactory $expenseFactory,
        Request $request,
        ExpenseValidator $expenseValidator,
        ExpenseRepository $expenseRepository,
    ): JsonResponse {
        try {
            $requestData = json_decode($request->getContent(), true);
            $errors = $expenseValidator->validate($requestData);

            if (!empty($errors)) {
                return $this->json($errors, 400);
            }

            $expense = $expenseFactory->createExpense($requestData);

            $expenseRepository->save($expense);

            return $this->json(['message' => 'Expense created successfully']);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()]);
        }
    }
}
