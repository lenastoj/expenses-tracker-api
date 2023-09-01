<?php

namespace App\Tests\Constants;

use Symfony\Component\HttpFoundation\Response;

class ExpenseData
{
    public const SHOW_DATA = [
        [
            'page' => 1,
            Response::HTTP_OK,
        ],
        [
            'page' => 10,
            Response::HTTP_NOT_FOUND,
            ['message' => 'No expenses'],
        ],
        [
            'page' => 2,
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
    ];
    public const UPDATE_DATA = [
        [
            'id' => 5,
            [
                'date' => '2023-08-02',
                'time' => '10:00:00',
                'description' => 'Update expense',
                'amount' => 10,
                'comment' => 'Update comment'
            ],
            Response::HTTP_OK,
            ['message' => 'Expense updated successfully'],
        ],
        [
            'id' => 1,
            [
                'date' => '2023-08-02',
                'time' => '10:00:00',
                'description' => 'Update expense',
                'amount' => 10,
                'comment' => 'Update comment'
            ],
            Response::HTTP_NOT_FOUND,
            ['message' => 'Expense not found'],
        ],
    ];

    public const DELETE_DATA = [
        [
            'id' => 2,
            Response::HTTP_NOT_FOUND,
            [
                'message' => 'Expense not found'
            ]
        ],
        [
            'id' => 12,
            Response::HTTP_OK,
        ],
    ];
    public const CREATE_DATA = [
        [
            [
                'date' => 'invalid_date',
                'time' => '17:00:00',
                'description' => 'Invalid date format',
                'amount' => 50.00,
                'comment' => 'Sample comment'
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
                'comment' => 'Sample comment'
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
                'comment' => ''
            ],
            Response::HTTP_BAD_REQUEST,
            [
                'description' => ['Description cannot be less than 2 characters.'],
                'amount' => ['Amount must be a positive number.']
            ],
        ],
        [
            [
                'date' => '2023-08-14',
                'time' => '17:00:',
                'description' => 'description',
                'amount' => 50.00,
                'comment' => 'comment'
            ],
            Response::HTTP_BAD_REQUEST,
            [
                'time' => ['This value is not a valid time.'],
            ]
        ],
        [
            [
                'date' => '2023-08-14',
                'time' => '17:00:',
                'description' => 'description',
                'amount' => 50.00
            ],
            Response::HTTP_BAD_REQUEST,
            [
                'time' => ['This value is not a valid time.'],
                'comment' => ['This field is missing.'],
            ]
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
            ]
        ],
        [
            [
                'date' => '2023-08-02',
                'time' => '10:00:00',
                'description' => 'Sample expense',
                'amount' => 10,
                'comment' => 'Sample comment'
            ],
            Response::HTTP_CREATED,
            [
                'message' => 'Expense created successfully'
            ]
        ],
    ];
}
