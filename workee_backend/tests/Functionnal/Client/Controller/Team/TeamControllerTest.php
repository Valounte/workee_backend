<?php

namespace App\Tests\Functionnal\Client\Controller\Notification;

use App\Tests\Functionnal\AbstractApiTestCase;
use App\Infrastructure\Team\Repository\TeamRepository;

final class TeamControllerTest extends AbstractApiTestCase
{
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->teamRepository = static::getContainer()->get(TeamRepository::class);
        parent::setUp();
    }

    public function test_create_team(): void
    {
        $body = [
            'name' => 'Team name',
            'description' => 'Team description',
        ];
        $this->client->request(
            'POST',
            '/api/team',
            [],
            [],
            ['HTTP_Authorization' => $this->generateToken()],
            json_encode($body)
        );
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Team created successfully.', $response->message);
    }

    public function test_edit_team(): void
    {
        $teams = $this->teamRepository->findAll();
        $body = [
            'name' => 'Team name',
            'description' => 'la team',
            'id' => $teams[0]->getId(),
        ];
        $this->client->request(
            'PUT',
            '/api/team',
            [],
            [],
            ['HTTP_Authorization' => $this->generateToken()],
            json_encode($body)
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
