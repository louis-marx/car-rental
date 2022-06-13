<?php

namespace App\Controller;

use App\Entity\Parking;
use App\Form\ParkingType;
use App\Repository\ParkingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    /**
     * @Route("/admin/parking/ajouter", name="parking_ajouter")
     */
    public function ajouter(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $parking = new Parking;

        $form = $this->createForm(ParkingType::class, $parking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parking->setSlug(strtolower($slugger->slug($parking->getVille())));
            $em->persist($parking);
            $em->flush();

            return $this->redirectToRoute('parking_show', [
                'slug' => $parking->getSlug()
            ]);
        }

        return $this->render('parking/ajouter.html.twig', [
            'formView' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/parking/{id}/modifier", name="parking_modifier")
     */
    public function edit($id, ParkingRepository $parkingRepository, Request $request, EntityManagerInterface $em)
    {
        $parking = $parkingRepository->find($id);

        $form = $this->createForm(ParkingType::class, $parking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            return $this->redirectToRoute('parking_show', [
                'slug' => $parking->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('parking/modifier.html.twig', [
            'parking' => $parking,
            'formView' => $formView
        ]);
    }
}
