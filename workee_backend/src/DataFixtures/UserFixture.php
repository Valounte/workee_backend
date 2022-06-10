<?php

namespace App\DataFixtures;

use App\DataFixtures\CompanyFixture;
use Doctrine\Persistence\ObjectManager;
use App\Core\Components\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Core\Components\Company\Repository\CompanyRepositoryInterface;

final class UserFixture extends Fixture implements FixtureInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $user = new User(
            'workee@gmail.com',
            'workee',
            'back',
            $this->getReference(CompanyFixture::INSTAPRO_REFERENCE),
            'Password123!',
        );

        $manager->persist($user);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            CompanyFixture::class,
        );
    }
}
