<?php

// src/Controller/StatistiquesController.php
namespace App\Controller;

use App\Repository\RessourcesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatistiquesController extends AbstractController
{
    #[Route('/statistiques', name: 'app_statistiques')]
    public function showStats(RessourcesRepository $ressourcesRepository): Response
    {
        // Récupérer toutes les ressources
        $ressources = $ressourcesRepository->findAll();

        // Calcul des statistiques
        $totalResources = count($ressources); 
        $totalInStock = count(array_filter($ressources, fn($r) => $r->getQuantite() > 0)); 
        $totalInMaintenance = count(array_filter($ressources, fn($r) => $r->getEtat() === 'maintenance'));
        $totalNotInStock = $totalResources - $totalInStock; 
        $totalAvailable = $totalInStock - $totalInMaintenance;

        // Passer les variables à la vue
        return $this->render('statistiques/index.html.twig', [
            'total_resources' => $totalResources,
            'total_in_stock' => $totalInStock,
            'total_in_maintenance' => $totalInMaintenance,
            'total_not_in_stock' => $totalNotInStock,
            'total_available' => $totalAvailable,
        ]);
    }
}
