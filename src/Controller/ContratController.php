<?php

namespace App\Controller;

use App\Entity\Contrat;
use App\Entity\Facture;
use App\Form\ReservationType;
use App\Repository\FactureRepository;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContratController extends AbstractController
{
    /**
     * @Route("/reservation/{id}", name="reservation")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour effectuer une réservation")
     */
    public function reservation($id, VoitureRepository $voitureRepository, Request $request, EntityManagerInterface $em)
    {
        $voiture = $voitureRepository->find($id);
        $parking = $voiture->getParking();

        $client = $this->getUser();

        $contrat = new Contrat;
        $facture = new Facture;

        $form = $this->createForm(ReservationType::class, $contrat);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $facture->setContrat($contrat)
                ->setDateFacturation($contrat->getDateRetour())
                ->setMontant(($contrat->getDateDepart()->diff($contrat->getDateRetour()))->days * $contrat->getVoiture()->getPrix());
            $contrat->setVoiture($voiture)
                ->setClient($client)
                ->setFacture($facture);
            $em->persist($contrat);
            $em->flush();

            return $this->redirectToRoute('reservation_list');
        }

        return $this->render('contrat/reservation.html.twig', [
            'voiture' => $voiture,
            'parking' => $parking,
            'formView' => $form->createView()
        ]);
    }

    /**
     * @Route("/reservations/all", name="reservation_list")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour accéder à vos réservations")
     */
    public function list()
    {
        $client = $this->getUser();

        return $this->render('contrat/list.html.twig', [
            'reservations' => $client->getContrats()
        ]);
    }
}
