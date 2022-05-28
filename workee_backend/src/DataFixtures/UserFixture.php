<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use App\Core\Components\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Core\Components\Company\Repository\CompanyRepositoryInterface;

final class UserFixture extends Fixture
{
    public function __construct(private CompanyRepositoryInterface $companyRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $company = $this->companyRepository->findOneByName('Instapro');

        $user = new User(
            'workee@gmail.com',
            'workee',
            'back',
            $company,
            'Password123!',
        );

        $manager->persist($user);
        $manager->flush();
    }
}
