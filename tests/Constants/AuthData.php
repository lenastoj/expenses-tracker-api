<?php

namespace App\Tests\Constants;

use Symfony\Component\HttpFoundation\Response;

class AuthData
{
    public const LOGIN_DATA = [
        [
            [
                'email' => 'fake@mail.com',
                'password' => 'password',
            ],
            Response::HTTP_BAD_REQUEST,
            [
                'password' => ['Invalid credentials']
            ],
        ],
        [
            [
                'email' => 'pera@mail.com',
                'password' => 'passworddd',
            ],
            Response::HTTP_BAD_REQUEST,
            [
                'password' => ['Invalid credentials']
            ],
        ],
        [
            [
                'email' => 'fake@mail.com',
                'password' => 'passworddd',
            ],
            Response::HTTP_BAD_REQUEST,
            [
                'password' => ['Invalid credentials']
            ],
        ],
        [
            [
                'email' => 'fake@mail.com',
                'password' => 'passworddd',
                'test' => 'test',
            ],
            Response::HTTP_BAD_REQUEST,
            [
                'test' => ['This field was not expected.'],
            ],
        ],
        [
            [],
            Response::HTTP_BAD_REQUEST,
            [
                'email' => ['This field is missing.'],
                'password' => ['This field is missing.'],

            ],
        ],
        [
            [
                'email' => 'pera@mail.com',
                'password' => 'password',
            ],
            Response::HTTP_OK,
        ],
    ];
}