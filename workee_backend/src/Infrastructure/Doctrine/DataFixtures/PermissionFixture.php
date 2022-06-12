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
    public const CREATE_USER_REFERENCE = 'create_user';
    public const CREATE_TEAM_REFERENCE = 'create_team';

    public function load(ObjectManager $manager): void
    {
        $permissionCreateUser = new Permission();
        $permissionCreateUser->setName(PermissionNameEnum::CREATE_USER);
        $permissionCreateUser->setContext(PermissionContextEnum::USER);

        $permissionCreateTeam = new Permission();
        $permissionCreateTeam->setName(PermissionNameEnum::CREATE_TEAM);
        $permissionCreateTeam->setContext(PermissionContextEnum::TEAM);

        $this->addReference(self::CREATE_USER_REFERENCE, $permissionCreateUser);
        $this->addReference(self::CREATE_TEAM_REFERENCE, $permissionCreateTeam);

        $manager->persist($permissionCreateTeam);
        $manager->persist($permissionCreateUser);
        $manager->flush();
    }
}
