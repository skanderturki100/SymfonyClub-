<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
        return $this->render('home.html.twig');
    }
    #[Route('/admine', name: 'app_homepag')]
    public function admin(): Response
    {
        return $this->render('page/indexadmin.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }
    #[Route('/starter', name: 'app_starterpage')]
    public function starter(): Response
    {
        return $this->render('page/starter-page.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    #[Route('/service', name: 'app_servicerpage')]
    public function service(): Response
    {
        return $this->render('page/service-detail.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    #[Route('/Portfolio', name: 'app_portfoliopage')]
    public function Portfolio(): Response
    {
        return $this->render('page/Portfolio.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }
}
