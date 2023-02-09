<?php

namespace App\Tests\Functionnal\Client\Controller\User;

use App\Infrastructure\User\Repository\UserRepository;
use App\Infrastructure\Company\Repository\CompanyRepository;
use App\Tests\Functionnal\AbstractApiTestCase;

final class AuthControllerTest extends AbstractApiTestCase
{
    private CompanyRepository $companyRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->companyRepository = static::getContainer()->get(CompanyRepository::class);
        $this->userRepository = static::getContainer()->get(UserRepository::class);

        parent::setUp();
    }

    public function test_register_and_login(): void
    {
        $bodyLogin = [
            'email' => 'workee@gmail.com',
            'password' => 'Password123!',
        ];

        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            [],
            json_encode($bodyLogin)
        );

        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('success!', $response->message);
    }

    public function test_bad_login(): void
    {
        $bodyLogin = [
            'email' => "bad@gmail.com",
            'password' => "222222",
        ];

        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            [],
            json_encode($bodyLogin)
        );

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }
}
