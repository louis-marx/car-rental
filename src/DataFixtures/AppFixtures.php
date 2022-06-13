<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Client;
use App\Entity\Contrat;
use App\Entity\Facture;
use App\Entity\Parking;
use App\Entity\Voiture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $encoder;

    public  function __construct(SluggerInterface $slugger, UserPasswordEncoderInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Faker\Provider\Fakecar($faker));
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        $admin = new Client();

        $hash = $this->encoder->encodePassword($admin, "password");

        $admin->setEmail("admin@test.com")
            ->setPrenom($faker->firstName)
            ->setNom($faker->lastName)
            ->setAdresse($faker->streetAddress)
            ->setVille($faker->city)
            ->setCodePostal($faker->postcode)
            ->setTelephone($faker->phoneNumber)
            ->setPassword($hash)
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $users = [];

        for ($u = 0; $u < 25; $u++) {
            $user = new Client();

            $hash = $this->encoder->encodePassword($user, "password");

            $user->setEmail("user$u@test.com")
                ->setPrenom($faker->firstName)
                ->setNom($faker->lastname)
                ->setAdresse($faker->streetAddress)
                ->setVille($faker->city)
                ->setCodePostal($faker->postCode)
                ->setTelephone($faker->phoneNumber)
                ->setPassword($hash);

            $users[] = $user;

            $manager->persist($user);
        }

        $voitures = [];

        for ($p = 0; $p < 15; $p++) {
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

                $voitures[] = $voiture;

                $manager->persist($voiture);
            }
        }

        for ($p = 0; $p < 100; $p++) {
            $contrat = new Contrat;
            $facture = new Facture;

            $contrat->setDateDepart($faker->dateTime())
                ->setDateRetour($faker->dateTimeInInterval($contrat->getDateDepart(), '+15  days'))
                ->setClient($faker->randomElement($users))
                ->setFacture($facture)
                ->setVoiture($faker->randomElement($voitures));

            $facture->setDateFacturation($contrat->getDateRetour())
                ->setContrat($contrat)
                ->setMontant(($contrat->getDateDepart()->diff($contrat->getDateRetour()))->days * $contrat->getVoiture()->getPrix());

            if ($faker->boolean(90)) {
                $facture->setStatut(Facture::STATUT_PAID);
            }

            $manager->persist($facture);
            $manager->persist($contrat);
        }

        $manager->flush();
    }
}
