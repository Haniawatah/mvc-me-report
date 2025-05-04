<?php

namespace App\Controller;

use App\Card\CardHand;
use App\Card\DeckOfCards;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CardController extends AbstractController
{
    #[Route('/card', name: 'card_index')]
    public function index(): Response
    {
        return $this->render('card/index.html.twig', [
            'uml_diagram' => '/img/card-uml-diagram.png'
        ]);
    }

    #[Route('/card/deck', name: 'card_deck')]
    public function deck(SessionInterface $session): Response
    {
        $deck = new DeckOfCards();
        $deck->sortDeck();
        $session->set("deck", $deck);
        
        return $this->render('card/deck.html.twig', [
            'cards' => $deck->getCards(),
            'count' => $deck->getCount()
        ]);
    }

    #[Route('/card/deck/shuffle', name: 'card_shuffle')]
    public function shuffle(SessionInterface $session): Response
    {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $session->set("deck", $deck);
        
        return $this->render('card/shuffle.html.twig', [
            'cards' => $deck->getCards(),
            'count' => $deck->getCount()
        ]);
    }

    #[Route('/card/deck/draw', name: 'card_draw')]
    public function draw(SessionInterface $session): Response
    {
        if (!$session->has("deck")) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("deck", $deck);
        }
        
        $deck = $session->get("deck");
        $drawnCards = $deck->draw(1);
        $session->set("deck", $deck);
        
        return $this->render('card/draw.html.twig', [
            'cards' => $drawnCards,
            'count' => $deck->getCount(),
            'remaining' => $deck->getCount()
        ]);
    }

    #[Route('/card/deck/draw/{number}', name: 'card_draw_multiple', requirements: ['number' => '\d+'])]
    public function drawMultiple(int $number, SessionInterface $session): Response
    {
        if (!$session->has("deck")) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("deck", $deck);
        }
        
        $deck = $session->get("deck");
        $drawnCards = $deck->draw($number);
        $session->set("deck", $deck);
        
        return $this->render('card/draw_multiple.html.twig', [
            'cards' => $drawnCards,
            'number' => $number,
            'count' => count($drawnCards),
            'remaining' => $deck->getCount()
        ]);
    }

    #[Route('/card/deck/deal/{players}/{cards}', name: 'card_deal', requirements: ['players' => '\d+', 'cards' => '\d+'])]
    public function deal(int $players, int $cards, SessionInterface $session): Response
    {
        if (!$session->has("deck")) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set("deck", $deck);
        }
        
        $deck = $session->get("deck");
        $hands = $deck->deal($players, $cards);
        $session->set("deck", $deck);
        
        return $this->render('card/deal.html.twig', [
            'hands' => $hands,
            'players' => $players,
            'cards' => $cards,
            'remaining' => $deck->getCount()
        ]);
    }
}
