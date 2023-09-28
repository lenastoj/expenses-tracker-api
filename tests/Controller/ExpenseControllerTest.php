<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\Tests\Constants\ExpenseData;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ExpenseControllerTest extends WebTestCase
{
    private UserRepository|null $userRepository;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        $this->userRepository = $container->get(UserRepository::class);
    }

    private function responseCheck($response, $expectedStatusCode, $expectedErrorMessage): void
    {
        $statusCode = $response->getStatusCode();
        $content = $response->getContent();

        $this->assertEquals($expectedStatusCode, $statusCode);
        if ($statusCode > 300) {
            $decodedContent = json_decode($content, true);
            $this->assertEquals($expectedErrorMessage, $decodedContent, "Error messages mismatch");
        }
    }

    /**
     * @dataProvider provideCreateData
     */
    public function testCreateExpense(
        $requestData,
        $expectedStatusCode,
        $expectedErrorMessage = null
    ): void {
        $testUser = $this->userRepository->findOneBy(['email' => 'pera@mail.com']);
        $this->client->loginUser($testUser);

        $this->client->request(
            'POST',
            '/api/expenses',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($requestData)
        );

        $response = $this->client->getResponse();
        $this->responseCheck($response, $expectedStatusCode, $expectedErrorMessage);
    }

    public function provideCreateData(): array
    {
        return ExpenseData::CREATE_DATA;
    }

    /**
     * @dataProvider provideDeleteData
     */
    public function testDeleteExpense(
        $id,
        $expectedStatusCode,
        $expectedErrorMessage = null
    ): void {
        $testUser = $this->userRepository->findOneBy(['email' => 'pera@mail.com']);
        $this->client->loginUser($testUser);
        $this->client->request(
            'DELETE',
            '/api/expenses/' . $id,
        );

        $response = $this->client->getResponse();
        $this->responseCheck($response, $expectedStatusCode, $expectedErrorMessage);
    }

    public function provideDeleteData(): array
    {
        return ExpenseData::DELETE_DATA;
    }

    /**
     * @dataProvider provideUpdateData
     */
    public function testUpdateExpense(
        $id,
        $requestData,
        $expectedStatusCode,
        $expectedErrorMessage = null
    ): void {
        $testUser = $this->userRepository->findOneBy(['email' => 'pera@mail.com']);
        $this->client->loginUser($testUser);
        $this->client->request(
            'PUT',
            '/api/expenses/' . $id,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($requestData)
        );
        $response = $this->client->getResponse();
        $this->responseCheck($response, $expectedStatusCode, $expectedErrorMessage);
    }

    public function provideUpdateData(): array
    {
        return ExpenseData::UPDATE_DATA;
    }

    /**
     * @dataProvider provideShowSingleData
     */
    public function testShowSingleExpense(
        $id,
        $expectedStatusCode,
        $expectedErrorMessage = null
    ): void {
        $testUser = $this->userRepository->findOneBy(['email' => 'pera@mail.com']);
        $this->client->loginUser($testUser);
        $this->client->request(
            'GET',
            '/api/expenses/' . $id,
        );

        $response = $this->client->getResponse();
        $this->responseCheck($response, $expectedStatusCode, $expectedErrorMessage);
    }

    public function provideShowSingleData(): array
    {
        return ExpenseData::SHOW_SINGLE_DATA;
    }

    /**
     * @dataProvider provideShowExpensesData
     */
    public function testShowExpenses(
        $page,
        $params,
        $expectedStatusCode,
        $expectedErrorMessage = null
    ): void {
        $testUser = $this->userRepository->findOneBy(['email' => 'pera@mail.com']);
        $this->client->loginUser($testUser);

        $filters = "?page=$page";
        foreach ($params as $key => $value) {
            $filters = $filters . "&$key=$value";
        }

        dump($filters);
        $this->client->request(
            'GET',
            '/api/expenses' . $filters,
        );
        $response = $this->client->getResponse();
        $this->responseCheck($response, $expectedStatusCode, $expectedErrorMessage);
    }

    public function provideShowExpensesData(): array
    {
        return ExpenseData::SHOW_DATA;
    }


    /**
     * @dataProvider providePrintExpensesData
     */
    public function testPrintExpenses(
        $params,
        $expectedStatusCode,
        $expectedErrorMessage = null
    ): void {
        $testUser = $this->userRepository->findOneBy(['email' => 'pera@mail.com']);
        $this->client->loginUser($testUser);

        $filters = "?";
        foreach ($params as $key => $value) {
            if (array_key_first($params) === $key) {
                $filters = $filters . "$key=$value";
            } else {
                $filters = $filters . "&$key=$value";
            }
        }

        dump($filters);
        $this->client->request(
            'GET',
            '/api/expenses/print' . $filters,
        );
        $response = $this->client->getResponse();
        $this->responseCheck($response, $expectedStatusCode, $expectedErrorMessage);
    }

    public function providePrintExpensesData(): array
    {
        return ExpenseData::PRINT_DATA;
    }
}
