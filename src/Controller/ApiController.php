<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'api_index')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig');
    }

    #[Route('/api/quote', name: 'api_quote')]
    public function quote(): JsonResponse
    {
        $quotes = [
            'Life is what happens when you\'re busy making other plans.',
            'The future belongs to those who believe in the beauty of their dreams.',
            'In the end, we will remember not the words of our enemies, but the silence of our friends.'
        ];
        
        $randomQuote = $quotes[array_rand($quotes)];
        
        return $this->json([
            'quote' => $randomQuote,
            'date' => date('Y-m-d'),
            'timestamp' => time()
        ]);
    }
}