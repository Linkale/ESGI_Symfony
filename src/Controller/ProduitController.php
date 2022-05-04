<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProduitController extends AbstractController
{
    #[Route('/produits', name: 'produits')]
    public function produits(ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findAll();

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/produit/new', name: 'new_produit')]
    public function newProduit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('produits');
        }

        return $this->render('produit/new_produit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/produit/update/{id}', name: 'update_produit')]
    public function updateProduit(Produit $produit, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('produit', array('produitName' => $produit->getNom()));
        }

        return $this->render('produit/new_produit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/produit/delete/{id}', name: 'delete_produit')]
    public function deleteProduit(Produit $produit, Request $request, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($produit);
        $entityManager->flush();

        return $this->redirectToRoute('produits');
    }

    #[Route('/produit/{produitName}', name: 'produit')]
    public function produit(ProduitRepository $produitRepository, string $produitName): Response
    {
        $produit = $produitRepository->findOneBy(array('nom' => $produitName));

        if (!$produit) {
            throw $this->createNotFoundException();
        }

        return $this->render('produit/detail.html.twig', [
            'produit' => $produit,
        ]);
    }
}
