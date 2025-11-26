<?php

declare(strict_types=1);

namespace App\Blackjack;

class Deck
{
    /** @var Card[] */
    private array $cards = [];

    public function __construct(int $decks = 1)
    {
        $ranks = ['A','2','3','4','5','6','7','8','9','10','J','Q','K'];
        $suits = ['♠','♥','♦','♣'];
        for ($d = 0; $d < $decks; $d++) {
            foreach ($suits as $s) {
                foreach ($ranks as $r) {
                    $this->cards[] = new Card($r, $s);
                }
            }
        }
        $this->shuffle();
    }

    public function shuffle(): void
    {
        shuffle($this->cards);
    }

    public function draw(): Card
    {
        $card = array_pop($this->cards);
        if (!$card) {
            $this->__construct();
            $card = array_pop($this->cards);
        }
        return $card;
    }
}
