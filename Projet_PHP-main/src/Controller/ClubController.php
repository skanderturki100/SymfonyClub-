<?php

namespace App\Controller;

use App\Entity\Club;
use App\Form\ClubType;
use App\Repository\ClubRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ClubController extends AbstractController
{
    #[Route('/club', name: 'app_club_index')]
    public function index(ClubRepository $clubRepository): Response
    {
        // Fetch all clubs from the database
        $clubs = $clubRepository->findAll();

        return $this->render('club/index.html.twig', [
            'clubs' => $clubs,
        ]);
    }

    #[Route('/club/create', name: 'app_club_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $club = new Club();
        $form = $this->createForm(ClubType::class, $club);
        
        // Handle form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Automatically uploads the image if it's set (handled by VichUploader)
            $entityManager->persist($club);
            $entityManager->flush(); // Persist the new club entity (including the uploaded image)

            return $this->redirectToRoute('app_club_index');
        }

        return $this->render('club/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/club/{id}/edit', name: 'app_club_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Club $club, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClubType::class, $club);
        
        // Handle form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // If there's a new image, VichUploaderBundle will handle the file upload automatically.
            $entityManager->flush(); // Save changes to the entity (including the image if changed)

            return $this->redirectToRoute('app_club_index');
        }

        return $this->render('club/edit.html.twig', [
            'form' => $form->createView(),
            'club' => $club,
        ]);
    }

    #[Route('/club/{id}/delete', name: 'app_club_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Club $club, EntityManagerInterface $entityManager): Response
    {
        // CSRF protection: check token
        if ($this->isCsrfTokenValid('delete'.$club->getIdClub(), $request->request->get('_token'))) {
            $entityManager->remove($club);
            $entityManager->flush(); // Delete the club from the database
        }

        return $this->redirectToRoute('app_club_index');
    }

    #[Route("/club/{id}", name: "app_club_show", methods: ["GET"])]
    public function show(int $id, ClubRepository $clubRepository): Response
    {
        // Fetch a single club by its ID
        $club = $clubRepository->find($id);

        if (!$club) {
            throw $this->createNotFoundException('The club does not exist');
        }

        return $this->render('club/show.html.twig', [
            'club' => $club,
        ]);
    }
}
