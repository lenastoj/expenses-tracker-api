<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\Tests\Constants\AuthData;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository|null $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        $this->userRepository = $container->get(UserRepository::class);
    }

    /**
     * @dataProvider provideLoginData
     */
    public function testLogin($requestData, $expectedStatusCode, $expectedErrorMessage = null)
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($requestData)
        );

        $response = $this->client->getResponse();
        $statusCode = $response->getStatusCode();
        $content = $response->getContent();

        $this->assertEquals($expectedStatusCode, $statusCode);
        if ($statusCode === Response::HTTP_OK) {
            $this->assertTrue($response->headers->has('Set-Cookie'));
        }

        if ($statusCode > 300) {
            $decodedContent = json_decode($content, true);
            $this->assertEquals($expectedErrorMessage, $decodedContent, "Error messages mismatch");
        }
    }

    private function provideLoginData(): array
    {
        return AuthData::LOGIN_DATA;
    }

    public function testLogout()
    {
        $testUser = $this->userRepository->findOneBy(['email' => 'pera@mail.com']);
        $this->client->loginUser($testUser);
        $this->client->request(
            'POST',
            '/api/logout',
        );
        $response = $this->client->getResponse();
        $statusCode = $response->getStatusCode();

        $this->assertEquals(Response::HTTP_OK, $statusCode);
    }

    public function testActiveUser()
    {
        $testUser = $this->userRepository->findOneBy(['email' => 'pera@mail.com']);
        $this->client->loginUser($testUser);
        $this->client->request(
            'GET',
            '/api/auth',
        );
        $response = $this->client->getResponse();
        $statusCode = $response->getStatusCode();

        $this->assertEquals(Response::HTTP_OK, $statusCode);
    }
}
