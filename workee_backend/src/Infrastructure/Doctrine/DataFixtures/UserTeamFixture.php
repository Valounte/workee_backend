<?php

namespace App\Infrastructure\Doctrine\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use App\Core\Components\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Core\Components\User\Entity\UserTeam;
use Doctrine\Common\DataFixtures\FixtureInterface;
use App\Infrastructure\Doctrine\DataFixtures\JobFixture;
use App\Infrastructure\Doctrine\Fixture\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Infrastructure\Doctrine\DataFixtures\CompanyFixture;
use App\Core\Components\Company\Repository\CompanyRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserTeamFixture extends Fixture implements FixtureInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $userTeam = new UserTeam(
            $this->getReference(UserFixture::USER_REFERENCE),
            $this->getReference(TeamFixture::IT_TEAM_REFERENCE),
        );

        $manager->persist($userTeam);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixture::class,
            TeamFixture::class,
        );
    }
}
