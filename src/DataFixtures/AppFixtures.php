<?php

namespace App\DataFixtures;

use App\Entity\BankAccount;
use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        $client = [];
        $bankAccount = [];

        for ($i = 0; $i < 10; $i++){
            $client[$i] = new Client();
            $client[$i]->setName($faker->lastName);
            $client[$i]->setFirstname($faker->firstName);
            $client[$i]->setFirstname($faker->firstName);
            $client[$i]->setAdresse($faker->address);
            $client[$i]->setPhoneNumber($faker->phoneNumber);
            $client[$i]->setMail($faker->email);
            $manager->persist($client[$i]);
        }

        for ($i = 0; $i < 10; $i++){
            $bankAccount[$i] = new BankAccount();
            $bankAccount[$i]->setClient($client[array_rand($client)]);
            $bankAccount[$i]->setNumber($faker->randomNumber());
            $bankAccount[$i]->setAmount($faker->numberBetween(0, 55000));
            $bankAccount[$i]->setType($faker->randomElement(['courant', 'epargne']));
            $bankAccount[$i]->setOverdraft(true);
            if ($bankAccount[$i]->getType() === 'epargne'){
                $bankAccount[$i]->setInterestRate(rand(1, 15));
            }
            $manager->persist($bankAccount[$i]);
        }

        $manager->flush();
    }
}
