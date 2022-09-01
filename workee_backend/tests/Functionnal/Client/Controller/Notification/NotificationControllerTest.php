<?php

namespace App\Tests\Functionnal\Client\Controller\Notification;

use App\Infrastructure\Job\Repository\JobRepository;
use App\Infrastructure\Job\Repository\PermissionRepository;
use App\Infrastructure\User\Repository\UserRepository;
use App\Tests\Functionnal\AbstractApiTestCase;

final class NotificationControllerTest extends AbstractApiTestCase
{
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->permissionRepository = static::getContainer()->get(PermissionRepository::class);
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        parent::setUp();
    }

    public function test_send_notification(): void
    {
        $users = $this->userRepository->findAll();

        $body = [
            'message' => 'Notification content',
            'recepteurId' => $users[0]->getId(),
            'alertLevel' => "important",

        ];
        $this->client->request(
            'POST',
            '/api/send-notification',
            [],
            [],
            ['HTTP_Authorization' => $this->generateToken()],
            json_encode($body)
        );
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Notification sent', $response->message);
    }
}
