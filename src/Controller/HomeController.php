<?php

namespace App\Controller;

use App\Repository\ParkingRepository;
use App\Repository\VoitureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(VoitureRepository $voitureRepository, ParkingRepository $parkingRepository)
    {
        $voitures = $voitureRepository->findBy([], [], 3);

        $parkings = $parkingRepository->findBy([], [], 3);

        return $this->render('home/index.html.twig', [
            'voitures' => $voitures,
            'parkings' => $parkings,
        ]);
    }
}
