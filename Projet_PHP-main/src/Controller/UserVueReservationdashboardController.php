<?php

namespace App\Controller;

use App\Repository\ReservationRepository;


use App\Repository\RessourcesRepository;
use App\Entity\Reservation;
use App\Entity\Ressources;
use App\Form\ReservationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\MailerService;

class UserVueReservationdashboardController extends AbstractController

{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/reservationss', name: 'app_reservationss')]
public function index(RessourcesRepository $ressourceRepository, ReservationRepository $reservationRepository): Response
{
    // Récupérer toutes les réservations
    $reservations = $reservationRepository->findAll();

    return $this->render('user_vue_reservationdashboard/indexreservation.html.twig', [
        'reservations' => $reservations,
    ]);
}

#[Route('/reservations/neww', name: 'app_reservations_neww')]
public function new(
    Request $request,
    RessourcesRepository $ressourceRepository,
    
    EntityManagerInterface $entityManager,

    MailerService $mailer


): Response {
    $reservation = new Reservation();
    
    // Récupérer l'ID de la ressource passé dans l'URL
    $ressourceId = $request->query->get('ressourceId');

    // Si l'ID de la ressource est fourni dans l'URL
    if ($ressourceId) {
        // Trouver la ressource dans la base de données
        $ressource = $ressourceRepository->find($ressourceId);

        // Si la ressource existe, la pré-remplir dans le formulaire
        if ($ressource) {
            $reservation->setRessource($ressource);
        }
    }

    // Créer le formulaire de réservation
    $form = $this->createForm(ReservationType::class, $reservation);
    $form->handleRequest($request);

    // Vérification avant l'enregistrement de la réservation
    if ($form->isSubmitted() && $form->isValid()) {
        // Vérifier si la quantité demandée est disponible
        if ($reservation->getQuantite() > $reservation->getRessource()->getQuantite()) {
            $this->addFlash('error', 'La quantité demandée dépasse la quantité disponible de cette ressource.');

            // Réinitialiser la quantité à une valeur invalide (comme 0)
            $reservation->setQuantite(0); // Remplace par 0 ou laisse vide pour indiquer une erreur

            // Afficher à nouveau le formulaire avec les autres valeurs remplies
            return $this->render('user_vue_reservationdashboard/newreservation.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        // Enregistrer la réservation
        $entityManager->persist($reservation);
        $entityManager->flush();

        // Mettre à jour la quantité de la ressource
        $ressource->setQuantite($ressource->getQuantite() - $reservation->getQuantite());
        $entityManager->persist($ressource);
        $entityManager->flush();


       // Assurez-vous d'avoir la bonne structure du message.
$message = 'La ressource réservée : ' . $reservation->getRessource()->__toString();  // Utilisez __toString() pour afficher la ressource sous forme de chaîne
$message .= '<br>Motif de la réservation : ' . $reservation->getMotif();  // Ajouter le motif
$message .= '<br>Quantité réservée : ' . $reservation->getQuantite();  // Ajouter la quantité réservée

// Envoi de l'email avec le message structuré
$mailer->sendEmail(content: $message);

       
        
       
        // Afficher un message de succès
        $this->addFlash('success', 'La réservation a été effectuée avec succès.');
       
        

        // Rediriger vers la liste des réservations
        return $this->redirectToRoute('app_reservationss');
    }

    // Afficher le formulaire
    return $this->render('user_vue_reservationdashboard/newreservation.html.twig', [
        'form' => $form->createView(),
    ]);
}




    

    #[Route('/reservations/{id}/edit', name: 'app_reservations_edit')]
    public function edit(Request $request, Reservation $reservation): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Utilisation de l'EntityManager injecté pour flusher les changements
            $this->entityManager->flush();

            return $this->redirectToRoute('app_reservations');
        }

        return $this->render('reservations/edit.html.twig', [
            'form' => $form->createView(),
            'reservation' => $reservation,
        ]);
    }

    #[Route('/reservations/{id}', name: 'app_reservations_show')]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservations/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    

    #[Route('/reservations/{id}/delete', name: 'app_reservations_delete')]
    public function delete(Reservation $reservation): Response
    {
        $this->entityManager->remove($reservation);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_reservations');
    }


    


    public function create(Request $request, EntityManagerInterface $em)
    {
        $reservation = new Reservation();
    
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer la ressource à partir de la réservation
            $ressource = $em->getRepository(Ressources::class)->find($reservation->getRessource()->getId());
    
            // Vérification de l'état de la ressource
            if ($ressource->getEtat() === 'en maintenance') {
                $this->addFlash('error', 'La ressource est en maintenance et ne peut pas être réservée.');
                return $this->redirectToRoute('app_reservation_new');
            }
    
            // Vérification de la quantité disponible
            if ($reservation->getQuantite() > $ressource->getQuantite()) {
                $this->addFlash('error', 'La quantité demandée dépasse la quantité disponible.');
                return $this->redirectToRoute('app_reservation_new');
            }
    
            // Persist la réservation
            $em->persist($reservation);
            $em->flush(); // Flusher la réservation dans la base de données
    
            // Après avoir persisté la réservation, mettre à jour la quantité de la ressource
            $ressource->setQuantite($ressource->getQuantite() - $reservation->getQuantite());
            $em->persist($ressource); // Persister la ressource mise à jour
            $em->flush(); // Flusher la ressource mise à jour dans la base de données
    
            $this->addFlash('success', 'Réservation effectuée avec succès!');
            return $this->redirectToRoute('app_reservation_index');
        }
    
        return $this->render('reservation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    









    
}