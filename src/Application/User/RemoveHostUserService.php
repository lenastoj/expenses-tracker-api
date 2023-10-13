<?php

namespace App\Application\User;

use App\Repository\UserRepository;

class RemoveHostUserService
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
    public function removeHost(int $userId, int $id): bool
    {
        $hostUser = $this->userRepository->findOneBy(['id' => $userId]);
        $guestUser = $this->userRepository->findOneBy(['id' => $id]);

        if (!$guestUser || !$guestUser->removeGuest($hostUser)) {
            return false;
        };

        $this->userRepository->save($guestUser);
        return true;
    }
}
