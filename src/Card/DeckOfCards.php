<?php

namespace App\Card;

class DeckOfCards
{
    /**
     * @var CardGraphic[] Array of cards
     */
    protected array $cards = [];

    /**
     * @var array Available suits
     */
    protected array $suits = ['Hearts', 'Diamonds', 'Clubs', 'Spades'];

    /**
     * @var array Available values with their numeric values
     */
    protected array $values = [
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        '10' => 10,
        'Jack' => 11,
        'Queen' => 12,
        'King' => 13,
        'Ace' => 14
    ];

    /**
     * Create a new deck of cards
     */
    public function __construct()
    {
        $this->initializeDeck();
    }

    /**
     * Initialize a standard deck of 52 cards
     */
    protected function initializeDeck(): void
    {
        $this->cards = [];
        foreach ($this->suits as $suit) {
            foreach ($this->values as $value => $numericValue) {
                $this->cards[] = new CardGraphic($suit, $value, $numericValue);
            }
        }
    }

    /**
     * Get all cards in the deck
     * @return CardGraphic[]
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * Sort deck by suit and value
     */
    public function sortDeck(): void
    {
        usort($this->cards, function ($firstCard, $secondCard) {
            $suitOrder = array_flip($this->suits);
            
            if ($suitOrder[$firstCard->getSuit()] === $suitOrder[$secondCard->getSuit()]) {
                return $firstCard->getNumericValue() - $secondCard->getNumericValue();
            }
            
            return $suitOrder[$firstCard->getSuit()] - $suitOrder[$secondCard->getSuit()];
        });
    }

    /**
     * Shuffle the deck of cards
     */
    public function shuffle(): void
    {
        shuffle($this->cards);
    }

    /**
     * Draw a specified number of cards from the deck
     * @return CardGraphic[]
     */
    public function draw(int $number = 1): array
    {
        if ($number <= 0) {
            return [];
        }

        $drawnCards = [];
        $cardsCount = count($this->cards);
        for ($i = 0; $i < $number && $i < $cardsCount; $i++) {
            $drawnCards[] = array_shift($this->cards);
        }
        
        return $drawnCards;
    }

    /**
     * Deal cards to a specified number of players
     * @return CardHand[] Array of CardHand objects
     */
    public function deal(int $numPlayers, int $numCards): array
    {
        $hands = [];
        
        // Initialize hands for each player
        for ($i = 0; $i < $numPlayers; $i++) {
            $hands[] = new CardHand();
        }
        
        // Deal cards to each player in turn
        for ($i = 0; $i < $numCards; $i++) {
            foreach ($hands as $hand) {
                $cardsCount = count($this->cards);
                if ($cardsCount > 0) {
                    $card = $this->draw(1)[0];
                    $hand->addCard($card);
                }
            }
        }
        
        return $hands;
    }

    /**
     * Get the number of cards remaining in the deck
     */
    public function getCount(): int
    {
        return count($this->cards);
    }

    /**
     * Get deck as JSON serializable array
     */
    public function getAsJson(): array
    {
        $result = [];
        foreach ($this->cards as $card) {
            $result[] = [
                'suit' => $card->getSuit(),
                'value' => $card->getValue(),
                'symbol' => $card->getSuitSymbol(),
                'representation' => $card->getAsString()
            ];
        }
        return $result;
    }

    /**
     * Reset the deck to its initial state
     */
    public function reset(): void
    {
        $this->initializeDeck();
    }
}
