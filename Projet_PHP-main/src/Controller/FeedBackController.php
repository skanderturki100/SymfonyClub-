<?php
namespace App\Controller;

use App\Entity\FeedBacks;
use App\Form\FeedBackType;
use App\Repository\FeedBacksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedBackController extends AbstractController
{
    #[Route('/feedback', name: 'app_feedback_index')]
    public function index(FeedBacksRepository $feedBacksRepository): Response
    {
        $feedbacks = $feedBacksRepository->findAll();

        return $this->render('feedback/index.html.twig', [
            'feedbacks' => $feedbacks,
        ]);
    }

    #[Route('/feedback/create', name: 'app_feedback_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $feedback = new FeedBacks();
    $form = $this->createForm(FeedBackType::class, $feedback);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        if ($form->isValid()) {
            $entityManager->persist($feedback);
            $entityManager->flush();

            return $this->redirectToRoute('app_feedback_index');
        } else {
            // Debugging: add flash for validation errors
            $this->addFlash('error', 'Form validation failed. Please check your inputs.');
        }
    }

    return $this->render('feedback/create.html.twig', [
        'form' => $form->createView(),
    ]);
    }

    #[Route('/feedback/{id}', name: 'app_feedback_show', requirements: ['id' => '\d+'])]
    public function show(FeedBacks $feedback): Response
    {
        return $this->render('feedback/show.html.twig', [
            'feedback' => $feedback,
        ]);
    }

    #[Route('/feedback/{id}/edit', name: 'app_feedback_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, FeedBacks $feedback, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FeedBackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Feedback updated successfully.');

            return $this->redirectToRoute('app_feedback_show', ['id' => $feedback->getIdFeedback()]);
        }

        return $this->render('feedback/edit.html.twig', [
            'form' => $form->createView(),
            'feedback' => $feedback,
        ]);
    }

    #[Route('/feedback/{id}/delete', name: 'app_feedback_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, FeedBacks $feedback, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $feedback->getIdFeedback(), $request->request->get('_token'))) {
            $entityManager->remove($feedback);
            $entityManager->flush();

            $this->addFlash('success', 'Feedback deleted successfully.');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('app_feedback_index');
    }
}
