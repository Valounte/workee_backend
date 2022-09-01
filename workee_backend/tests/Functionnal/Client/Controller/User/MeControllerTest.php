<?php

namespace App\Tests\Functionnal\Client\Controller\User;

use App\Infrastructure\User\Repository\UserRepository;
use App\Infrastructure\Company\Repository\CompanyRepository;
use App\Tests\Functionnal\AbstractApiTestCase;

final class MeControllerTest extends AbstractApiTestCase
{
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        parent::setUp();
    }

    public function test_get_me(): void
    {
        $this->client->request(
            'POST',
            '/api/me',
            [],
            [],
            ['HTTP_Authorization' => $this->generateToken()],
        );
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
