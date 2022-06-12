<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Form\VoitureType;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VoitureController extends AbstractController
{
    /**
     * @Route("/voitures", name="voiture_list")
     */
    public function list(VoitureRepository $voitureRepository)
    {
        $voitures = $voitureRepository->findAll();

        if (!$voitures) {
            throw $this->createNotFoundException("Aucune voiture à afficher");
        }

        return $this->render('voiture/index.html.twig', [
            'voitures' => $voitures
        ]);
    }

    /**
     * @Route("/voitures/{slug}", name="voiture_show")
     */
    public function show($slug, VoitureRepository $voitureRepository)
    {
        $voiture = $voitureRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$voiture) {
            throw $this->createNotFoundException("La voiture recherchée n'existe pas");
        }

        return $this->render('voiture/show.html.twig', [
            'voiture' => $voiture
        ]);
    }

    /**
     * @Route("/admin/voitures/ajouter", name="voiture_ajouter")
     */
    public function ajouter(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $voiture = new Voiture;

        $form = $this->createForm(VoitureType::class, $voiture);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $voiture->setSlug(strtolower($slugger->slug($voiture->getModel())));
            $em->persist($voiture);
            $em->flush();

            return $this->redirectToRoute('voiture_show', [
                'slug' => $voiture->getSlug()
            ]);
        }

        return $this->render('voiture/ajouter.html.twig', [
            'formView' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/voitures/{id}/modifier", name="voiture_modifier")
     */
    public function edit($id, VoitureRepository $voitureRepository, Request $request, EntityManagerInterface $em)
    {
        $voiture = $voitureRepository->find($id);

        $form = $this->createForm(VoitureType::class, $voiture);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            return $this->redirectToRoute('voiture_show', [
                'slug' => $voiture->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('voiture/modifier.html.twig', [
            'voiture' => $voiture,
            'formView' => $formView
        ]);
    }
}
