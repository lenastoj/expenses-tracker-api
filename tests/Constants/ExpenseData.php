<?php

namespace App\Tests\Constants;

use Symfony\Component\HttpFoundation\Response;

class ExpenseData
{
    public const PRINT_DATA = [
        [
            [
                'sort' => 'amount',
                'order' => 'desc',
            ],
            Response::HTTP_OK,
        ],
        [
            [
                'sort' => 'description',
                'order' => 'asc',
                'word' => 'ample',
            ],
            Response::HTTP_OK,
        ],
        [
            [
                'startDate' => '2023-08-01',
                'endDate' => '2023-08-31',
            ],
            Response::HTTP_OK,
        ],
        [
            [
                'startDate' => '2023-09-01',
                'endDate' => '2023-08-31',
            ],
            Response::HTTP_OK,
        ],
        [
            [],
            Response::HTTP_OK,
        ],
        [
            [
                'week' => 'true',
            ],
            Response::HTTP_OK,
        ],
        [
            [
                'week' => 'false',
            ],
            Response::HTTP_OK,
        ],
    ];
    public const SHOW_DATA = [
        [
            'page' => 1,
            [
                'sort' => 'amount',
                'sortDirection' => 'desc',
            ],
            Response::HTTP_OK,
        ],
        [
            'page' => 1,
            [
                'sort' => 'description',
                'sortDirection' => 'asc',
                'searchQuery' => 'ample',
            ],
            Response::HTTP_OK,
        ],
        [
            'page' => 1,
            [
                'startDate' => '2023-08-01',
                'endDate' => '2023-08-31',
            ],
            Response::HTTP_OK,
        ],
        [
            'page' => 10,
            [],
            Response::HTTP_OK,
            ['message' => 'No expenses'],
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

    public const SHOW_SINGLE_DATA = [
        [
            'id' => 5,
            Response::HTTP_OK,
        ],
        [
            'id' => 1,
            Response::HTTP_NOT_FOUND,
            ['message' => 'Expense not found'],
        ],
        [
            'id' => 'bb',
            Response::HTTP_INTERNAL_SERVER_ERROR,
        ],
    ];
    public const UPDATE_DATA = [
        [
            'id' => 5,
            [
                'date' => '2023-08-02',
                'time' => '10:00:00',
                'description' => 'Update expense again',
                'amount' => 10,
                'comment' => 'Update comment again',
            ],
            Response::HTTP_CREATED,
            ['message' => 'Expense updated successfully'],
        ],
        [
            'id' => 1,
            [
                'date' => '2023-08-02',
                'time' => '10:00:00',
                'description' => 'Update expense again',
                'amount' => 10,
                'comment' => 'Update comment again',
            ],
            Response::HTTP_BAD_REQUEST,
            ['message' => 'Expense not found'],
        ],
        [
            'id' => 13,
            [
                'date' => '2023-08-02',
                'time' => '10:00:00',
                'description' => '',
                'amount' => 10,
                'comment' => 'Update comment again',
            ],
            Response::HTTP_BAD_REQUEST,
            ['description' => ['Description cannot be blank.', 'Description cannot be less than 2 characters.'],
            ],
        ],
    ];

    public const DELETE_DATA = [
        [
            'id' => 1,
            Response::HTTP_NOT_FOUND,
            ['message' => 'Expense not found'],
        ],
        [
            'id' => 20,
            Response::HTTP_OK,
        ],
        [
            'id' => 'bb',
            Response::HTTP_INTERNAL_SERVER_ERROR,
        ],
    ];
    public const CREATE_DATA = [
        [
            [
                'date' => 'invalid_date',
                'time' => '17:00:00',
                'description' => 'Invalid date format',
                'amount' => 50.00,
                'comment' => 'Sample comment',
            ],
            Response::HTTP_BAD_REQUEST,
            [
                'date' => ['This value is not a valid date.'],
            ],
        ],
        [
            [
                'date' => '2023-08-14',
                'time' => '17:00:00',
                'description' => 'i',
                'amount' => 50.00,
                'comment' => 'Sample comment',
            ],
            Response::HTTP_BAD_REQUEST,
            [
                'description' => ['Description cannot be less than 2 characters.'],
            ],
        ],
        [
            [
                'date' => '2023-08-14',
                'time' => '17:00:00',
                'description' => 'i',
                'amount' => -50.00,
                'comment' => '',
            ],
            Response::HTTP_BAD_REQUEST,
            [
                'description' => ['Description cannot be less than 2 characters.'],
                'amount' => ['Amount must be a positive number.'],
            ],
        ],
        [
            [
                'date' => '2023-08-14',
                'time' => '17:00:',
                'description' => 'description',
                'amount' => 50.00,
                'comment' => 'comment',
            ],
            Response::HTTP_BAD_REQUEST,
            [
                'time' => ['This value is not a valid time.'],
            ],
        ],
        [
            [
                'date' => '2023-08-14',
                'time' => '17:00:',
                'description' => 'description',
                'amount' => 50.00,
            ],
            Response::HTTP_BAD_REQUEST,
            [
                'time' => ['This value is not a valid time.'],
                'comment' => ['This field is missing.'],
            ],
        ],
        [
            [],
            Response::HTTP_BAD_REQUEST,
            [
                'date' => ['This field is missing.'],
                'time' => ['This field is missing.'],
                'description' => ['This field is missing.'],
                'amount' => ['This field is missing.'],
                'comment' => ['This field is missing.'],
            ],
        ],
        [
            [
                'date' => '2023-08-02',
                'time' => '10:00:00',
                'description' => 'Sample expense',
                'amount' => 10,
                'comment' => 'Sample comment',
            ],
            Response::HTTP_CREATED,
            [
                'message' => 'Expense created successfully',
            ],
        ],
    ];
}
