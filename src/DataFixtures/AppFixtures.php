<?php

namespace App\DataFixtures;

use App\Entity\Catalogue;
use App\Entity\Commande;
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

        $client = new User();
        $client->setEmail("client");
        $client->setRoles(["ROLE_USER"]);
        $client->setPassword($this->userPasswordHasherInterface->hashPassword($client, "client"));
        $manager->persist($client);

        $admin = new User();
        $admin->setEmail("admin");
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setPassword($this->userPasswordHasherInterface->hashPassword($admin, "admin"));
        $manager->persist($admin);

        $catalogue = new Catalogue();
        $catalogue->setNom("Fleur");
        $catalogue->setDescription($faker->realText($maxNbChars = 200, $indexSize = 2));
        $manager->persist($catalogue);

        $catalogue2 = new Catalogue();
        $catalogue2->setNom("Fleur printemps");
        $catalogue2->setDescription($faker->realText($maxNbChars = 200, $indexSize = 2));
        $manager->persist($catalogue2);

        $produitTest = new Produit();
        $produitTest->setNom($faker->name);
        $produitTest->setDescription($faker->realText($maxNbChars = 200, $indexSize = 2));
        $produitTest->setCatalogue($catalogue2);
        $produitTest->setPrix($faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = 1000));
        $manager->persist($produitTest);
        
        for ($i = 0; $i < 20; $i++) {
            $produit = new Produit();
            $produit->setNom($faker->name);
            $produit->setDescription($faker->realText($maxNbChars = 200, $indexSize = 2));
            $produit->setCatalogue($catalogue);
            $produit->setPrix($faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = 1000));
            $manager->persist($produit);
        }

        for ($i = 0; $i < 5; $i++) {
            $commande = new Commande();
            $commande->setDate(new \DateTimeImmutable('Europe/Paris'));
            $commande->setUser($client);
            $commande->addProduit($produitTest);
            $manager->persist($commande);
        }

        $manager->flush();
    }
}
