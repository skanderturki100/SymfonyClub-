<?php


namespace App\Controller;
use Symfony\Component\String\Slugger\SluggerInterface;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use App\Repository\RessourcesRepository;
use App\Repository\ReservationRepository;

use App\Form\ReservationType;
use App\Entity\Reservation;
use App\Entity\Ressources;
use App\Form\RessourcesType;
use App\Service\PdfService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use App\Service\MailerService;



class UserVueController extends AbstractController
{
    private $entityManager;
    private $slugger;

   
    
    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger; // Injecter le SluggerInterface

    }
    

    #[Route('/ressourcess', name: 'app_ressourcess')]
    public function index(RessourcesRepository $ressourcesRepository): Response
    {
        // Récupérer toutes les ressources
        $ressources = $ressourcesRepository->findAll();

        
        

    // Vérifier si une ressource est en rupture de stock
          foreach ($ressources as $ressource) {
        if ($ressource->getQuantite() == 0) {
        $this->addFlash('warning', "La ressource {{ ressource.nom }} est en rupture de stock.");
    }
}

        // Passer les ressources à la vue
        return $this->render('user_vue/indexuserressource.html.twig', [
            'ressources' => $ressources,
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
            return $this->render('reservations/new.html.twig', [
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
        return $this->redirectToRoute('app_ressourcess');
    }

    // Afficher le formulaire
    return $this->render('user_vue_reservationdashboard/newreservation.html.twig', [
        'form' => $form->createView(),
    ]);
}




#[Route('/ressources/pdf', name: 'app_ressources_pdf')]
public function generateAllResourcesPdf(RessourcesRepository $ressourcesRepository, PdfService $pdf): Response
{
    // Récupérer toutes les ressources
    $ressources = $ressourcesRepository->findAll();

    // Créer un HTML personnalisé pour afficher toutes les ressources
    $html = $this->renderView('ressources/show_pdf.html.twig', [
        'ressources' => $ressources,
    ]);

    // Générer le PDF à partir du HTML
    $pdf->generatePdfFromHtml($html, 'ressources.pdf');

    // Retourner la réponse PDF
    return new Response('', 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="ressources.pdf"',
    ]);
}


    
    #[Route('/ressourcess/{id}', name: 'app_ressources_showw')]
    public function show(Ressources $ressource): Response
    {
        return $this->render('ressources/show.html.twig', [
            'ressource' => $ressource,
        ]);


    }

    #[Route('/ressources/{id}', name: 'app_ressources_showw')]
public function showQr(Ressources $ressource): Response
{
    // Récupérer les réservations associées à la ressource
    $reservations = $ressource->getReservations();

    // Générer les données à encoder dans le QR Code
    $reservationDetails = [];
    foreach ($reservations as $reservation) {
        $reservationDetails[] = [
            'motif' => $reservation->getMotif(),
            'dateDebut' => $reservation->getDateDebut()->format('Y-m-d H:i'),
            'dateFin' => $reservation->getDateFin()->format('Y-m-d H:i'),
            'quantite' => $reservation->getQuantite(),
            
            

        ];
    }

    // Convertir les données en JSON
    $dataToEncode = json_encode([
        'ressourceId' => $ressource->getId(),
        'ressourceName' => $ressource->getNom(),
        'reservations' => $reservationDetails,
    ]);

    // Générer le QR Code
    $qrCode = Builder::create()
        ->writer(new PngWriter())
        ->data($dataToEncode)
        ->size(300)
        ->margin(10)
        ->build();

    // Obtenir le QR Code en format Data URI
    $qrCodeDataUri = $qrCode->getDataUri();

    return $this->render('user_vue/showuserressource.html.twig', [
        'ressource' => $ressource,
        'qrCode' => $qrCodeDataUri,
        'reservations' => $reservations,
    ]);
}









    
}





