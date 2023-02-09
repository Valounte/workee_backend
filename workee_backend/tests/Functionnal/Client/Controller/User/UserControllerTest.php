<?php

namespace App\Tests\Functionnal\Client\Controller\User;

use App\Infrastructure\User\Repository\UserRepository;
use App\Infrastructure\Company\Repository\CompanyRepository;
use App\Infrastructure\Team\Repository\TeamRepository;
use App\Tests\Functionnal\AbstractApiTestCase;

final class UserControllerTest extends AbstractApiTestCase
{
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->teamRepository = static::getContainer()->get(TeamRepository::class);
        parent::setUp();
    }

    public function test_set_picture(): void
    {
        $body = [
            "picture" => "https://test.com",
        ];

        $this->client->request(
            'POST',
            '/api/user/picture',
            [],
            [],
            ['HTTP_Authorization' => $this->generateToken()],
            json_encode($body),
        );
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Picture updated', $response->message);
    }

    public function test_add_user_to_team(): void
    {
        $teams = $this->teamRepository->findAll();
        $users = $this->userRepository->findALl();

        $body = [
            "userId" => $users[0]->getId(),
            "teamId" => $teams[0]->getId(),
        ];

        $this->client->request(
            'POST',
            'api/add-to-team',
            [],
            [],
            ['HTTP_Authorization' => $this->generateToken()],
            json_encode($body),
        );
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('user successfully added to the team !', $response->message);
    }
}
