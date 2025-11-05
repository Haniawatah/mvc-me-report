<?php

namespace App\Controller;

use App\Game\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * Game Controller class.
 *
 * This controller handles card game functionality and related API endpoints.
 *
 * @OA\Tag(
 *     name="Game",
 *     description="Game related operations"
 * )
 */
#[Route('/game')]
class GameController extends AbstractController
{
    #[Route('/', name: 'game_landing')]
    public function landing(): Response
    {
        return $this->render('game/landing.html.twig');
    }

    #[Route('/doc', name: 'game_doc')]
    public function doc(): Response
    {
        return $this->render('game/doc.html.twig');
    }

    #[Route('/init', name: 'game_init')]
    public function init(SessionInterface $session): Response
    {
        $game = new Game();
        $game->init();

        $session->set('game', $game);

        return $this->redirectToRoute('game_play');
    }

    #[Route('/play', name: 'game_play')]
    public function play(SessionInterface $session): Response
    {
        if (!$session->has('game')) {
            return $this->redirectToRoute('game_init');
        }

        $game = $session->get('game');

        return $this->render('game/play.html.twig', [
            'game' => $game,
            'playerHand' => $game->getPlayerHand(),
            'dealerHand' => $game->getDealerHand(),
            'playerScore' => $game->getPlayerScore(),
            'dealerScore' => $game->getDealerScore(),
            'gameState' => $game->getGameState(),
            'result' => $game->getResult(),
        ]);
    }

    #[Route('/player/hit', name: 'game_player_hit')]
    public function playerHit(SessionInterface $session): Response
    {
        if (!$session->has('game')) {
            return $this->redirectToRoute('game_init');
        }

        $game = $session->get('game');
        $game->playerHit();
        $session->set('game', $game);

        return $this->redirectToRoute('game_play');
    }

    #[Route('/player/stand', name: 'game_player_stand')]
    public function playerStand(SessionInterface $session): Response
    {
        if (!$session->has('game')) {
            return $this->redirectToRoute('game_init');
        }

        $game = $session->get('game');
        $game->playerStand();
        $session->set('game', $game);

        return $this->redirectToRoute('game_play');
    }

    #[Route('/reset', name: 'game_reset')]
    public function reset(SessionInterface $session): Response
    {
        $session->remove('game');

        return $this->redirectToRoute('game_init');
    }

    /**
     * API endpoint to get the current game state.
     *
     * @Route("/api/game", name="api_game_state", methods={"GET"})
     *
     * @OA\Get(
     *     path="/api/game",
     *     summary="Get current game state",
     *     description="Returns the current state of the game",
     *     tags={"Game"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="gameState", type="string", example="active"),
     *             @OA\Property(property="deck", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     *
     * @return JsonResponse Current game state
     */
    public function getGameState(): JsonResponse
    {
        // ... implementation code ...

        return $this->json([
            'gameState' => 'active',
            'deck' => ['card1', 'card2', '...']
        ]);
    }
}
