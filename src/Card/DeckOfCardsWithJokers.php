<?php

namespace App\Card;

class DeckOfCardsWithJokers extends DeckOfCards
{
    private int $numJokers = 2;

    protected function initializeDeck(): void
    {
        parent::initializeDeck();
        for ($i = 0; $i < $this->numJokers; $i++) {
            $this->cards[] = new JokerCard();
        }
    }
}
