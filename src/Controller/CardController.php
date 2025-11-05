<?php

namespace App\Controller;

use App\Card\CardHand;
use App\Card\DeckOfCards;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/card')]
class CardController extends AbstractController
{
    #[Route('/', name: 'card_index')]
    public function index(): Response
    {
        return $this->render('card/index.html.twig');
    }

    #[Route('/deck', name: 'card_deck')]
    public function deck(SessionInterface $session): Response
    {
        $deck = new DeckOfCards();
        $session->set('deck', $deck);

        return $this->render('card/deck.html.twig', [
            'cards' => $deck->getCards(),
            'count' => $deck->getCount(),
        ]);
    }

    #[Route('/deck/shuffle', name: 'card_shuffle')]
    public function shuffle(SessionInterface $session): Response
    {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $session->set('deck', $deck);

        return $this->render('card/shuffle.html.twig', [
            'cards' => $deck->getCards(),
            'count' => $deck->getCount(),
        ]);
    }

    #[Route('/deck/draw', name: 'card_draw')]
    public function draw(SessionInterface $session): Response
    {
        if (!$session->has('deck')) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set('deck', $deck);
        }

        $deck = $session->get('deck');
        $cards = $deck->draw(1);
        $session->set('deck', $deck);

        return $this->render('card/draw.html.twig', [
            'cards' => $cards,
            'remaining' => $deck->getCount(),
        ]);
    }

    #[Route('/deck/draw/{number<\d+>}', name: 'card_draw_multiple')]
    public function drawMultiple(int $number, SessionInterface $session): Response
    {
        if (!$session->has('deck')) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set('deck', $deck);
        }

        $deck = $session->get('deck');
        $cards = $deck->draw($number);
        $session->set('deck', $deck);

        return $this->render('card/draw_multiple.html.twig', [
            'cards' => $cards,
            'count' => count($cards),
            'number' => $number,
            'remaining' => $deck->getCount(),
        ]);
    }

    #[Route('/deck/deal/{players<\d+>}/{cards<\d+>}', name: 'card_deal')]
    public function deal(int $players, int $cards, SessionInterface $session): Response
    {
        if (!$session->has('deck')) {
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set('deck', $deck);
        }

        $deck = $session->get('deck');

        // Check if there are enough cards
        $totalNeeded = $players * $cards;
        if ($deck->getCount() < $totalNeeded) {
            // Not enough cards, create a new deck
            $deck = new DeckOfCards();
            $deck->shuffle();
            $session->set('deck', $deck);
        }

        // Create player hands
        $hands = [];
        for ($i = 0; $i < $players; $i++) {
            $hand = new CardHand();
            $drawnCards = $deck->draw($cards);

            foreach ($drawnCards as $card) {
                $hand->addCard($card);
            }

            $hands[] = $hand;
        }

        $session->set('deck', $deck);

        return $this->render('card/deal.html.twig', [
            'players' => $players,
            'cards' => $cards,
            'hands' => $hands,
            'remaining' => $deck->getCount(),
        ]);
    }
}
