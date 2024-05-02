<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\PropertyType;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class PropertiesTypesFixtures extends Fixture
{

    function __construct(
        private readonly SluggerInterface $slugger
    )
    {
    }

    function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $propertyTypes = ['Maison', 'Hôtel', 'Château', 'Appartement', 'Bibliothèque'];
        /* for ((int)$i = 0; $i < count($propertyTypes); $i++) {
            $propertyType = (new PropertyType())
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
            ;
            $propertyType->setName($propertyType[$i]);
            $this->addReference($propertyTypes[$i], $propertyType);
            $manager->persist($propertyType);
        } */

        array_map(function (string $type) use ($manager, $faker) {
            $propertyType = (new PropertyType())
                ->setName($type)
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
            ;
            $propertyType->setSlug(strtolower($this->slugger->slug($propertyType->getName())));

            $this->addReference($type, $propertyType);
            $manager->persist($propertyType);
        }, $propertyTypes);

        $manager->flush();
    }
}
