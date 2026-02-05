<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjController extends AbstractController
{
    #[Route('/proj', name: 'proj_index')]
    public function index(): Response
    {
        return $this->render('proj/index.html.twig');
    }

    #[Route('/proj/about', name: 'proj_about')]
    public function about(): Response
    {
        return $this->render('proj/about.html.twig');
    }

    #[Route('/proj/play', name: 'proj_play')]
    public function play(): Response
    {
        // Länka till ditt Blackjack-spel
        return $this->redirectToRoute('blackjack');
    }
}
