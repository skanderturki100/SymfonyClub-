<?php

namespace App\Controller;

use App\Entity\Partenaire;
use App\Form\PartenaireType;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\TwilioService;
class PartenaireController extends AbstractController
{
    private $partenaireRepository;
    private $entityManager;

    public function __construct(PartenaireRepository $partenaireRepository, EntityManagerInterface $entityManager)
    {
        $this->partenaireRepository = $partenaireRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/partenaire', name: 'app_partenaire_index')]
    public function index(): Response
    {
        $partenaires = $this->partenaireRepository->findAll();

        return $this->render('partenaire/index.html.twig', [
            'partenaires' => $partenaires,
        ]);
    }

    #[Route('/partenaire/new', name: 'app_partenaire_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, TwilioService $twilioService): Response
    {
        $partenaire = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($partenaire);
            $this->entityManager->flush();
            $message = sprintf(
                "Bonjour %s, votre partenariat avec notre club a été enregistré avec succès !",
                $partenaire->getNomPartenaire()
            );
            $twilioService->sendSMS($partenaire->getTelPartenaire(), $message);
            return $this->redirectToRoute('app_partenaire_index');
        }

        return $this->render('partenaire/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/partenaire/{id}', name: 'app_partenaire_show')]
    public function show(Partenaire $partenaire): Response
    {
        return $this->render('partenaire/show.html.twig', [
            'partenaire' => $partenaire,
        ]);
    }

    #[Route('/partenaire/{id}/edit', name: 'app_partenaire_edit')]
    public function edit(Request $request, Partenaire $partenaire): Response
    {
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Le partenaire a été mis à jour avec succès.');
            return $this->redirectToRoute('app_partenaire_show', ['id' => $partenaire->getIdPartenaire()]);
        }

        return $this->render('partenaire/edit.html.twig', [
            'form' => $form->createView(),
            'partenaire' => $partenaire,
        ]);
    }

    #[Route('/partenaire/{id}/delete', name: 'app_partenaire_delete')]
    public function delete(Request $request, Partenaire $partenaire): Response
    {
        if ($this->isCsrfTokenValid('delete' . $partenaire->getIdPartenaire(), $request->get('_token'))) {
            $this->entityManager->remove($partenaire);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_partenaire_index');
    }
}
