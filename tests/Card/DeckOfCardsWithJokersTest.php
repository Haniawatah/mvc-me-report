<?php

namespace App\Tests\Card;

use App\Card\DeckOfCardsWithJokers;
use PHPUnit\Framework\TestCase;

class DeckOfCardsWithJokersTest extends TestCase
{
    public function testConstructorWithJokers(): void
    {
        $deck = new DeckOfCardsWithJokers();
        $this->assertEquals(54, $deck->getCount());
        
        // Check for jokers in the deck
        $cards = $deck->getCards();
        $jokerCount = 0;
        
        foreach ($cards as $card) {
            if ($card->getSuit() === 'Joker') {
                $jokerCount++;
            }
        }
        
        $this->assertEquals(2, $jokerCount);
    }
}
