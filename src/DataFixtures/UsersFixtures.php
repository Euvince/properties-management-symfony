<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture
{
    private const ADMIN = 'USER';

    function __construct(
        private readonly UserPasswordHasherInterface $hasher
    )
    {
    }

    function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $user = (new User())
            ->setUsername('Euvince')
            ->setEmail('euvince03@gmail.com')
            ->setRoles([])
            ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
            ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
        ;
        $user->setPassword($this->hasher->hashPassword($user, '0000'));
        $this->addReference(self::ADMIN, $user);
        $manager->persist($user);

        for ((int) $i = 1; $i <= 5; $i++) {
            $user = (new User())
                ->setUsername("User{$i}")
                ->setEmail("user0{$i}@gmail.com")
                ->setRoles([])
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
            ;
            $user->setPassword($this->hasher->hashPassword($user, '0000'));
            $this->setReference("user0{$i}", $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
