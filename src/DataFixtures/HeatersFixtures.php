<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Heating;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class HeatersFixtures extends Fixture
{
    function __construct(
        private readonly SluggerInterface $slugger
    )
    {
    }

    function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $heaters = ['Électric', 'Gaz', 'Cuisinière', 'Charbon', 'Bois'];
        array_map(function (string $h) use ($manager, $faker) {
            $heating = (new Heating())
                ->setName($h)
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
            ;
            $heating->setSlug(strtolower($this->slugger->slug($heating->getName())));

            $this->addReference($h, $heating);
            $manager->persist($heating);
        }, $heaters);
        $manager->flush();
    }
}
