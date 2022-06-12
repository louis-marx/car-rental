<?php

namespace App\Controller;

use App\Repository\ParkingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ParkingController extends AbstractController
{
    /**
     * @Route("/parkings", name="parking_list")
     */
    public function list(ParkingRepository $parkingRepository)
    {
        $parkings = $parkingRepository->findAll();

        if (!$parkings) {
            throw $this->createNotFoundException("Aucun parking à afficher");
        }

        return $this->render('parking/index.html.twig', [
            'parkings' => $parkings
        ]);
    }

    /**
     * @Route("/parkings/{slug}", name="parking_show")
     */
    public function show($slug, ParkingRepository $parkingRepository)
    {
        $parking = $parkingRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$parking) {
            throw $this->createNotFoundException("Le parking recherché n'existe pas");
        }

        return $this->render('parking/show.html.twig', [
            'parking' => $parking
        ]);
    }
}
