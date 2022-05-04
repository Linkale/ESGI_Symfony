<?php

namespace App\DataFixtures;

use App\Entity\Catalogue;
use App\Entity\Produit;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        $user = new User();
        $user->setEmail("test");
        $user->setPassword($this->userPasswordHasherInterface->hashPassword($user, "test"));
        $manager->persist($user);

        $catalogue = new Catalogue();
        $catalogue->setNom("Fleur");
        $catalogue->setDescription($faker->realText($maxNbChars = 200, $indexSize = 2));
        $manager->persist($catalogue);

        $catalogue2 = new Catalogue();
        $catalogue2->setNom("Fleur printemps");
        $catalogue2->setDescription($faker->realText($maxNbChars = 200, $indexSize = 2));
        $manager->persist($catalogue2);
        
        for ($i = 0; $i < 20; $i++) {
            $produit = new Produit();
            $produit->setNom($faker->name);
            $produit->setDescription($faker->realText($maxNbChars = 200, $indexSize = 2));
            $produit->setCatalogue($catalogue);
            $manager->persist($produit);
        }

        $manager->flush();
    }
}
