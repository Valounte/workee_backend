<?php

namespace App\Tests\Functionnal;

use Firebase\JWT\JWT;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Infrastructure\User\Repository\UserRepository;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use App\Infrastructure\Doctrine\DataFixtures\UserFixture;
use App\Infrastructure\Doctrine\DataFixtures\CompanyFixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class AbstractApiTestCase extends WebTestCase
{
    protected function setUp(): void
    {
        $this->resetDatabaseAndLoadFixtures();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->resetDatabaseAndLoadFixtures();
        parent::tearDown();
    }

    public static function generateToken(): string
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findUserByEmail('workee@gmail.com');

        $jwt = JWT::encode(
            ["id" => $user->getId(), "company" => $user->getCompany()->getId()],
            'jwt_secret',
            'HS256'
        );

        return sprintf('Bearer %s', $jwt);
    }

    private function resetDatabaseAndLoadFixtures(): void
    {
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $loader = new Loader();
        $loader->addFixture(new UserFixture(static::getContainer()->get(UserPasswordHasherInterface::class)));
        $loader->addFixture(new CompanyFixture());

        $purger = new ORMPurger($this->em);

        $executor = new ORMExecutor($this->em, $purger);

        self::ensureKernelShutdown();
        $executor->execute($loader->getFixtures());
    }
}
