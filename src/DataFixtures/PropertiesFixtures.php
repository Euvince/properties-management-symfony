<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Property;
use App\DataFixtures\UsersFixtures;
use App\DataFixtures\HeatersFixtures;
use App\DataFixtures\SpecificitesFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\PropertiesTypesFixtures;
use App\Repository\HeatingRepository;
use App\Repository\SpecificityRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PropertiesFixtures extends Fixture implements DependentFixtureInterface
{

    function __construct(
        private readonly SluggerInterface $slugger,
        private readonly HeatingRepository $heatingRepository,
        private readonly SpecificityRepository $specificityRepository
    )
    {
    }

    function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $pictures = ['1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg', '7.jpg', '9.jpg', '10.jpg', '11.jpg', '13.png', '14.png', '16.png', '17.jpg', '18.jpg'];
        $heaters = ['Électric', 'Gaz', 'Cuisinière', 'Charbon', 'Bois'];
        $specificities = ['Piscine', 'Véranda', 'Balcon', 'Jaccuzi', 'Ascenceur'];
        $propertyTypes = ['Maison', 'Hôtel', 'Château', 'Appartement', 'Bibliothèque'];

        for ((int) $i = 0; $i < 60; $i++) {
            $property = (new Property())
                ->setUser($this->getReference("user0" . $faker->numberBetween(1, 5)))
                ->setPropertyType($this->getReference($faker->randomElement($propertyTypes)))
                ->setTitle($faker->sentence())
                ->setSurface($faker->numberBetween(20, 120))
                ->setPrice($faker->randomNumber())
                ->setDescription($faker->sentences(rand(3, 10), true))
                ->setRooms($faker->numberBetween(2, 6))
                ->setBedrooms($faker->numberBetween(3, 15))
                ->setFloor($faker->numberBetween(2, 5))
                ->setAdress($faker->address())
                ->setCity($faker->city())
                ->setPostalCode($faker->postcode())
                ->setSold($faker->boolean(10))
                ->setPicture($faker->randomElement($pictures))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))

                /* ->addHeater($this->getReference($faker->randomElement($heaters)))
                ->addSpecificite($this->getReference($faker->randomElement($specificities))) */
            ;
            $property->setSlug(strtolower($this->slugger->slug($property->getTitle())));

            $heaters = $this->heatingRepository->findAll();
            foreach ($faker->randomElements($heaters, $faker->numberBetween(2, 3)) as $heater) {
                $property->addHeater($heater);
            }
            $specificities = $this->specificityRepository->findAll();
            foreach ($faker->randomElements($specificities, $faker->numberBetween(2, 3)) as $specificity) {
                $property->addSpecificity($specificity);
            }

            $manager->persist($property);
        }

        $manager->flush();
    }


    function getDependencies() : array
    {
        return [
            UsersFixtures::class,
            HeatersFixtures::class,
            SpecificitesFixtures::class,
            PropertiesTypesFixtures::class
        ];
    }

}
