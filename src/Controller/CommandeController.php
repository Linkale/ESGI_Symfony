<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CommandeController extends AbstractController
{
    #[Route('/commandes', name: 'commandes')]
    public function commandes(CommandeRepository $commandeRepository): Response
    {
        $commandes = $commandeRepository->findAll();

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/commande/new', name: 'new_commande')]
    public function newCommande(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commande->setDate(new \DateTimeImmutable('Europe/Paris'));
            $entityManager->persist($commande);
            $entityManager->flush();

            return $this->redirectToRoute('commandes');
        }

        return $this->render('commande/new_commande.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/commande/update/{id}', name: 'update_commande')]
    public function updateCommande(Commande $commande, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $facture */
            $factureFile = $form->get('facture')->getData();
            if ($factureFile) {
                $originalFilename = pathinfo($factureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$factureFile->guessExtension();

                try {
                    $factureFile->move(
                        $this->getParameter('factures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $commande->setFacture($newFilename);
            }
            $entityManager->persist($commande);
            $entityManager->flush();

            return $this->redirectToRoute('commande', array('commandeId' => $commande->getId()));
        }

        return $this->render('commande/new_commande.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/commande/delete/{id}', name: 'delete_commande')]
    public function deleteCommande(Commande $commande, Request $request, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($commande);
        $entityManager->flush();

        return $this->redirectToRoute('commandes');
    }

    #[Route('/commande/{commandeId}', name: 'commande')]
    public function commande(CommandeRepository $commandeRepository, int $commandeId): Response
    {
        $commande = $commandeRepository->findOneBy(array('id' => $commandeId));

        if (!$commande) {
            throw $this->createNotFoundException();
        }

        return $this->render('commande/detail.html.twig', [
            'commande' => $commande,
        ]);
    }
}
