<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Core\Components\Company\Entity\Company;

final class CompanyFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $company = new Company('Instapro');

        $manager->persist($company);
        $manager->flush();
    }
}
