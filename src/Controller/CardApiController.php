<?php

namespace App\Controller;

use App\Card\DeckOfCards;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CardApiController extends AbstractController
{
    #[Route('/api/deck', name: 'api_deck', methods: ['GET'])]
    public function getDeck(): JsonResponse
    {
        $deck = new DeckOfCards();
        $deck->sortDeck();
        
        return $this->json([
            'deck' => $deck->getAsJson(),
            'count' => $deck->getCount()
        ]);
    }

    #[Route('/api/deck/shuffle', name: 'api_shuffle', methods: ['POST'])]
    public function shuffleDeck(SessionInterface $session): JsonResponse
    {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $session->set("deck", $deck);
        
        return $this->json([
            'deck' => $deck->getAsJson(),
            'count' => $deck->getCount()
        ]);
    }

    #[Route('/api/deck/draw', name: 'api_draw', methods: ['POST'])]
    public function drawCard(SessionInterface $session): JsonResponse
    {
        if (!$session->has("deck")) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("deck", $deck);
        }
        
        $deck = $session->get("deck");
        $drawnCards = $deck->draw(1);
        $session->set("deck", $deck);
        
        $cardsJson = [];
        foreach ($drawnCards as $card) {
            $cardsJson[] = [
                'suit' => $card->getSuit(),
                'value' => $card->getValue(),
                'symbol' => $card->getSuitSymbol(),
                'representation' => $card->getAsString()
            ];
        }
        
        return $this->json([
            'cards' => $cardsJson,
            'count' => count($drawnCards),
            'remaining' => $deck->getCount()
        ]);
    }

    #[Route('/api/deck/draw/{number}', name: 'api_draw_multiple', methods: ['POST'], requirements: ['number' => '\d+'])]
    public function drawMultipleCards(int $number, SessionInterface $session): JsonResponse
    {
        if (!$session->has("deck")) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("deck", $deck);
        }
        
        $deck = $session->get("deck");
        $drawnCards = $deck->draw($number);
        $session->set("deck", $deck);
        
        $cardsJson = [];
        foreach ($drawnCards as $card) {
            $cardsJson[] = [
                'suit' => $card->getSuit(),
                'value' => $card->getValue(),
                'symbol' => $card->getSuitSymbol(),
                'representation' => $card->getAsString()
            ];
        }
        
        return $this->json([
            'cards' => $cardsJson,
            'count' => count($drawnCards),
            'remaining' => $deck->getCount()
        ]);
    }

    #[Route('/api/deck/deal/{players}/{cards}', name: 'api_deal', methods: ['POST'], requirements: ['players' => '\d+', 'cards' => '\d+'])]
    public function dealCards(int $players, int $cards, SessionInterface $session): JsonResponse
    {
        if (!$session->has("deck")) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("deck", $deck);
        }
        
        $deck = $session->get("deck");
        $hands = $deck->deal($players, $cards);
        $session->set("deck", $deck);
        
        $handsJson = [];
        foreach ($hands as $index => $hand) {
            $handsJson[$index + 1] = $hand->getAsJson();
        }
        
        return $this->json([
            'players' => $players,
            'cards_per_player' => $cards,
            'hands' => $handsJson,
            'remaining' => $deck->getCount()
        ]);
    }
}
