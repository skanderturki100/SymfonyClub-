<?php

namespace App\Controller;


use App\Entity\Events;
use App\Entity\Innovation;
use App\Repository\InnovationRepository;
use App\Service\EmailService; // Assurez-vous d'ajouter l'import de EmailService
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user/innovation')]
class UserInnovationController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private InnovationRepository $innovationRepository;
    private EmailService $emailService; // Déclaration du service d'email

    public function __construct(EntityManagerInterface $entityManager, InnovationRepository $innovationRepository, EmailService $emailService)
    {
        $this->entityManager = $entityManager;
        $this->innovationRepository = $innovationRepository;
        $this->emailService = $emailService; // Injection du service d'email
    }

    #[Route('/', name: 'user_innovation_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $search = $request->query->get('search', ''); // Récupère le mot-clé de recherche
        $innovations = $search
            ? $this->innovationRepository->findByTitre($search) // Recherche par titre
            : $this->innovationRepository->findAll();

        return $this->render('user_innovation/index.html.twig', [
            'innovations' => $innovations,
        ]);
    }

    #[Route('/new', name: 'user_innovation_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $innovation = new Innovation();
        $errors = [];
    
        // Récupération des événements disponibles
        $events = $this->entityManager->getRepository(Events::class)->findAll();
        
        if ($request->isMethod('POST')) {
            $nomClub = $request->get('nomClub');
            $titre = $request->get('titre');
            $idMembre = $request->get('idMembre');
            $descriptionInnovation = $request->get('descriptionInnovation');
            $dateCreationInnovation = new \DateTime($request->get('dateCreationInnovation'));
            $eventId = $request->get('events'); // Récupération de l'événement depuis le formulaire
            $statusInnovation = 'En attente';
    
            // Validation
            if (empty($nomClub)) {
                $errors['nomClub'] = "Le nom du club ne peut pas être vide.";
            }
            if (empty($titre)) {
                $errors['titre'] = "Le titre ne peut pas être vide.";
            }
            if (empty($idMembre)) {
                $errors['idMembre'] = "L'ID du membre ne peut pas être vide.";
            } elseif (!preg_match('/^\d{8}$/', $idMembre)) {
                $errors['idMembre'] = "L'ID du membre doit être un nombre de 8 chiffres.";
            }
            $today = new \DateTime();
            if ($dateCreationInnovation->format('Y-m-d') !== $today->format('Y-m-d')) {
                $errors['dateCreationInnovation'] = "La date de création doit être aujourd'hui.";
            }
    
            // Validation de l'événement
            $event = $this->entityManager->getRepository(Events::class)->find($eventId);
            if (!$event) {
                $errors['events'] = "Veuillez sélectionner un événement valide.";
            }
    
            // Si aucune erreur, enregistrer l'innovation
            if (count($errors) === 0) {
                $innovation->setNomClub($nomClub)
                    ->setTitre($titre)
                    ->setDescriptionInnovation($descriptionInnovation)
                    ->setDateCreationInnovation($dateCreationInnovation)
                    ->setStatusInnovation($statusInnovation)
                    ->setIdMembre((int)$idMembre)
                    ->setEvents($event); // Associer l'événement
    
                $this->entityManager->persist($innovation);
                $this->entityManager->flush();
    
                // Appel du service d'email
            
                $to = 'nabil.khiari@esprit.tn';
                $subject = 'Nouvelle innovation créée';
                $body = 'Une nouvelle innovation a été créée avec les détails suivants : ' . $titre;
                $this->emailService->sendInnovationEmail($to, $subject, $body);
               
    
                return $this->redirectToRoute('user_innovation_index');
            }
    
            return $this->render('user_innovation/new.html.twig', [
                'errors' => $errors,
                'innovation' => $innovation,
                'events' => $events, // Passer les événements à la vue
            ]);
        }
    
        return $this->render('user_innovation/new.html.twig', [
            'innovation' => $innovation,
            'events' => $events, // Passer les événements à la vue
        ]);
    }
}
