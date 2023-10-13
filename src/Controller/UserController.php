<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\User\AddGuestService;
use App\Application\User\IndexGuestService;
use App\Application\User\RemoveGuestUserService;
use App\Application\User\RemoveHostUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/api/guest', methods: 'POST')]
    public function addGuestUser(Request $request, AddGuestService $addGuestService): JsonResponse
    {
        try {
            $userId = $this->getUser()->getId();
            $requestData = json_decode($request->getContent(), true);
            $response = $addGuestService->addGuest($userId, $requestData);
            if ($response) {
                return $this->json($response, Response::HTTP_BAD_REQUEST);
            }
            return $this->json(['message' => 'Guest added successfully'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/guest/{id}', methods: 'DELETE')]
    public function removeGuestUser(int $id, RemoveGuestUserService $removeGuestUserService): JsonResponse
    {
        try {
            $userId = $this->getUser()->getId();
            $response = $removeGuestUserService->removeGuest($userId, $id);
            if (!$response) {
                return $this->json(['message' => 'User is not on the guest list'], Response::HTTP_CONFLICT);
            }
            return $this->json(['message' => 'Guest removed successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/host/{id}', methods: 'DELETE')]
    public function removeHostUser(int $id, RemoveHostUserService $removeHostUserService): JsonResponse
    {
        try {
            $userId = $this->getUser()->getId();
            $response = $removeHostUserService->removeHost($userId, $id);
            if (!$response) {
                return $this->json(['message' => 'User is not on the hosts list'], Response::HTTP_CONFLICT);
            }
            return $this->json(['message' => 'Host removed successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/guest', methods: 'GET')]
    public function indexGuests(Request $request, IndexGuestService $indexGuestService): JsonResponse
    {
        try {
            $userId = $this->getUser()->getId();
            $page = (int)$request->query->get('page', 1);

            $response = $indexGuestService->getGuests(
                $userId,
                $request->query->get('searchQuery'),
                $request->query->get('sort'),
                $request->query->get('sortDirection'),
                $page,
            );
            return $this->json($response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
