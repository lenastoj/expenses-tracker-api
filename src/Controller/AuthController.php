<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Validator\LoginValidator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

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
//        if (!empty($errors)) {
//            return $this->json($errors, Response::HTTP_BAD_REQUEST);
//        }

        $token = $jwtManager->create($user);
        $cookie = new Cookie('jwt_token', $token, time() + 3600, '/', null, false, true);
        $response = new JsonResponse();
        $response->headers->setCookie($cookie);

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
        $response->headers->clearCookie('jwt_token', '/', null, false, true);
        $responseData = [
            'message' => 'Logged out successfully'
        ];

        $response->setData($responseData);
        return $response;
    }

    #[Route('/api/auth', methods: 'GET')]
    public function activeUser(): JsonResponse
    {
        $user = $this->getUser();
        $userData = [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
        ];

        return $this->json($userData, Response::HTTP_OK);
    }
}
