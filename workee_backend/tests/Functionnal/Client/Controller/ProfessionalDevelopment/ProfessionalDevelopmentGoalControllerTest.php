<?php

namespace App\Tests\Functionnal\Client\Controller\ProfessionalDevelopment;

use App\Core\Components\ProfessionalDevelopment\Repository\ProfessionalDevelopmentGoalRepositoryInterface;
use App\Tests\Functionnal\AbstractApiTestCase;

final class ProfessionalDevelopmentGoalControllerTest extends AbstractApiTestCase
{
    private ProfessionalDevelopmentGoalRepositoryInterface $professionalDevelopmentGoalRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->professionalDevelopmentGoalRepository = self::getContainer()->get(ProfessionalDevelopmentGoalRepositoryInterface::class);
        parent::setUp();
    }

    public function test_create_goal(): void
    {
        $body = [
            "goal" => "Apprendre de nouveaux frameworks",
            "startDate" => "2024-12-26",
            "endDate" => "2025-12-26"
        ];

        $this->client->request(
            'POST',
            '/api/professional-development-goal',
            [],
            [],
            ['HTTP_Authorization' => $this->generateToken()],
            json_encode($body),
        );
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Goal created successfully', $response->message);
    }
}
