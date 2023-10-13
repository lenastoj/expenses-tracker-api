<?php

namespace App\Application\User;

use App\Repository\UserRepository;

class RemoveGuestUserService
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository,
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * @return bool
     */
    public function removeGuest(int $userId, int $id): bool
    {
        $hostUser = $this->userRepository->findOneBy(['id' => $userId]);
        $guestUser = $this->userRepository->findOneBy(['id' => $id]);

        if (!$guestUser || !$hostUser->removeGuest($guestUser)) {
            return false;
        };

        $this->userRepository->save($hostUser);
        return true;
    }
}
