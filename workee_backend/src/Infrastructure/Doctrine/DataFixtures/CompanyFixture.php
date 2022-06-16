<?php

namespace App\Infrastructure\Doctrine\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Core\Components\Company\Entity\Company;
use Doctrine\Common\DataFixtures\FixtureInterface;
use App\Infrastructure\Doctrine\Fixture\AbstractFixture;

final class CompanyFixture extends Fixture implements FixtureInterface
{
    public const INSTAPRO_REFERENCE = 'instapro';

    public function load(ObjectManager $manager): void
    {
        $company = new Company('Instapro');
        $this->addReference(self::INSTAPRO_REFERENCE, $company);
        $manager->persist($company);
        $manager->flush();
    }
}
