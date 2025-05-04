<?php

namespace App\Card;

class CardHand
{
    /**
     * @var CardGraphic[] Array of cards in hand
     */
    private array $cards = [];

    /**
     * Add a card to the hand
     */
    public function addCard(CardGraphic $card): void
    {
        $this->cards[] = $card;
    }

    /**
     * Get all cards in the hand
     * @return CardGraphic[]
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * Get number of cards in hand
     */
    public function getCount(): int
    {
        return count($this->cards);
    }

    /**
     * Get hand as HTML
     */
    public function getAsHtml(): string
    {
        $html = "<div class='card-hand'>";
        foreach ($this->cards as $card) {
            $html .= $card->getAsHtml();
        }
        $html .= "</div>";
        
        return $html;
    }

    /**
     * Get hand cards as JSON serializable array
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
}
