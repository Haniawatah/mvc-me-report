<?php

namespace App\Card;

/**
 * Graphic playing card class
 *
 * Extends the Card class to provide graphical representation
 */
class CardGraphic extends Card
{
    /**
     * @var array Map of suits to their symbols
     */
    private static array $suitSymbols = [
        'Hearts' => '♥',
        'Diamonds' => '♦',
        'Clubs' => '♣',
        'Spades' => '♠',
        'Joker' => '🃏',
    ];

    /**
     * @var array Map of suits to their CSS classes
     */
    private static array $suitClasses = [
        'Hearts' => 'red',
        'Diamonds' => 'red',
        'Clubs' => 'black',
        'Spades' => 'black'
    ];

    /**
     * Get the symbol for this card's suit
     *
     * @return string The symbol for the suit
     */
    public function getSymbol(): string
    {
        return $this->getSuitSymbol();
    }

    /**
     * Get an HTML representation of the card
     *
     * @return string An HTML representation of the card with suit symbol
     */
    public function getAsHTML(): string
    {
        $symbol = $this->getSuitSymbol();
        $displayValue = $this->value;
        $color = ($this->suit === 'Hearts' || $this->suit === 'Diamonds') ? '#D40000' : '#000000';

        // Joker rendering: uppercase text and joker symbol
        if ($this->suit === 'Joker' || $this->value === 'Joker') {
            return "<div class=\"card\" style=\"color:{$color}\">
                        <div class=\"card-inner\">
                            <div class=\"card-value\">JOKER</div>
                            <div class=\"card-suit\">{$symbol}</div>
                        </div>
                    </div>";
        }

        return "<div class=\"card\" style=\"color:{$color}\">
                    <div class=\"card-inner\">
                        <div class=\"card-value\">{$displayValue}</div>
                        <div class=\"card-suit\">{$symbol}</div>
                    </div>
                </div>";
    }

    public function __construct(string $suit, string $value, ?int $numericValue = null)
    {
        parent::__construct($suit, $value, $numericValue);
    }

    public function getSuitSymbol(): string
    {
        // For unknown suits, return the suit name itself (as tests expect)
        return self::$suitSymbols[$this->suit] ?? $this->suit;
    }
}
