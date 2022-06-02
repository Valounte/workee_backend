<?php

namespace App\Tests\Functionnal;

use App\DataFixtures\UserFixture;
use App\DataFixtures\CompanyFixture;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

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

    private function resetDatabaseAndLoadFixtures(): void
    {
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $loader = new Loader();
        $loader->addFixture(new UserFixture());
        $loader->addFixture(new CompanyFixture());

        $purger = new ORMPurger($this->em);

        $executor = new ORMExecutor($this->em, $purger);

        self::ensureKernelShutdown();
        $executor->execute($loader->getFixtures());
    }
}
