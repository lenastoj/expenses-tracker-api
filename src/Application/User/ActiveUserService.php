<?php

namespace App\Application\User;

use App\Dto\User\UserDto;
use App\Repository\UserRepository;

class ActiveUserService
{
    private UserRepository $userRepository;
    public function __construct(
        UserRepository $userRepository,
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * @return array{user: UserDto, hosts: UserDto[]}
     */
    public function getActiveUser(int $userId): array
    {
        $hostUser = $this->userRepository->findOneBy(['id' => $userId]);
        $hosts = $hostUser->getHosts()->getValues();
        $filteredHosts = array_map(function ($user) {
            return UserDto::createFromEntity($user);
        }, $hosts);

        return [
            'user' => UserDto::createFromEntity($hostUser),
            'hosts' => $filteredHosts,
        ];
    }
}
