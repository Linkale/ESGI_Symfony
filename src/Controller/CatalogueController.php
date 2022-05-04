<?php

namespace App\Controller;

use App\Entity\Catalogue;
use App\Form\CatalogueType;
use App\Repository\CatalogueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CatalogueController extends AbstractController
{
    #[Route('/catalogues', name: 'catalogues')]
    public function catalogues(CatalogueRepository $catalogueRepository): Response
    {
        $catalogues = $catalogueRepository->findAll();

        return $this->render('catalogue/index.html.twig', [
            'catalogues' => $catalogues,
        ]);
    }

    #[Route('/catalogue/new', name: 'new_catalogue')]
    public function newCatalogue(Request $request, EntityManagerInterface $entityManager): Response
    {
        $catalogue = new Catalogue();
        $form = $this->createForm(CatalogueType::class, $catalogue);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($catalogue);
            $entityManager->flush();

            return $this->redirectToRoute('catalogues');
        }

        return $this->render('catalogue/new_catalogue.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/catalogue/update/{id}', name: 'update_catalogue')]
    public function updateCatalogue(Catalogue $catalogue, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CatalogueType::class, $catalogue);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($catalogue);
            $entityManager->flush();

            return $this->redirectToRoute('catalogue', array('catalogueName' => $catalogue->getNom()));
        }

        return $this->render('catalogue/new_catalogue.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/catalogue/delete/{id}', name: 'delete_catalogue')]
    public function deleteCatalogue(Catalogue $catalogue, Request $request, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($catalogue);
        $entityManager->flush();

        return $this->redirectToRoute('catalogues');
    }

    #[Route('/catalogue/{catalogueName}', name: 'catalogue')]
    public function catalogue(CatalogueRepository $catalogueRepository, string $catalogueName): Response
    {
        $catalogue = $catalogueRepository->findOneBy(array('nom' => $catalogueName));

        if (!$catalogue) {
            throw $this->createNotFoundException();
        }

        return $this->render('catalogue/detail.html.twig', [
            'catalogue' => $catalogue,
        ]);
    }
}
