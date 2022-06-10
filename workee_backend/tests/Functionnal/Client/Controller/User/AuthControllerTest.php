<?php

namespace App\Tests\Functionnal\Client\Controller\User;

use App\Tests\Functionnal\Services\LoginService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
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

    public function test_invite_user()
    {
        $bodyInvite = [
            'email' => 'workeetech@gmail.com',
            'firstname' => 'work',
            'lastname' => 'EE',
        ];

        $response = $this->client->request(
            'POST',
            '/api/invite/user',
            [],
            [],
            array('HTTP_Authorization' => LoginService::generateToken()),
            json_encode($bodyInvite)
        );

        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('User successfully invited !', $response->message);
    }


    public function test_register_and_login(): void
    {
        $company = $this->companyRepository->findOneByName('Instapro');

        $bodyRegister = [
            'email' => 'workeetech@gmail.com',
            'password' => 'Password123456!',
            'company' => $company->getId(),
            'firstname' => 'jéjé',
            'lastname' => 'LeBeauf',
        ];

        $bodyLogin = [
            'email' => 'workeetech@gmail.com',
            'password' => 'Password123456!',
        ];

        $this->client->request(
            'POST',
            '/api/user',
            [],
            [],
            [],
            json_encode($bodyRegister)
        );

        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('User successfully created !', $response->message);

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

    /**
     * @dataProvider registrationProvider
     */
    public function test_bad_registration_and_bad_login(string $password, string $email): void
    {
        $company = $this->companyRepository->findOneByName('Instapro');

        $bodyRegister = [
            'email' => $email,
            'password' => $password,
            'company' => $company->getId(),
            'firstname' => 'jéjé',
            'lastname' => 'LeBeauf',
        ];

        $this->client->request(
            'POST',
            '/api/user',
            [],
            [],
            [],
            json_encode($bodyRegister)
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());

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

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function registrationProvider(): array
    {
        return [
            'bad_email' => [
                'password' => 'Password123456!',
                'email' => 'valouuuuuuuuu',
            ],
            'bad_password' => [
                'password' => '123',
                'email' => 'val@gmail.com',
            ],
            'user already exist' => [
                'password' => 'Password123456!',
                'email' => 'workee@gmail.com',
            ],
        ];
    }
}
