<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Core\Components\User\Entity\User;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Infrastructure\User\Repository\UserRepository;

abstract class AbstractTestCase extends TestCase
{
    private UserRepositoryInterface $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        parent::setUp();
    }

    protected function getUser(): User
    {
    }
}
