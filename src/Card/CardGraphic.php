<?php

namespace App\Card;

/**
 * Playing card with graphical representation
 */
class CardGraphic extends Card
{
    /**
     * Constructor for CardGraphic
     *
     * @param string $suit The suit of the card
     * @param string $value The value/rank of the card
     * @param int $numericValue The numeric value of the card
     */
    public function __construct(string $suit, string $value, int $numericValue)
    {
        parent::__construct($suit, $value, $numericValue);
    }
    
    /**
     * Get HTML representation of the card
     *
     * @return string HTML representation of the card
     */
    public function getAsHtml(): string
    {
        $suitSymbols = [
            'Hearts' => '♥',
            'Diamonds' => '♦',
            'Clubs' => '♣',
            'Spades' => '♠',
            'Joker' => '🃏'
        ];
        
        $symbol = $suitSymbols[$this->suit] ?? $this->suit;
        $color = ($this->suit === 'Hearts' || $this->suit === 'Diamonds') ? '#D40000' : '#000000';
        $valueDisplay = $this->value;
        
        // Common card style
        $cardStyle = 'display:inline-block; width:100px; height:140px; background-color:white; border-radius:10px; box-shadow:1px 1px 3px rgba(0,0,0,0.3); margin:5px; position:relative; font-family:Arial,sans-serif; overflow:hidden; border:1px solid #ccc;';
        
        // Special case for Joker
        if ($this->suit === 'Joker') {
            return '
            <div style="' . $cardStyle . ' background-image:linear-gradient(45deg, #f0f0f0, #e5e5e5);">
                <div style="position:absolute; top:5px; left:5px; font-weight:bold; font-size:16px;">J</div>
                <div style="position:absolute; top:22px; left:5px; font-size:16px;">🃏</div>
                <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); text-align:center;">
                    <div style="font-size:40px;">🃏</div>
                    <div style="font-weight:bold; margin-top:5px; color:#333;">JOKER</div>
                </div>
                <div style="position:absolute; bottom:5px; right:5px; font-weight:bold; font-size:16px; transform:rotate(180deg);">J</div>
                <div style="position:absolute; bottom:22px; right:5px; font-size:16px; transform:rotate(180deg);">🃏</div>
            </div>';
        }
        
        // For regular cards
        return '
        <div style="' . $cardStyle . '">
            <div style="position:absolute; top:5px; left:5px; font-weight:bold; color:' . $color . ';">' . $valueDisplay . '</div>
            <div style="position:absolute; top:22px; left:5px; color:' . $color . ';">' . $symbol . '</div>
            <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); font-size:40px; color:' . $color . ';">' . $symbol . '</div>
            <div style="position:absolute; bottom:5px; right:5px; font-weight:bold; color:' . $color . '; transform:rotate(180deg);">' . $valueDisplay . '</div>
            <div style="position:absolute; bottom:22px; right:5px; color:' . $color . '; transform:rotate(180deg);">' . $symbol . '</div>
        </div>';
    }

    /**
     * Get suit symbol
     *
     * @return string The suit symbol
     */
    public function getSuitSymbol(): string
    {
        $suitSymbols = [
            'Hearts' => '♥',
            'Diamonds' => '♦',
            'Clubs' => '♣',
            'Spades' => '♠',
            'Joker' => '🃏'
        ];
        
        return $suitSymbols[$this->suit] ?? $this->suit;
    }

    /**
     * Get the card as a string
     */
    public function getAsString(): string
    {
        if ($this->suit === 'Joker') {
            return "[Joker]";
        }
        return "[{$this->value} of {$this->suit}]";
    }
}
