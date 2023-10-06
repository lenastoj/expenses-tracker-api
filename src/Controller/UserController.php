<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use App\Validator\GuestValidator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private UserRepository $userRepository;
    private GuestValidator $guestValidator;

    public function __construct(UserRepository $userRepository, GuestValidator $guestValidator)
    {
        $this->userRepository = $userRepository;
        $this->guestValidator = $guestValidator;
    }

    #[Route('/api/guest', methods: 'POST')]
    public function addGuestUser(Request $request, MailerInterface $mailer): JsonResponse
    {
        try {
            $requestData = json_decode($request->getContent(), true);
            $errors = $this->guestValidator->validate($requestData);

            if (!empty($errors)) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            $user = $this->getUser();
            $hostUser = $this->userRepository->find($user);

            $guestUser = $this->userRepository->findOneBy(['email' => $requestData['email']]);

            if (!$guestUser) {
                return $this->json(['email' => ['User with this email does not exists.']], Response::HTTP_BAD_REQUEST);
            };
            if (!$hostUser->addGuest($guestUser)) {
                return $this->json(['email' => ['User is already on the guests list.']], Response::HTTP_CONFLICT);
            }
            if ($guestUser === $hostUser) {
                return $this->json(['email' => ['You cant not invite yourself.']], Response::HTTP_CONFLICT);
            }
            $this->userRepository->save($hostUser);

            $fistName = $hostUser->getFirstName();
            $lastName = $hostUser->getLastName();
            $hostUserId = $hostUser->getId();
            $url = "http://localhost:3000/expenses?page=1&id=$hostUserId";
            $email = (new TemplatedEmail())
                ->from('noreplay@expense.com')
                ->to($guestUser->getEmail())
                ->subject('Invitation to see other user Expenses')
                ->htmlTemplate('email.html.twig')
                ->context([
                    'user_first_name' => $fistName,
                    'user_last_name' => $lastName,
                    'url' => $url,
                ]);
//            $email = (new Email())
//                ->from('noreplay@expense.com')
//                ->to($guestUser->getEmail())
//                ->subject('Invitation to see other user Expenses')
//                ->html('<h3>Invitation from {{$hostUser->getFirstName}}</h3>');

            $mailer->send($email);
            return $this->json(['message' => 'Guest added successfully'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/guest/{id}', methods: 'DELETE')]
    public function removeGuestUser(int $id): JsonResponse
    {
        $user = $this->getUser();
        $hostUser = $this->userRepository->find($user);
        $guestUser = $this->userRepository->findOneBy(['id' => $id]);

        if (!$guestUser || !$hostUser->removeGuest($guestUser)) {
            return $this->json(['message' => 'User is not on the guests list'], Response::HTTP_CONFLICT);
        };

        $this->userRepository->save($hostUser);
        return $this->json(['message' => 'Guest removed successfully'], Response::HTTP_OK);
    }
}
