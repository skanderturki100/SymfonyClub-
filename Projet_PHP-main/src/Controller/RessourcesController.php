<?php


namespace App\Controller;
use Symfony\Component\String\Slugger\SluggerInterface;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\RessourcesRepository;



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



class RessourcesController extends AbstractController
{
    private $entityManager;
    private $slugger;

   
    
    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger; // Injecter le SluggerInterface

    }
    

    #[Route('/ressources', name: 'app_ressources')]
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
        return $this->render('ressources/index.html.twig', [
            'ressources' => $ressources,
        ]);
    }

    
    #[Route('/ressources/new', name: 'app_ressources_new')]
public function new(Request $request, SluggerInterface $slugger): Response
{
    $ressource = new Ressources();
    $form = $this->createForm(RessourcesType::class, $ressource, [
        'is_new' => true, // Photo obligatoire
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $photoFile = $form->get('photo')->getData();
        if ($photoFile) {
            $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

            try {
                $photoFile->move(
                    $this->getParameter('photos_directory'),
                    $newFilename
                );
                $ressource->setPhoto($newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors du téléchargement de la photo.');
            }
            
        }

        $this->entityManager->persist($ressource);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_ressources');
    }

    return $this->render('ressources/new.html.twig', [
        'form' => $form->createView(),
    ]);
}


#[Route('/ressources/{id}/edit', name: 'app_ressources_edit')]
public function edit(Request $request, Ressources $ressource): Response
{
    $form = $this->createForm(RessourcesType::class, $ressource, [
        'is_new' => false, // Photo facultative
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $photoFile = $form->get('photo')->getData();
        if ($photoFile) {
            $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

            try {
                $photoFile->move(
                    $this->getParameter('photos_directory'),
                    $newFilename
                );

                if ($ressource->getPhoto()) {
                    $oldPhotoPath = $this->getParameter('photos_directory') . '/' . $ressource->getPhoto();
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }

                $ressource->setPhoto($newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors du téléchargement de la photo.');
            }
        }

        $this->entityManager->flush();

        $this->addFlash('success', 'La ressource a été modifiée avec succès.');

        return $this->redirectToRoute('app_ressources');
    }

    return $this->render('ressources/edit.html.twig', [
        'form' => $form->createView(),
        'ressource' => $ressource,
    ]);
}


#[Route('/ressources/pdf', name: 'app_ressources_pdf')]
public function generatePdf(RessourcesRepository $ressourcesRepository , PdfService $pdf)
{
    $ressources = $ressourcesRepository->findAll();

    // Calculer les statistiques
    $totalResources = count($ressources);
    $totalInStock = count(array_filter($ressources, fn($r) => $r->getQuantite() > 0));
    $totalInMaintenance = count(array_filter($ressources, fn($r) => $r->getEtat() === 'maintenance'));
    $totalNotInStock = $totalResources - $totalInStock;

    // Passer les données au template
    $html = $this->renderView('ressources/show_pdf.html.twig', [
        'ressources' => $ressources,
        'total_resources' => $totalResources,
        'total_in_stock' => $totalInStock,
        'total_in_maintenance' => $totalInMaintenance,
        'total_not_in_stock' => $totalNotInStock,
    ]);

    // Générer le PDF (exemple avec TCPDF ou DOMPDF)
    $pdf->generatePdfFromHtml($html, 'ressources.pdf');
}




    #[Route('/ressources/{id}', name: 'app_ressources_show')]
    public function show(Ressources $ressource): Response
    {
        return $this->render('ressources/show.html.twig', [
            'ressource' => $ressource,
        ]);


    }

    #[Route('/ressource/{id}', name: 'app_ressources_show')]
public function showQr(Ressources $ressource): Response
{

    // Récupérer les réservations associées à la ressource
    $reservations = $ressource->getReservations();

    // lencodage de la code qr 
    $reservationDetails = [];
    foreach ($reservations as $reservation) {
        $reservationDetails[] = [
            'motif' => $reservation->getMotif(),
            'dateDebut' => $reservation->getDateDebut()->format('Y-m-d H:i'),
            'dateFin' => $reservation->getDateFin()->format('Y-m-d H:i'),
            'quantite' => $reservation->getQuantite(),
        ];
    }

    // on convertir le code en json 
    $dataToEncode = json_encode([
        'ressourceId' => $ressource->getId(),
        'ressourceName' => $ressource->getNom(),
        'reservations' => $reservationDetails,
    ]);

    // est ici on genere le ocde qr 
    $qrCode = Builder::create()
        ->writer(new PngWriter())
        ->data($dataToEncode)
        ->size(300)
        ->margin(10)
        ->build();

    // Obtenir le QR Code en format Data URI
    $qrCodeDataUri = $qrCode->getDataUri();

    return $this->render('ressources/show.html.twig', [
        'ressource' => $ressource,
        'qrCode' => $qrCodeDataUri,
        'reservations' => $reservations,
    ]);
}




    #[Route('/ressources/{id}/delete', name: 'app_ressources_delete')]
    public function delete(Ressources $ressource): Response
    {
        $this->entityManager->remove($ressource);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_ressources');
    }





   
    




     
     
  
   
    
    
    


    
}





