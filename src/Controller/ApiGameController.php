<?php

namespace App\Controller;

use App\Game\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiGameController extends AbstractController
{
    #[Route('/game', name: 'api_game', methods: ['GET'])]
    public function gameStatus(Request $request, SessionInterface $session): Response
    {
        if (!$session->has('game')) {
            $game = new Game();
            $game->init();
            $session->set('game', $game);
        } else {
            $game = $session->get('game');
        }

        $data = [
            'gameState' => $game->getGameState(),
            'result' => $game->getResult(),
            'isGameOver' => $game->isGameOver(),
            'playerHand' => [
                'count' => count($game->getPlayerHand()->getCards()),
                'cards' => []
            ],
            'dealerHand' => [
                'count' => count($game->getDealerHand()->getCards()),
                'cards' => []
            ],
            'playerScore' => $game->getPlayerScore(),
            'dealerScore' => $game->getDealerScore(),
            'actions' => [
                'hit' => $this->generateUrl('game_player_hit'),
                'stand' => $this->generateUrl('game_player_stand'),
                'reset' => $this->generateUrl('game_reset')
            ]
        ];

        // Add card details
        foreach ($game->getPlayerHand()->getCards() as $card) {
            $data['playerHand']['cards'][] = [
                'suit' => $card->getSuit(),
                'value' => $card->getValue(),
                'symbol' => $card->getSymbol(),
                'numericValue' => $this->getNumericValue($card->getValue())
            ];
        }

        foreach ($game->getDealerHand()->getCards() as $card) {
            $data['dealerHand']['cards'][] = [
                'suit' => $card->getSuit(),
                'value' => $card->getValue(),
                'symbol' => $card->getSymbol(),
                'numericValue' => $this->getNumericValue($card->getValue())
            ];
        }

        // Check if request wants JSON
        if ($request->getPreferredFormat() === 'json') {
            return $this->json($data);
        }

        // Otherwise, render the HTML view
        return $this->render('api/game.html.twig', [
            'data' => $data,
            'json' => json_encode($data, JSON_PRETTY_PRINT)
        ]);
    }

    private function getNumericValue(string $value): int
    {
        if (in_array($value, ['Jack', 'Queen', 'King'])) {
            return 10;
        } elseif ($value === 'Ace') {
            return 1; // Or 14, but we calculate that separately
        }
        return intval($value);
    }
}
