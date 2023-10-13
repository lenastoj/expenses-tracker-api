<?php

namespace App\Application\User;

use App\Repository\UserRepository;
use App\Validator\GuestValidator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class AddGuestService
{
    private UserRepository $userRepository;
    private GuestValidator $guestValidator;
    private MailerInterface $mailer;

    public function __construct(
        UserRepository $userRepository,
        GuestValidator $guestValidator,
        MailerInterface $mailer,
    ) {
        $this->userRepository = $userRepository;
        $this->guestValidator = $guestValidator;
        $this->mailer = $mailer;
    }

    public function addGuest(int $userId, array $requestData): array | bool
    {
        $errors = $this->guestValidator->validate($requestData);
        if (!empty($errors)) {
            return $errors;
        }
        $hostUser = $this->userRepository->findOneBy(['id' => $userId]);
        $guestUser = $this->userRepository->findOneBy(['email' => $requestData['email']]);

        if (!$guestUser) {
            return ['email' => ['User with this email does not exists.']];
        };
        if (!$hostUser->addGuest($guestUser)) {
            return ['email' => ['User is already on the guests list.']];
        }
        if ($guestUser === $hostUser) {
            return ['email' => ['You cannot invite yourself.']];
        }
        $sendEmail = $this->sendEmail($hostUser, $guestUser);
        if ($sendEmail) {
            return ['email' => 'Unable  to notify guest user via email.'];
        }
        $this->userRepository->save($hostUser);
        return false;
    }

    private function sendEmail($hostUser, $guestUser): array | bool
    {
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
        try {
            $this->mailer->send($email);
            return false;
        } catch (TransportExceptionInterface $e) {
            return [$e->getMessage()];
        }
    }
}
