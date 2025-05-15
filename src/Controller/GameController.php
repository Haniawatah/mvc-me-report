<?php

namespace App\Controller;

use App\Game\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends AbstractController
{
    #[Route('/game', name: 'game_landing')]
    public function landing(): Response
    {
        return $this->render('game/landing.html.twig');
    }

    #[Route('/game/doc', name: 'game_doc')]
    public function doc(): Response
    {
        return $this->render('game/doc.html.twig');
    }

    #[Route('/game/init', name: 'game_init')]
    public function init(SessionInterface $session): Response
    {
        $game = new Game();
        $game->init();
        $session->set('game', $game);
        
        return $this->redirectToRoute('game_play');
    }

    #[Route('/game/play', name: 'game_play')]
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
            'gameState' => $game->getGameState()
        ]);
    }

    #[Route('/game/player/hit', name: 'game_player_hit')]
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

    #[Route('/game/player/stand', name: 'game_player_stand')]
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

    #[Route('/game/reset', name: 'game_reset')]
    public function reset(SessionInterface $session): Response
    {
        $session->remove('game');
        
        return $this->redirectToRoute('game_init');
    }
}
