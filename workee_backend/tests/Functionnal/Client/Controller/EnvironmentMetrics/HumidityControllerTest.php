<?php

namespace App\Tests\Functionnal\Client\Controller\EnvironmentMetrics;

use App\Tests\Functionnal\AbstractApiTestCase;
use App\Infrastructure\Job\Repository\JobRepository;
use App\Infrastructure\User\Repository\UserRepository;
use App\Infrastructure\Job\Repository\PermissionRepository;

final class HumidityControllerTest extends AbstractApiTestCase
{
    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    public function test_post_humidity(): void
    {
        $body = [
            'value' => '50',
        ];

        $this->client->request(
            'POST',
            '/api/humidity',
            [],
            [],
            ['HTTP_Authorization' => $this->generateToken()],
            json_encode($body)
        );
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Data stored', $response->message);
    }
}
