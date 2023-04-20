<?php

namespace App\Tests\Functionnal;

use App\Core\Components\Job\Entity\JobPermission;
use App\Core\Components\User\Entity\UserTeam;
use Firebase\JWT\JWT;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Infrastructure\User\Repository\UserRepository;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use App\Infrastructure\Doctrine\DataFixtures\JobFixture;
use App\Infrastructure\Doctrine\DataFixtures\UserFixture;
use App\Infrastructure\Doctrine\DataFixtures\CompanyFixture;
use App\Infrastructure\Doctrine\DataFixtures\GoalFixture;
use App\Infrastructure\Doctrine\DataFixtures\JobPermissionFixture;
use App\Infrastructure\Doctrine\DataFixtures\NotificationFixture;
use App\Infrastructure\Doctrine\DataFixtures\PermissionFixture;
use App\Infrastructure\Doctrine\DataFixtures\TeamFixture;
use App\Infrastructure\Doctrine\DataFixtures\UserTeamFixture;
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
        $loader->addFixture(new JobFixture());
        $loader->addFixture(new TeamFixture());
        $loader->addFixture(new PermissionFixture());
        $loader->addFixture(new JobPermissionFixture());

        $purger = new ORMPurger($this->em);

        $executor = new ORMExecutor($this->em, $purger);

        $executor->execute($loader->getFixtures());
        self::ensureKernelShutdown();
    }
}
