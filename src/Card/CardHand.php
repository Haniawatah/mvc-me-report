<?php

namespace App\Card;

/**
 * Card hand class
 *
 * Represents a hand of playing cards
 */
class CardHand
{
    /**
     * @var array<Card> Array of Card objects in this hand
     */
    private array $cards = [];

    /**
     * Add a card to the hand
     *
     * @param Card $card The card to add
     *
     * @return void
     */
    public function addCard(Card $card): void
    {
        $this->cards[] = $card;
    }

    /**
     * Get all cards in the hand
     *
     * @return array<Card> Array of Card objects
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * Get HTML representation of all cards
     *
     * @return string HTML representation of all cards
     */
    public function getAsHtml(): string
    {
        $inner = '';
        foreach ($this->cards as $card) {
            $inner .= $card->getAsHtml();
        }
        return "<div class='card-hand'>{$inner}</div>";
    }

    /**
     * Get a JSON representation of all cards
     *
     * @return array JSON representation of all cards
     */
    public function getAsJson(): array
    {
        $out = [];
        foreach ($this->cards as $card) {
            $out[] = [
                'suit' => $card->getSuit(),
                'value' => $card->getValue(),
                'symbol' => method_exists($card, 'getSuitSymbol') ? $card->getSuitSymbol() : (method_exists($card, 'getSymbol') ? $card->getSymbol() : '?'),
                'representation' => $card->getAsString(),
            ];
        }
        return $out;
    }

    /**
     * Get the number of cards in the hand
     *
     * @return int The number of cards
     */
    public function getCount(): int
    {
        return count($this->cards);
    }

    /**
     * Get a string representation of the hand
     *
     * @return string A string representation of the hand
     */
    public function getString(): string
    {
        $strings = [];

        foreach ($this->cards as $card) {
            $strings[] = $card->getAsString();
        }

        return implode(', ', $strings);
    }
}
