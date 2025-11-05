<?php

namespace App\Card;

/**
 * Deck of cards class
 *
 * Represents a standard deck of 52 playing cards
 */
class DeckOfCards
{
    /**
     * @var array<CardGraphic> Array of Card objects in the deck
     */
    protected array $cards = [];

    /**
     * @var array Possible suits for cards
     */
    private const SUITS = ['Hearts', 'Diamonds', 'Clubs', 'Spades'];

    /**
     * @var array Possible values for cards
     */
    private const VALUES = [
        '2', '3', '4', '5', '6', '7', '8', '9', '10',
        'Jack', 'Queen', 'King', 'Ace'
    ];

    /**
     * Constructor that creates a standard deck of 52 cards
     */
    public function __construct()
    {
        $this->initializeDeck();
    }

    /**
     * Initialize a standard deck of 52 cards
     *
     * @return void
     */
    protected function initializeDeck(): void
    {
        $this->cards = [];
        foreach (self::SUITS as $suit) {
            foreach (self::VALUES as $value) {
                $this->cards[] = new CardGraphic($suit, $value);
            }
        }
    }

    /**
     * Get all cards in the deck
     *
     * @return array<CardGraphic> Array of Card objects
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * Shuffle the cards in the deck
     *
     * @return void
     */
    public function shuffle(): void
    {
        shuffle($this->cards);
    }

    /**
     * Draw a specified number of cards from the deck
     *
     * @param int $number Number of cards to draw
     *
     * @return array<CardGraphic> Array of drawn Card objects
     */
    public function draw(int $number = 1): array
    {
        $drawn = [];

        for ($i = 0; $i < $number && !empty($this->cards); $i++) {
            $drawn[] = array_pop($this->cards);
        }

        return $drawn;
    }

    /**
     * Get the count of cards remaining in the deck
     *
     * @return int Number of cards remaining
     */
    public function getCount(): int
    {
        return count($this->cards);
    }

    /**
     * Sort the deck back into order
     *
     * @return void
     */
    public function sort(): void
    {
        $this->initializeDeck();
    }

    /**
     * Sort the deck back into order
     *
     * @return void
     */
    public function sortDeck(): void
    {
        $this->sort();
    }

    /**
     * Deal N cards to M players, returns array<CardHand>
     */
    public function deal(int $players, int $cardsPerPlayer): array
    {
        $hands = [];
        for ($i = 0; $i < $players; $i++) {
            $hand = new CardHand();
            $drawn = $this->draw($cardsPerPlayer);
            foreach ($drawn as $card) {
                $hand->addCard($card);
            }
            $hands[] = $hand;
        }
        return $hands;
    }

    public function getAsJson(): array
    {
        $out = [];
        foreach ($this->cards as $card) {
            $out[] = [
                'suit' => $card->getSuit(),
                'value' => $card->getValue(),
                'symbol' => $card->getSuitSymbol(),
                'representation' => $card->getAsString(),
            ];
        }
        return $out;
    }
}
