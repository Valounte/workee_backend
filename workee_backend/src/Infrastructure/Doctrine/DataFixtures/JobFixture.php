<?php

namespace App\Infrastructure\Doctrine\DataFixtures;

use App\Core\Components\Job\Entity\Job;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use App\Infrastructure\Doctrine\Fixture\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Infrastructure\Doctrine\DataFixtures\CompanyFixture;

final class JobFixture extends Fixture implements FixtureInterface, DependentFixtureInterface
{
    public const MANAGER_REFERENCE = 'manager';

    public function load(ObjectManager $manager): void
    {
        $job = new Job(
            'Manager',
            "Manager's job",
            $this->getReference(CompanyFixture::INSTAPRO_REFERENCE),
        );

        $this->addReference(self::MANAGER_REFERENCE, $job);

        $manager->persist($job);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            CompanyFixture::class,
        );
    }
}
