<?php

namespace App\Controller;

use App\Application\User\ActiveUserService;
use App\Repository\UserRepository;
use App\Validator\LoginValidator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/api/login', methods: 'POST')]
    public function login(
        Request $request,
        JWTTokenManagerInterface $jwtManager,
        LoginValidator $loginValidator,
        UserPasswordHasherInterface $passwordHasher,
    ): JsonResponse {
        $requestData = json_decode($request->getContent(), true);
        $errors = $loginValidator->validate($requestData);
        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findOneBy(['email' => $requestData['email']]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $requestData['password'])) {
            $errors['password'][] = 'Invalid credentials';
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $token = $jwtManager->create($user);
        $cookie = new Cookie('jwt_token', $token, time() + 3600, '/', null, false, true);
        $cookie2 = new Cookie('login', 'true', time() + 3600, '/', null, false, false);
        $response = new JsonResponse();
        $response->headers->setCookie($cookie);
        $response->headers->setCookie($cookie2);

        $responseData = [
            'user' => [
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
            ]
        ];

        $response->setData($responseData);
        return $response;
    }

    #[Route('/api/logout', methods: 'POST')]
    public function logout(Request $request): JsonResponse
    {
        $response = new JsonResponse();
        $response->headers->clearCookie('jwt_token');
        $response->headers->clearCookie('login');
        $responseData = [
            'message' => 'Logged out successfully'
        ];

        $response->setData($responseData);
        return $response;
    }

    #[Route('/api/auth', methods: 'GET')]
    public function activeUser(ActiveUserService $activeUserService): JsonResponse
    {
        try {
            $userId = $this->getUser()->getId();
            $response = $activeUserService->getActiveUser($userId);
            return $this->json($response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
//
///**
// * @return array{user: UserDto, hosts: UserDto[]}
// */
//public function getActiveUser(int $userId): array
//{
//    $hostUser = $this->userRepository->findOneBy(['id' => $userId]);
//    $hosts = $hostUser->getHosts()->getValues();
//    $filteredHosts = array_map(function ($user) {
//        return UserDto::createFromEntity($user);
//    }, $hosts);
//
//    return [
//        'user' => UserDto::createFromEntity($hostUser),
//        'hosts' => $filteredHosts,
//    ];
//}
