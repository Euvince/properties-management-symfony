<?php

namespace App\DataFixtures;

use App\Entity\Specificity;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class SpecificitesFixtures extends Fixture
{

    function __construct(
        private readonly SluggerInterface $slugger
    )
    {
    }

    function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $specificities = ['Piscine', 'Véranda', 'Balcon', 'Jaccuzi', 'Ascenceur', 'Adapté aux personnes à mobilité réduite'];
        foreach ($specificities as $o) {
            $option = (new Specificity())
                ->setName($o)
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
            ;
            $option->setSlug(strtolower($this->slugger->slug($option->getName())));

            $this->addReference($o, $option);
            $manager->persist($option);
        }
        $manager->flush();
    }
}
