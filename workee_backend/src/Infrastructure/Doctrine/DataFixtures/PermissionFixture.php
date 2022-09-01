<?php

namespace App\Infrastructure\Doctrine\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Core\Components\Job\Entity\Permission;
use Doctrine\Common\DataFixtures\FixtureInterface;
use App\Infrastructure\Doctrine\Fixture\AbstractFixture;
use App\Core\Components\Job\Entity\Enum\PermissionNameEnum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Core\Components\Job\Entity\Enum\PermissionContextEnum;

final class PermissionFixture extends Fixture implements FixtureInterface
{
    public const CREATE_USER_REFERENCE = 'CREATE_USER';
    public const CREATE_TEAM_REFERENCE = 'CREATE_TEAM';
    public const CREATE_JOB_REFERENCE = 'CREATE_JOB';

    public function load(ObjectManager $manager): void
    {
        $permissionCreateUser = new Permission();
        $permissionCreateUser->setName(PermissionNameEnum::CREATE_USER);
        $permissionCreateUser->setContext(PermissionContextEnum::USER);

        $permissionCreateTeam = new Permission();
        $permissionCreateTeam->setName(PermissionNameEnum::CREATE_TEAM);
        $permissionCreateTeam->setContext(PermissionContextEnum::TEAM);

        $permissionCreateJob = new Permission();
        $permissionCreateJob->setName(PermissionNameEnum::CREATE_JOB);
        $permissionCreateJob->setContext(PermissionContextEnum::TEAM);

        $this->addReference(self::CREATE_USER_REFERENCE, $permissionCreateUser);
        $this->addReference(self::CREATE_TEAM_REFERENCE, $permissionCreateTeam);
        $this->addReference(self::CREATE_JOB_REFERENCE, $permissionCreateJob);

        $manager->persist($permissionCreateTeam);
        $manager->persist($permissionCreateUser);
        $manager->persist($permissionCreateJob);
        $manager->flush();
    }
}
