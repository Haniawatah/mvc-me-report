<?php

namespace App\Controller;

use App\Game\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * API Controller class.
 * 
 * This controller handles API endpoints and documentation.
 * 
 * @OA\Info(
 *     title="MVC Course API",
 *     version="1.0.0",
 *     description="API Documentation for MVC Course",
 *     @OA\Contact(
 *         email="student@example.com",
 *         name="MVC Student"
 *     )
 * )
 * @OA\Server(
 *     url="/",
 *     description="MVC API Server"
 * )
 * @OA\Tag(
 *     name="API",
 *     description="API Endpoints"
 * )
 */
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
            "Life is what happens when you're busy making other plans.",
            "The way to get started is to quit talking and begin doing.",
            "Your time is limited, so don't waste it living someone else's life.",
            "If life were predictable it would cease to be life, and be without flavor.",
            "When you reach the end of your rope, tie a knot in it and hang on."
        ];
        
        $randomQuote = $quotes[array_rand($quotes)];
        
        $data = [
            'quote' => $randomQuote,
            'date' => date('Y-m-d'),
            'timestamp' => time()
        ];
        
        return $this->json($data);
    }

    /**
     * Display API documentation.
     * 
     * @Route("/api", name="api_documentation")
     * 
     * @return Response The rendered API documentation page
     */
    public function apiDocumentation(): Response
    {
        return $this->render('api/documentation.html.twig');
    }

    /**
     * Example API endpoint.
     * 
     * @Route("/api/example", name="api_example", methods={"GET"})
     * 
     * @OA\Get(
     *     path="/api/example",
     *     summary="Example API endpoint",
     *     description="Returns a simple example response",
     *     tags={"API"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="This is an example API response")
     *         )
     *     )
     * )
     * 
     * @return JsonResponse A sample API response
     */
    public function exampleEndpoint(): JsonResponse
    {
        return $this->json([
            'message' => 'This is an example API response'
        ]);
    }
}
