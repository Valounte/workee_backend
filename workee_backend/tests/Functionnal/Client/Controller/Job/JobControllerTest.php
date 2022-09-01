<?php

namespace App\Tests\Functionnal\Client\Controller\User;

use App\Infrastructure\Job\Repository\JobRepository;
use App\Infrastructure\Job\Repository\PermissionRepository;
use App\Tests\Functionnal\AbstractApiTestCase;

final class JobControllerTest extends AbstractApiTestCase
{
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->permissionRepository = static::getContainer()->get(PermissionRepository::class);
        $this->jobRepository = static::getContainer()->get(JobRepository::class);
        parent::setUp();
    }

    public function test_create_job(): void
    {
        $permissions = $this->permissionRepository->findAll();

        $body = [
            'name' => 'Lead developerdzad',
            'permissionsId' => [
                $permissions[0]->getId(),
            ],
        ];
        $this->client->request(
            'POST',
            '/api/job',
            [],
            [],
            ['HTTP_Authorization' => $this->generateToken()],
            json_encode($body)
        );
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Job created', $response->message);
    }

    public function test_get_jobs(): void
    {
        $this->client->request(
            'GET',
            '/api/jobs',
            [],
            [],
            ['HTTP_Authorization' => $this->generateToken()]
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function test_modify_job(): void
    {
        $job = $this->jobRepository->findAll();
        $permissions = $this->permissionRepository->findAll();

        $body = [
            "jobId" => $job[0]->getId(),
            'name' => 'Lead dev',
            'permissionsId' => [
                $permissions[0]->getId(),
            ],
        ];
        $this->client->request(
            'PUT',
            '/api/job',
            [],
            [],
            ['HTTP_Authorization' => $this->generateToken()],
            json_encode($body)
        );
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Job modified', $response->message);
    }
}
