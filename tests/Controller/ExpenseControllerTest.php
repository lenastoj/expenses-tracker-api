<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExpenseControllerTest extends WebTestCase
{
    /**
     * @dataProvider provideTestData
     */
    public function testCreateExpense($requestData, $expectedStatusCode, $expectedErrorMessage = null): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/expenses',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($requestData)
        );

        $response = $client->getResponse();
        $statusCode = $response->getStatusCode();
        $content = $response->getContent();

        $this->assertEquals($expectedStatusCode, $statusCode);
        if ($statusCode === 400) {
            $decodedContent = json_decode($content, true);
            $this->assertEquals($expectedErrorMessage, $decodedContent, "Error messages mismatch");
        }
    }

    public function provideTestData(): array
    {
        return [
            [
                [
                    'date' => '2023-08-14',
                    'time' => '16:00:00',
                    'description' => 'Sample expense',
                    'amount' => 100.00,
                    'comment' => 'Sample comment'
                ],
                200,
                [
                    'message' => 'Expense created successfully'
                ]
            ],
            [
                [
                    'date' => 'invalid_date',
                    'time' => '17:00:00',
                    'description' => 'Invalid date format',
                    'amount' => 50.00,
                    'comment' => 'Sample comment'
                ],
                400,
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
                400,
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
                400,
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
                400,
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
                400,
                [
                    'time' => ['This value is not a valid time.'],
                    'comment' => ['This field is missing.'],
                ]
            ],
            [
                [],
                400,
                [
                    'date' => ['This field is missing.'],
                    'time' => ['This field is missing.'],
                    'description' => ['This field is missing.'],
                    'amount' => ['This field is missing.'],
                    'comment' => ['This field is missing.'],
                ]
            ],
        ];
    }
}
