<?php

namespace App\Tests\Unit\Infrastructure\User\Services;

use App\Tests\Unit\AbstractTestCase;
use App\Infrastructure\User\Repository\UserRepository;
use App\Infrastructure\User\Exceptions\UserInformationException;
use App\Infrastructure\User\Services\CheckUserInformationService;
use App\Tests\Unit\StubUserFactory;

final class CheckUserInformationServiceTest extends AbstractTestCase
{
    private CheckUserInformationService $service;

    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->service = new CheckUserInformationService($this->userRepository);

        parent::setUp();
    }

    public function test_email_already_used(): void
    {
        $this->userRepository->method('findUserByEmail')->willReturn(StubUserFactory::create(1));
        $this->expectException(UserInformationException::class);
        $this->expectExceptionMessage('Email already used');
        $this->expectExceptionCode(400);

        $this->service->checkUserInformation('test@gmail.com');
    }

    public function test_invalid_email(): void
    {
        $this->userRepository->method('findUserByEmail')->willReturn(null);
        $this->expectException(UserInformationException::class);
        $this->expectExceptionMessage('Invalid email');
        $this->expectExceptionCode(400);

        $this->service->checkUserInformation('test');
    }

    /**
     * @dataProvider invalidPasswordProvider
     */
    public function test_invalid_password(string $password): void
    {
        $this->userRepository->method('findUserByEmail')->willReturn(null);
        $this->expectException(UserInformationException::class);
        $this->expectExceptionMessage('Invalid password format');
        $this->expectExceptionCode(400);

        $this->service->checkUserInformation('test@gmail.com', $password);
    }

    public function test_valid_information(): void
    {
        $this->userRepository->method('findUserByEmail')->willReturn(null);

        try {
            $this->service->checkUserInformation('test@gmail.com', 'Password123!');
        } catch (UserInformationException $e) {
            $this->fail('Unexpected exception');
        }

        $this->assertTrue(true);
    }

    public function invalidPasswordProvider(): array
    {
        return [
            [''], // mot de passe vide
            ['a'], // mot de passe trop court
            ['password'], // mot de passe trop commun
            ['123456'], // mot de passe trop simple
            ['test1234'], // mot de passe n'a pas de lettre majuscule
            ['TEST1234'], // mot de passe n'a pas de lettre minuscule
            ['testPASSWORD'], // mot de passe n'a pas de chiffre
        ];
    }
}
