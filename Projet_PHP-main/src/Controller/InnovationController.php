<?php

namespace App\Controller;

use App\Entity\Innovation;
use App\Repository\InnovationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/innovation')]
class InnovationController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private InnovationRepository $innovationRepository;

    public function __construct(EntityManagerInterface $entityManager, InnovationRepository $innovationRepository)
    {
        $this->entityManager = $entityManager;
        $this->innovationRepository = $innovationRepository;
    }

    #[Route('/', name: 'innovation_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // Récupérer le terme de recherche pour le statut depuis la requête
        $statusTerm = $request->query->get('status', '');

        // Si un statut est fourni, filtrer les résultats
        if ($statusTerm) {
            // Recherche par le statut
            $innovations = $this->innovationRepository->findByStatus($statusTerm);
        } else {
            // Sinon, afficher toutes les innovations
            $innovations = $this->innovationRepository->findAll();
        }

        return $this->render('innovation/index.html.twig', [
            'innovations' => $innovations,
            'statusTerm' => $statusTerm, // Passer le statut de recherche pour pré-remplir le champ
        ]);
    }

    #[Route('/new', name: 'innovation_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
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
                return $this->render('innovation/new.html.twig', [
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

        return $this->render('innovation/new.html.twig', [
            'innovation' => $innovation,
        ]);
    }

    #[Route('/{id}', name: 'innovation_show', methods: ['GET'])]
    public function show(Innovation $innovation): Response
    {
        return $this->render('innovation/show.html.twig', [
            'innovation' => $innovation,
        ]);
    }

    #[Route('/edit/{id}', name: 'innovation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Innovation $innovation): Response
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
                return $this->render('innovation/edit.html.twig', [
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
        return $this->render('innovation/edit.html.twig', [
            'innovation' => $innovation,
            'errors' => [], // Pas d'erreurs au chargement initial
        ]);
    }
    
    #[Route('/{id}', name: 'innovation_delete', methods: ['POST'])]
    public function delete(Request $request, Innovation $innovation): Response
    {
        if ($this->isCsrfTokenValid('delete' . $innovation->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($innovation);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('innovation_index');
    }
   
}
