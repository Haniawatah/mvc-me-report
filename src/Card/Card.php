<?php

namespace App\Card;

/**
 * Class representing a playing card
 */
class Card
{
    /**
     * @var string The suit of the card (Hearts, Diamonds, Clubs, Spades)
     */
    protected string $suit;

    /**
     * @var string The value of the card (Ace, 2-10, Jack, Queen, King)
     */
    protected string $value;

    /**
     * @var int The numeric value of the card (1-14)
     */
    protected int $numericValue;

    /**
     * Create a new card
     */
    public function __construct(string $suit, string $value, int $numericValue)
    {
        $this->suit = $suit;
        $this->value = $value;
        $this->numericValue = $numericValue;
    }

    /**
     * Get the suit of the card
     */
    public function getSuit(): string
    {
        return $this->suit;
    }

    /**
     * Get the value of the card
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Get the numeric value of the card
     */
    public function getNumericValue(): int
    {
        return $this->numericValue;
    }

    /**
     * Get the card as a string
     */
    public function getAsString(): string
    {
        return "[{$this->value} of {$this->suit}]";
    }
}
