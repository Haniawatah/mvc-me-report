<?php

namespace App\Card;

class DeckOfCardsWithJokers extends DeckOfCards
{
    /**
     * @var int Number of jokers to include
     */
    private int $numJokers = 2;

    /**
     * Initialize a deck of 52 cards + jokers
     */
    protected function initializeDeck(): void
    {
        parent::initializeDeck();
        
        // Add jokers
        for ($i = 0; $i < $this->numJokers; $i++) {
            $this->cards[] = new CardGraphic('Joker', 'Joker', 15);
        }
    }
}
