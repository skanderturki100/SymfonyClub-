<?php

// src/Controller/UserEventController.php
namespace App\Controller;

use App\Repository\UserEventsRepository; // Utilisez UserEventsRepository ici
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserEventController extends AbstractController
{
    private UserEventsRepository $eventsRepository; // Typage avec UserEventsRepository

    public function __construct(UserEventsRepository $eventsRepository)
    {
        $this->eventsRepository = $eventsRepository;
    }

    #[Route('/user/events', name: 'user_events_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // Récupérer les paramètres de recherche et de tri
        $search = $request->query->get('search', '');
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'ASC');

        // Récupérer les événements avec recherche et tri
        $events = $this->eventsRepository->findBySearch($search, $sort, $order);

        return $this->render('user_event/index.html.twig', [
            'events' => $events,
            'search' => $search,   // La valeur de recherche pour pré-remplir la barre de recherche
            'sortOrder' => $order, // L'ordre de tri actuel pour les liens de tri
        ]);
    }

    #[Route('/user/events/show/{id}', name: 'user_events_show', methods: ['GET'])]
    public function show($id): Response
    {
        // Récupérer l'événement par son ID
        $event = $this->eventsRepository->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Aucun événement trouvé pour l\'ID ' . $id);
        }

        return $this->render('user_event/show.html.twig', [
            'event' => $event,
        ]);
    }
}

