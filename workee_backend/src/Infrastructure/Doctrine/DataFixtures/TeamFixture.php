<?php

namespace App\Infrastructure\Doctrine\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use App\Core\Components\Team\Entity\Team;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Core\Components\Job\Entity\Permission;
use Doctrine\Common\DataFixtures\FixtureInterface;
use App\Infrastructure\Doctrine\Fixture\AbstractFixture;
use App\Core\Components\Job\Entity\Enum\PermissionNameEnum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Core\Components\Job\Entity\Enum\PermissionContextEnum;

final class TeamFixture extends Fixture implements FixtureInterface, DependentFixtureInterface
{
    public const IT_TEAM_REFERENCE = 'it_team';

    public function load(ObjectManager $manager): void
    {
        $team = new Team(
            'IT Team',
            'IT Team',
            $this->getReference(CompanyFixture::INSTAPRO_REFERENCE),
        );

        $this->addReference(self::IT_TEAM_REFERENCE, $team);

        $manager->persist($team);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            CompanyFixture::class,
        );
    }
}
