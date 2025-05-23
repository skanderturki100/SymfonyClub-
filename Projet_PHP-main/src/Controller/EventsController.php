<?php

namespace App\Controller;

use App\Entity\Events;
use App\Repository\EventsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/events')]
class EventsController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private EventsRepository $eventsRepository;

    public function __construct(EntityManagerInterface $entityManager, EventsRepository $eventsRepository)
    {
        $this->entityManager = $entityManager;
        $this->eventsRepository = $eventsRepository;
    }

    #[Route('/', name: 'events_index', methods: ['GET'])]
public function index(Request $request): Response
{
    // Récupérer la valeur de la recherche
    $search = $request->query->get('search');
    
    // Paramètres pour le tri, avec des valeurs par défaut
    $sortField = $request->query->get('sort', 'id'); // Champ par défaut : 'id'
    $sortOrder = $request->query->get('order', 'ASC'); // Ordre par défaut : 'ASC'
    
    // Appel à la méthode repository pour récupérer les événements
    if ($search) {
        $events = $this->eventsRepository->findByNameAndSorted($search, $sortField, $sortOrder);
    } else {
        $events = $this->eventsRepository->findAllSorted($sortField, $sortOrder);
    }

    return $this->render('events/index.html.twig', [
        'events' => $events,
        'search' => $search,
        'sortField' => $sortField,
        'sortOrder' => $sortOrder,
    ]);
}


    #[Route('/new', name: 'events_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $event = new Events();
    
        if ($request->isMethod('POST')) {
            // Récupération des valeurs du formulaire
            $nomEvent = $request->get('nomEvent');
            $debutEvent = new \DateTime($request->get('debutEvent'));
            $finEvent = new \DateTime($request->get('finEvent'));
            $typeEvent = $request->get('typeEvent');
            $idClub = (int) $request->get('idClub');
            $capacite = (int) $request->get('capacite');
            $descriptionEvent = $request->get('descriptionEvent');
            $membreInscrits = $request->get('membreInscrits'); // Attendu sous forme de chaîne JSON
$ressources = $request->get('ressources'); // Attendu sous forme de chaîne JSON

$event->setMembreInscrits(json_decode($membreInscrits, true))
      ->setRessources(json_decode($ressources, true));

            $uploadedFile =$request->files->get('photo');

            if ($uploadedFile) {
                $imageFileName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();
                $uploadedFile->move(
                    $this->getParameter('image_directory'), // Configure this parameter in your services.yaml
                    $imageFileName
                );

                $image = $imageFileName;
            } else {
                $image = null;
            }

    
            // Validation des données
            $errors = [];
    
            if ($debutEvent < new \DateTime()) {
                $errors[] = "La date de début doit être supérieure ou égale à la date d'aujourd'hui.";
            }
    
            if ($finEvent <= $debutEvent) {
                $errors[] = "La date de fin doit être supérieure à la date de début.";
            }
    
            if ($idClub <= 0) {
                $errors[] = "L'ID Club doit être supérieur à 0.";
            }
    
            if ($capacite <= 0) {
                $errors[] = "La capacité doit être supérieure à 0.";
            }
    
            if (!empty($errors)) {
                return $this->render('events/new.html.twig', [
                    'event' => $event,
                    'errors' => $errors, // Envoi des erreurs à la vue
                ]);
            }
    
            // Hydratation de l'objet Event
            $event->setNomEvent($nomEvent)
                  ->setDebutEvent($debutEvent)
                  ->setFinEvent($finEvent)
                  ->setTypeEvent($typeEvent)
                  ->setIdClub($idClub)
                  ->setCapacite($capacite)
                  ->setDateCreation(new \DateTime())
                  ->setDescriptionEvent($descriptionEvent)
                  ->setPhoto($imageFileName);
    
            // Sauvegarde
            $this->entityManager->persist($event);
            $this->entityManager->flush();
    
            return $this->redirectToRoute('events_index');
        }
    
        return $this->render('events/new.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/show/{id}', name: 'events_show', methods: ['GET'])]
    public function show($id): Response
    {
        // Utilisez find() pour récupérer l'événement par ID
        $event = $this->eventsRepository->find($id);

        if (!$event) {
            throw $this->createNotFoundException('No event found for id ' . $id);
        }

        return $this->render('events/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/edit/{id}', name: 'events_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Events $event): Response
    {
        if ($request->isMethod('POST')) {
            // Récupérer les données du formulaire
            $nomEvent = $request->get('nomEvent');
            $debutEvent = $request->get('debutEvent');
            $finEvent = $request->get('finEvent');
            $typeEvent = $request->get('typeEvent');
            $idClub = $request->get('idClub');
            $capacite = $request->get('capacite');
            $descriptionEvent = $request->get('descriptionEvent');
            $membreInscrits = $request->get('membreInscrits'); // Attendu sous forme de chaîne JSON
            $ressources = $request->get('ressources'); // Attendu sous forme de chaîne JSON
            
            $event->setMembreInscrits(json_decode($membreInscrits, true))
                  ->setRessources(json_decode($ressources, true));
            

            
           

            // Validation des données
            $errors = [];

            if (empty($nomEvent)) {
                $errors[] = "Le nom de l'événement est obligatoire.";
            }

            if (!$debutEvent || !\DateTime::createFromFormat('Y-m-d', $debutEvent)) {
                $errors[] = "La date de début est invalide.";
            }
            if (!$finEvent || !\DateTime::createFromFormat('Y-m-d', $finEvent)) {
                $errors[] = "La date de fin est invalide.";
            }
            if ($debutEvent && $finEvent && new \DateTime($debutEvent) > new \DateTime($finEvent)) {
                $errors[] = "La date de début ne peut pas être postérieure à la date de fin.";
            }

            if (empty($typeEvent)) {
                $errors[] = "Le type de l'événement est obligatoire.";
            }

            if (!ctype_digit($idClub) || (int)$idClub <= 0) {
                $errors[] = "L'ID du club doit être un entier positif.";
            }

            if (!ctype_digit($capacite) || (int)$capacite <= 0) {
                $errors[] = "La capacité doit être un entier positif.";
            }

            if (empty($descriptionEvent)) {
                $errors[] = "La description de l'événement est obligatoire.";
            }

            if (!empty($errors)) {
                return $this->render('events/edit.html.twig', [
                    'event' => $event,
                    'errors' => $errors,
                ]);
            }

            $event->setNomEvent($nomEvent)
                ->setDebutEvent(new \DateTime($debutEvent))
                ->setFinEvent(new \DateTime($finEvent))
                ->setTypeEvent($typeEvent)
                ->setIdClub((int)$idClub)
                ->setCapacite((int)$capacite)
                ->setDescriptionEvent($descriptionEvent)
                ->setMembreInscrits(explode(',', $membreInscrits)) // Convertir en tableau
                ->setRessources(explode(',', $ressources)); // Convertir en tableau

            $this->entityManager->flush();

            return $this->redirectToRoute('events_index');
        }

        return $this->render('events/edit.html.twig', [
            'event' => $event,
            'errors' => [], // Aucune erreur à afficher lors de l'affichage du formulaire d'édition
        ]);
    }

    #[Route('/delete/{id}', name: 'events_delete', methods: ['POST'])]
    public function delete(Request $request, Events $event): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($event);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('events_index');
    }
    
}
