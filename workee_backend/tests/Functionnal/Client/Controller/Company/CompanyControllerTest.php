<?php

namespace App\Tests\Functionnal\Client\Controller\Company;

use App\Tests\Functionnal\AbstractApiTestCase;

final class CompanyControllerTest extends AbstractApiTestCase
{
    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    public function test_create_company(): void
    {
        $body = [
            'name' => 'Travaux.com',
        ];

        $this->client->request(
            'POST',
            '/api/company',
            [],
            [],
            ['HTTP_Authorization' => $this->generateToken()],
            json_encode($body)
        );
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Company created successfully.', $response->message);
    }
}
