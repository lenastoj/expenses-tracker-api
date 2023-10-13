<?php

namespace App\Tests\Constants;

use Symfony\Component\HttpFoundation\Response;

class UserData
{
    public const ADD_DATA = [
        [
            ['email' => 'joca@mail.com',],
            Response::HTTP_CREATED,
        ],
        [
            ['email' => 'lena@mail.com',],
            Response::HTTP_CREATED,
        ],
        [
            ['email' => 'notrealuser@mail.com',],
            Response::HTTP_BAD_REQUEST,
            [
                'email' => ['User with this email does not exists.'],
            ]
        ],
        [
            ['email' => 'pera@mail.com',],
            Response::HTTP_BAD_REQUEST,
            [
                'email' => ['You cant not invite yourself.'],
            ]
        ],
        [
            ['email' => 'pera',],
            Response::HTTP_BAD_REQUEST,
            [
                'email' => ['This value is not a valid email address.'],
            ]
        ],
    ];

    public const REMOVE_GUEST_DATA = [
        [
            'id' => 2,
            Response::HTTP_OK,
        ],
        [
            'id' => 50,
            Response::HTTP_CONFLICT,
            ['message' => 'User is not on the guest list']
        ],
    ];

    public const REMOVE_HOST_DATA = [
        [
            'id' => 2,
            Response::HTTP_OK,
        ],
        [
            'id' => 50,
            Response::HTTP_CONFLICT,
            ['message' => 'User is not on the hosts list']
        ],
    ];

    public const SHOW_DATA = [
        [
            'page' => 1,
            [
                'sort' => 'firstName',
                'sortDirection' => 'desc',
            ],
            Response::HTTP_OK,
        ],
        [
            'page' => 1,
            [
                'sort' => 'firstName',
                'sortDirection' => 'asc',
                'searchQuery' => 'joca',
            ],
            Response::HTTP_OK,
        ],
        [
            'page' => 10,
            [],
            Response::HTTP_OK,
        ],
        [
            'page' => 2,
            [],
            Response::HTTP_OK,
        ],
        [
            'page' => 50,
            [],
            Response::HTTP_OK,
        ],
    ];
}
