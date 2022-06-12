<?php

namespace App\DataFixtures;

use App\Entity\Parking;
use App\Entity\Voiture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;

    public  function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Faker\Provider\Fakecar($faker));
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        for ($p = 0; $p < 20; $p++) {
            $parking = new Parking;
            $parking->setAdresse($faker->streetAddress)
                ->setCodePostal($faker->postcode)
                ->setVille($faker->city)
                ->setCapacite(mt_rand(20, 200))
                ->setImage($faker->imageUrl(400, 200, true))
                ->setSlug(strtolower($this->slugger->slug($parking->getVille())));

            $manager->persist($parking);

            for ($v = 0; $v < mt_rand(0, $parking->getCapacite()); $v++) {
                $voiture = new Voiture;
                $voiture->setImmatriculation($faker->vehicleRegistration('[A-Z]{2}-[0-9]{3}-[A-Z]{2}'))
                    ->setModel($faker->vehicle)
                    ->setCarburant($faker->vehicleFuelType)
                    ->setCapaciteHabitacle($faker->vehicleSeatCount)
                    ->setBoitier($faker->vehicleGearBoxType)
                    ->setPrix($faker->price(2000, 10000))
                    ->setImage($faker->imageUrl(400, 200, true))
                    ->setSlug(strtolower($this->slugger->slug($voiture->getModel())))
                    ->setParking($parking);

                $manager->persist($voiture);
            }
        }

        $manager->flush();
    }
}
