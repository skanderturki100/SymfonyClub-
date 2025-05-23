<?php

namespace App\Controller\Admin;

use App\Entity\Partenaire;
use App\Form\PartenaireType;
use App\Repository\PartenaireRepository;
use App\Entity\Club;
use App\Form\ClubType;
use App\Entity\FeedBacks;
use App\Form\FeedBackType;
use App\Repository\ClubRepository;
use App\Repository\FeedBacksRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use App\Entity\Innovation;
use App\Entity\Events;
use App\Service\TwilioService;
use App\Repository\InnovationRepository;
use App\Repository\EventsRepository;

class DashboardController extends AbstractDashboardController
{
    private EntityManagerInterface $entityManager;
    private InnovationRepository $innovationRepository;
    private EventsRepository $eventsRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        InnovationRepository $innovationRepository,
        EventsRepository $eventsRepository
    ) {
        $this->entityManager = $entityManager;
        $this->innovationRepository = $innovationRepository;
        $this->eventsRepository = $eventsRepository; 
    }

    #[Route('/admin', name: 'admin')]
    public function indexi(ClubRepository $clubRepository, FeedBacksRepository $feedBacksRepository, Security $security): Response
    {
        // Ensure the user is an admin
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_homepage'); // Or another page based on your requirement
        }

        // Fetch all clubs and feedbacks from the database
        $clubs = $clubRepository->findAll();
        $feedbacks = $feedBacksRepository->findAll();

        // Default dashboard page
        return $this->render('admin/dashboard.html.twig', [
            'clubs' => $clubs,
            'feedbacks' => $feedbacks,
            'user' => $security->getUser(),
        ]);
    }

   
    //-------------------------Cubs---------------------------------------
    // Add Club Route
    #[Route('/admin/clubs/add', name: 'admin_add_club')]
    public function addClub(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create a new Club entity
        $club = new Club();

        // Create the form using ClubType
        $form = $this->createForm(ClubType::class, $club);

        // Handle the request and bind the submitted data
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save the club to the database
            $entityManager->persist($club);
            $entityManager->flush();

            // Add a success flash message
            $this->addFlash('success', 'Club successfully added!');

            // Redirect to the list of clubs or another page
            return $this->redirectToRoute('admin_show_clubs'); // Redirecting to the clubs list page
        }

        // Render the form in the template
        return $this->render('admin/ClubAdmin/addclubadmin.html.twig', [
            'form' => $form->createView(),
            'title' => 'Add a New Club',
        ]);
    }

    #[Route('/admin/clubs/edit/{id}', name: 'admin_edit_club')]
    public function editClub(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Retrieve the existing club by its ID
        $club = $entityManager->getRepository(Club::class)->find($id);

        if (!$club) {
            throw $this->createNotFoundException('The club does not exist.');
        }

        // Create the form with the existing club data
        $form = $this->createForm(ClubType::class, $club);

        // Handle the form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save the updated club data to the database
            $entityManager->flush();

            // Add a success flash message
            $this->addFlash('success', 'Club successfully updated!');

            // Redirect to the club details page or another page
            return $this->redirectToRoute('admin_show_club', ['id' => $club->getIdClub()]);
        }

        // Render the edit form template
        return $this->render('admin/ClubAdmin/editclubadmin.html.twig', [
            'form' => $form->createView(),
            'title' => 'Edit Club: ' . $club->getNomClub(),
        ]);
    }

    
    #[Route('/admin/clubs', name: 'admin_show_clubs')]
    public function showClubs(EntityManagerInterface $entityManager): Response
    {
        // Fetch all clubs from the database
        $clubs = $entityManager->getRepository(Club::class)->findAll();

        // Render the list of clubs
        return $this->render('admin/ClubAdmin/showclubadmin.html.twig', [
            'clubs' => $clubs,
        ]);
    }
   
   
    
    #[Route('/admin/clubs/delete/{id}', name: 'admin_delete_club')]
        public function deleteClub(int $id, EntityManagerInterface $entityManager): Response
        {
            // Find the club by ID
            $club = $entityManager->getRepository(Club::class)->find($id);
    
            // If the club doesn't exist, throw an error
            if (!$club) {
                $this->addFlash('error', 'The club does not exist.');
                return $this->redirectToRoute('admin_show_clubs');
            }
    
            // Remove the club from the database
            $entityManager->remove($club);
            $entityManager->flush();
    
            // Add a success flash message
            $this->addFlash('success', 'Club successfully deleted!');
    
            // Redirect to the clubs list page
            return $this->redirectToRoute('admin_show_clubs');
        }
    // -------------------------------FeedBack---------------------------------
    #[Route('admin/feedback/create', name: 'admin_feedback_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $feedback = new FeedBacks();
        $form = $this->createForm(FeedBackType::class, $feedback);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($feedback);
            $entityManager->flush();

            $this->addFlash('success', 'Feedback created successfully.');

            return $this->redirectToRoute('admin_feedback_show', ['id' => $feedback->getIdFeedback()]);
        }

        return $this->render('admin/FeedBackAdmin/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/feedback', name: 'admin_feedback_index')]
    public function indexey(FeedBacksRepository $feedBacksRepository): Response
    {
        $feedbacks = $feedBacksRepository->findAll();

        return $this->render('admin/FeedBackAdmin/index.html.twig', [
            'feedbacks' => $feedbacks,
        ]);
    }

    #[Route('admin/feedback/{id}/edit', name: 'admin_feedback_edit', requirements: ['id' => '\d+'])]
    public function edite(Request $request, FeedBacks $feedback, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FeedBackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Feedback updated successfully.');

            return $this->redirectToRoute('admin_feedback_index');
        }

        return $this->render('admin/FeedBackAdmin/edit.html.twig', [
            'form' => $form->createView(),
            'feedback' => $feedback,
        ]);
    }

    #[Route('admin/feedback/{id}/delete', name: 'admin_feedback_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function deletey(Request $request, FeedBacks $feedback, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $feedback->getIdFeedback(), $request->request->get('_token'))) {
            $entityManager->remove($feedback);
            $entityManager->flush();

            $this->addFlash('success', 'Feedback deleted successfully.');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('admin_feedback_index');
    }
    
    



    #[Route('/admin/innovation', name: 'admin_innovation_index', methods: ['GET'])]
    public function indexe(Request $request): Response
    {
        $statusTerm = $request->query->get('status', '');

        if ($statusTerm) {
            $innovations = $this->innovationRepository->findByStatus($statusTerm);
        } else {
            $innovations = $this->innovationRepository->findAll();
        }

        return $this->render('admin/innovation/index.html.twig', [
            'innovations' => $innovations,
            'statusTerm' => $statusTerm,
        ]);
    }

    #[Route('/admin/event', name: 'admin_events_index', methods: ['GET', 'POST'])]
    public function indexy(Request $request): Response
    {
        $search = $request->query->get('search');
        $sortField = $request->query->get('sort', 'id');
        $sortOrder = $request->query->get('order', 'ASC');

        if ($search) {
            $events = $this->eventsRepository->findByNameAndSorted($search, $sortField, $sortOrder);
        } else {
            $events = $this->eventsRepository->findAllSorted($sortField, $sortOrder);
        }

        return $this->render('admin/Events/index.html.twig', [
            'events' => $events,
            'search' => $search,
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
        ]);
    }

    #[Route('admin/events/new', name: 'admin_events_new', methods: ['GET', 'POST'])]
    public function news(Request $request): Response
    {
        $event = new Events();

        if ($request->isMethod('POST')) {
            $nomEvent = $request->get('nomEvent');
            $debutEvent = new \DateTime($request->get('debutEvent'));
            $finEvent = new \DateTime($request->get('finEvent'));
            $typeEvent = $request->get('typeEvent');
            $idClub = (int)$request->get('idClub');
            $capacite = (int)$request->get('capacite');
            $descriptionEvent = $request->get('descriptionEvent');
            $membreInscrits = json_decode($request->get('membreInscrits'), true);
            $ressources = json_decode($request->get('ressources'), true);

            $uploadedFile = $request->files->get('photo');
            if ($uploadedFile) {
                $imageFileName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();
                $uploadedFile->move(
                    $this->getParameter('image_directory'),
                    $imageFileName
                );
                $event->setPhoto($imageFileName);
            }

            $errors = [];
            if ($debutEvent < new \DateTime()) {
                $errors[] = "The start date must be today or later.";
            }
            if ($finEvent <= $debutEvent) {
                $errors[] = "The end date must be after the start date.";
            }

            if (!empty($errors)) {
                return $this->render('events/new.html.twig', [
                    'event' => $event,
                    'errors' => $errors,
                ]);
            }

            $event->setNomEvent($nomEvent)
                ->setDebutEvent($debutEvent)
                ->setFinEvent($finEvent)
                ->setTypeEvent($typeEvent)
                ->setIdClub($idClub)
                ->setCapacite($capacite)
                ->setDateCreation(new \DateTime())
                ->setDescriptionEvent($descriptionEvent)
                ->setMembreInscrits($membreInscrits)
                ->setRessources($ressources);

            $this->entityManager->persist($event);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_events_index');
        }

        return $this->render('admin/events/addevent.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/admin/new/innovation', name: 'admin_innovation_new', methods: ['GET', 'POST'])]
    public function newa(Request $request): Response
    {
        $innovation = new Innovation();

        // Variable pour stocker les erreurs de validation
        $errors = [];

        if ($request->isMethod('POST')) {
            // Récupérer les données du formulaire
            $idMembre = $request->get('idMembre');
            $nomClub = $request->get('nomClub');
            $titre = $request->get('titre');
            $dateCreationInnovation = new \DateTime($request->get('dateCreationInnovation'));
            $statusInnovation =  $request->get('statusInnovation');  // Valeur par défaut

            // Validation de l'ID Membre
            if (strlen($idMembre) !== 8) {
                $errors[] = "L'ID Membre doit avoir 8 chiffres.";
            }

            // Validation du nom du club
            if (empty($nomClub)) {
                $errors[] = "Le nom du club ne peut pas être vide.";
            }

            // Validation du titre
            if (empty($titre)) {
                $errors[] = "Le titre ne peut pas être vide.";
            }

            // Validation de la date de création (doit être égale à la date d'aujourd'hui)
            $today = new \DateTime();
            if ($dateCreationInnovation->format('Y-m-d') !== $today->format('Y-m-d')) {
                $errors[] = "La date de création doit être aujourd'hui.";
            }

            // Si des erreurs sont présentes, renvoyer l'utilisateur vers le formulaire avec les erreurs
            if (count($errors) > 0) {
                return $this->render('admin/innovation/addinnovation.html.twig', [
                    'errors' => $errors,
                    'innovation' => $innovation,
                ]);
            }

            // Si aucune erreur, remplir l'objet et persister
            $innovation->setIdMembre((int)$idMembre)
                ->setNomClub($nomClub)
                ->setDescriptionInnovation($request->get('descriptionInnovation'))
                ->setTitre($titre)
                ->setDateCreationInnovation($dateCreationInnovation)
                ->setStatusInnovation($statusInnovation);

            // Persist et flush dans la base de données
            $this->entityManager->persist($innovation);
            $this->entityManager->flush();

            // Rediriger vers la liste des innovations après la création
            return $this->redirectToRoute('innovation_index');
        }

        return $this->render('admin/innovation/addinnovation.html.twig', [
            'innovation' => $innovation,
        ]);
    }
    #[Route('admin/showi/{id}', name: 'admin_innovation_show', methods: ['GET'])]
    public function showe(Innovation $innovation): Response
    {
        return $this->render('admin/innovation/showInnovation.html.twig', [
            'innovation' => $innovation,
        ]);
    }


    #[Route('admin/edit/{id}', name: 'admin_innovation_edit', methods: ['GET', 'POST'])]
    public function edivt(Request $request, Innovation $innovation): Response
    {
        if ($request->isMethod('POST')) {
            // Récupérer les données du formulaire
            $idMembre = $request->get('idMembre');
            $nomClub = $request->get('nomClub');
            $descriptionInnovation = $request->get('descriptionInnovation');
            $titre = $request->get('titre');
            $dateCreationInnovation = $request->get('dateCreationInnovation');
            $statusInnovation = $request->get('statusInnovation');
            
            // Validation des champs
            $errors = [];
    
            // Vérifier ID Membre : exactement 8 chiffres
            if (!preg_match('/^\d{8}$/', $idMembre)) {
                $errors[] = "L'ID Membre doit être un nombre de 8 chiffres.";
            }
    
            // Vérifier Nom Club : uniquement des lettres et des espaces
            if (!preg_match('/^[a-zA-Z\s]+$/', $nomClub)) {
                $errors[] = "Le Nom du Club ne doit contenir que des lettres et des espaces.";
            }
    
            // Vérifier Titre : uniquement des lettres, chiffres et espaces
            if (!preg_match('/^[a-zA-Z0-9\s]+$/', $titre)) {
                $errors[] = "Le Titre ne doit contenir que des lettres, des chiffres et des espaces.";
            }
    
            // Vérifier Date de Création : doit correspondre à aujourd'hui
            $currentDate = new \DateTime('today');
            if ($dateCreationInnovation !== $currentDate->format('Y-m-d')) {
                $errors[] = "La Date de Création doit être uniquement aujourd'hui.";
            }
    
            // Vérifier Status Innovation : une valeur valide
            $validStatuses = ['Approuvé', 'En attente', 'Rejeté'];
            if (!in_array($statusInnovation, $validStatuses)) {
                $errors[] = "Le statut sélectionné est invalide.";
            }
    
            // Si des erreurs sont détectées
            if (!empty($errors)) {
                return $this->render('admin/innovation/editInnovation.html.twig', [
                    'innovation' => $innovation,
                    'errors' => $errors,
                ]);
            }
    
            // Mettre à jour l'entité Innovation si aucune erreur
            $innovation->setIdMembre((int)$idMembre)
                ->setNomClub($nomClub)
                ->setDescriptionInnovation($descriptionInnovation)
                ->setTitre($titre)
                ->setDateCreationInnovation(new \DateTime($dateCreationInnovation))
                ->setStatusInnovation($statusInnovation);
    
            $this->entityManager->flush();
    
            // Rediriger vers l'index après la mise à jour
            return $this->redirectToRoute('innovation_index');
        }
    
        // Affichage initial du formulaire
        return $this->render('admin/innovation/editInnovation.html.twig', [
            'innovation' => $innovation,
            'errors' => [], // Pas d'erreurs au chargement initial
        ]);
    }




    #[Route('/edit/{id}', name: 'admin_events_edit', methods: ['GET', 'POST'])]
    public function editt(Request $request, Events $event): Response
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

            return $this->redirectToRoute('admin_events_index');
        }

        return $this->render('admin/Events/editEvent.html.twig', [
            'event' => $event,
            'errors' => [], // Aucune erreur à afficher lors de l'affichage du formulaire d'édition
        ]);
    }
    #[Route('admin/show/{id}', name: 'admin_events_show', methods: ['GET'])]
    public function showw($id): Response
    {
        // Utilisez find() pour récupérer l'événement par ID
        $event = $this->eventsRepository->find($id);

        if (!$event) {
            throw $this->createNotFoundException('No event found for id ' . $id);
        }

        return $this->render('admin/events/showEvent.html.twig', [
            'event' => $event,
        ]);
    }


    #[Route('admin/delete/{id}', name: 'admin_events_delete', methods: ['POST'])]
    public function delete(Request $request, Events $event): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($event);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_events_index');
    }
    #[Route('adminn/delete/{id}', name: 'admin_innovation_delete', methods: ['POST'])]
    public function deletee(Request $request, Innovation $innovation): Response
    {
        if ($this->isCsrfTokenValid('delete' . $innovation->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($innovation);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_innovation_index');
    }
    

    
    // -------------------------------Partenaire-------------------------------------
 
#[Route('/admin/partenaire', name: 'admin_partenaire_index')]
public function indexj(Request $request, PartenaireRepository $partenaireRepository): Response
{
    // Ensure only admins can access this route

   $partenaires = $partenaireRepository->findAll();

    return $this->render('admin/Partenaire/index.html.twig', [
        'partenaires' => $partenaires,
    ]);
}

    #[Route('/admin/partenaire/new', name: 'admin_partenaire_new')]
    public function new(Request $request, TwilioService $twilioService, EntityManagerInterface $entityManager): Response
    {
        // Make sure only admins can access this route
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $partenaire = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->$entityManager->persist($partenaire);
            $this->$entityManager->flush();
            $message = sprintf(
                "Bonjour %s, votre partenariat avec notre club a été enregistré avec succès !",
                $partenaire->getNomPartenaire()
            );
            $twilioService->sendSMS($partenaire->getTelPartenaire(), $message);

            // Flash success message
            $this->addFlash('success', 'Le partenariat a été créé avec succès.');

            return $this->redirectToRoute('admin_partenaire_index');
        }

        return $this->render('admin/Partenaire/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/partenaire/{id}', name: 'admin_partenaire_show')]
    public function show(Partenaire $partenaire): Response
    {
        // Make sure only admins can access this route
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/Partenaire/show.html.twig', [
            'partenaire' => $partenaire,
        ]);
    }

    #[Route('/admin/partenaire/{id}/edit', name: 'admin_partenaire_edit')]
    public function edit(Request $request, Partenaire $partenaire, EntityManagerInterface $entityManager): Response
    {
        // Make sure only admins can access this route
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->$entityManager->flush();
            $this->addFlash('success', 'Le partenaire a été mis à jour avec succès.');

            return $this->redirectToRoute('admin_partenaire_show', ['id' => $partenaire->getIdPartenaire()]);
        }

        return $this->render('admin/Partenaire/edit.html.twig', [
            'form' => $form->createView(),
            'partenaire' => $partenaire,
        ]);
    }

    #[Route('/admin/partenaire/{id}/delete', name: 'admin_partenaire_delete')]
    public function deletei(Request $request, Partenaire $partenaire, EntityManagerInterface $entityManager): Response
    {
        // Make sure only admins can access this route
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $partenaire->getIdPartenaire(), $request->get('_token'))) {
            $this->$entityManager->remove($partenaire);
            $this->$entityManager->flush();
            $this->addFlash('success', 'Le partenaire a été supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_partenaire_index');
    }
 
}