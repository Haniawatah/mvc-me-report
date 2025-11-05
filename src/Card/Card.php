<?php

namespace App\Card;

/**
 * Playing card class
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
     * @var ?int The numeric value of the card (null for jokers)
     */
    private ?int $numericValue = null;

    /**
     * Constructor to create a card with a suit and value
     *
     * @param string $suit The suit of the card
     * @param string $value The value of the card
     * @param ?int $numericValue The numeric value of the card (null for jokers)
     */
    public function __construct(string $suit, string $value, ?int $numericValue = null)
    {
        $this->suit = $suit;
        $this->value = $value;
        $this->numericValue = $numericValue;
    }

    /**
     * Get the suit of the card
     *
     * @return string The suit of the card
     */
    public function getSuit(): string
    {
        return $this->suit;
    }

    /**
     * Get the value of the card
     *
     * @return string The value of the card
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Get a string representation of the card
     *
     * @return string A string representation of the card
     */
    public function getAsString(): string
    {
        // Special-case Joker
        if ($this->suit === 'Joker' || $this->value === 'Joker') {
            return '[Joker]';
        }
        return "[{$this->value} of {$this->suit}]";
    }

    /**
     * Get an HTML representation of the card
     *
     * @return string An HTML representation of the card
     */
    public function getAsHtml(): string
    {
        return "<div class=\"card\"><div class=\"card-inner\"><div class=\"card-value\">{$this->value}</div></div></div>";
    }

    /**
     * Get a symbol representation of the card's suit
     *
     * @return string The symbol of the card's suit
     */
    public function getSymbol(): string
    {
        return '?';
    }

    /**
     * Get the numeric value of the card
     *
     * @return ?int The numeric value of the card (null for jokers)
     */
    public function getNumericValue(): ?int
    {
        return $this->numericValue;
    }
}
