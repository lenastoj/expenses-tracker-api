<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Constants\UserData;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository|null $userRepository;

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
    private function loginUser(): User | null
    {
        $testUser = $this->userRepository->findOneBy(['email' => 'pera@mail.com']);
        $this->client->loginUser($testUser);
        return $testUser;
    }

    /**
     * @dataProvider provideAddGuestData
     */
    public function testAddGuestUser(
        $requestData,
        $expectedStatusCode,
        $expectedErrorMessage = null
    ) {
        $this->loginUser();
        $this->client->request(
            'POST',
            '/api/guest',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($requestData)
        );

        $response = $this->client->getResponse();
        $this->responseCheck($response, $expectedStatusCode, $expectedErrorMessage);
    }
    private function provideAddGuestData(): array
    {
        return UserData::ADD_DATA;
    }

    /**
     * @dataProvider provideRemoveGuestData
     */
    public function testRemoveGuestUser(
        $id,
        $expectedStatusCode,
        $expectedErrorMessage = null
    ) {
        $this->loginUser();
        $this->client->request(
            'DELETE',
            '/api/guest/' . $id,
        );
        $response = $this->client->getResponse();
        $this->responseCheck($response, $expectedStatusCode, $expectedErrorMessage);
    }
    private function provideRemoveGuestData(): array
    {
        return UserData::REMOVE_GUEST_DATA;
    }

    /**
     * @dataProvider provideRemoveHostData
     */
    public function testRemoveHostUser(
        $id,
        $expectedStatusCode,
        $expectedErrorMessage = null
    ) {
        $this->loginUser();
        $this->client->request(
            'DELETE',
            '/api/host/' . $id,
        );
        $response = $this->client->getResponse();
        $this->responseCheck($response, $expectedStatusCode, $expectedErrorMessage);
    }
    private function provideRemoveHostData(): array
    {
        return UserData::REMOVE_HOST_DATA;
    }

    /**
     * @dataProvider provideIndexGuestData
     */
    public function testIndexGuest(
        $page,
        $params,
        $expectedStatusCode,
        $expectedErrorMessage = null
    ) {
        $this->loginUser();
        $filters = "?page=$page";
        foreach ($params as $key => $value) {
            $filters = $filters . "&$key=$value";
        }
        $this->client->request(
            'GET',
            '/api/guest' . $filters,
        );
        $response = $this->client->getResponse();
        $this->responseCheck($response, $expectedStatusCode, $expectedErrorMessage);
    }
    private function provideIndexGuestData(): array
    {
        return UserData::SHOW_DATA;
    }
}
