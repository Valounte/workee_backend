<?php

namespace App\Infrastructure\Doctrine\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use App\Core\Components\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use App\Infrastructure\Doctrine\DataFixtures\JobFixture;
use App\Infrastructure\Doctrine\Fixture\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Infrastructure\Doctrine\DataFixtures\CompanyFixture;
use App\Core\Components\Company\Repository\CompanyRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixture extends Fixture implements FixtureInterface, DependentFixtureInterface
{
    public const USER_REFERENCE = 'user';

    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User(
            'workee@gmail.com',
            'workee',
            'back',
            $this->getReference(CompanyFixture::INSTAPRO_REFERENCE),
            $this->getReference(JobFixture::MANAGER_REFERENCE),
            null,
        );

        $user->setPassword($this->hasher->hashPassword($user, 'Password123!'));
        $this->addReference(self::USER_REFERENCE, $user);

        $manager->persist($user);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            CompanyFixture::class,
            JobFixture::class,
        );
    }
}
