<?php

namespace App\Infrastructure\Doctrine\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Core\Components\Job\Entity\JobPermission;
use Doctrine\Common\DataFixtures\FixtureInterface;
use App\Infrastructure\Doctrine\DataFixtures\JobFixture;
use App\Infrastructure\Doctrine\Fixture\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Infrastructure\Doctrine\DataFixtures\PermissionFixture;

final class JobPermissionFixture extends Fixture implements FixtureInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $jobPermissionCreateUser = new JobPermission(
            $this->getReference(JobFixture::MANAGER_REFERENCE),
            $this->getReference(PermissionFixture::CREATE_USER_REFERENCE),
        );

        $jobPermissionsCreateTeam = new JobPermission(
            $this->getReference(JobFixture::MANAGER_REFERENCE),
            $this->getReference(PermissionFixture::CREATE_TEAM_REFERENCE),
        );

        $manager->persist($jobPermissionCreateUser);
        $manager->persist($jobPermissionsCreateTeam);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            JobFixture::class,
            PermissionFixture::class,
        );
    }
}
