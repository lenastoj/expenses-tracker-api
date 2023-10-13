<?php

namespace App\Application\User;

use App\Dto\User\UserDto;
use App\Repository\UserRepository;
use App\Utils\Pagination;

class IndexGuestService
{
    private Pagination $pagination;
    private UserRepository $userRepository;

    public function __construct(
        Pagination $pagination,
        UserRepository $userRepository,
    ) {
        $this->pagination = $pagination;
        $this->userRepository = $userRepository;
    }

    /**
     * @return array{data: UserDto, metadata: int[]}
     */
    public function getGuests(
        int $userId,
        string|null $searchQuery,
        string|null $sort,
        string|null $sortDirection,
        int $page,
        int $perPage = 5
    ): array {
        if ($page < 1) {
            $page = 1;
        }
        $hostUser = $this->userRepository->findOneBy(['id' => $userId]);

        $query = $this->userRepository->getGuests($hostUser->getId(), $searchQuery, $sort, $sortDirection);
        $guests = $this->pagination->paginate($query, $page, $perPage);
        $totalGuests = $this->userRepository->getGuests(
            $hostUser->getId(),
            $searchQuery,
            $sort,
            $sortDirection,
            true
        );

        $totalGuestsCount = count($totalGuests);
        $totalPages = ceil($totalGuestsCount / $perPage);

        $filteredGuests = array_map(function ($guest) {
            return UserDto::createFromArray($guest);
        }, $guests);
        $metadata = [
            'page' => $page,
            'paginationLimit' => $perPage,
            'count' => count($filteredGuests),
            'total' => $totalGuestsCount,
            'totalPages' => $totalPages,
        ];
        return [
            'data' => $filteredGuests,
            'metadata' => $metadata,
        ];
    }
}
