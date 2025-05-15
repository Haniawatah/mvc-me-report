<?php

namespace App\Tests\Card;

use App\Card\Card;
use PHPUnit\Framework\TestCase;

class CardTest extends TestCase
{
    /**
     * Test constructor and getters
     */
    public function testCreateCard(): void
    {
        $card = new Card('Hearts', '10', 10);
        $this->assertEquals('Hearts', $card->getSuit());
        $this->assertEquals('10', $card->getValue());
        $this->assertEquals(10, $card->getNumericValue());
    }

    /**
     * Test getAsString method
     */
    public function testGetAsString(): void
    {
        $card = new Card('Spades', 'Ace', 14);
        $this->assertEquals('[Ace of Spades]', $card->getAsString());
    }
    
    /**
     * Test different card values
     */
    public function testDifferentCardValues(): void
    {
        $card1 = new Card('Diamonds', 'Jack', 11);
        $this->assertEquals('Diamonds', $card1->getSuit());
        $this->assertEquals('Jack', $card1->getValue());
        $this->assertEquals(11, $card1->getNumericValue());
        
        $card2 = new Card('Clubs', '2', 2);
        $this->assertEquals('Clubs', $card2->getSuit());
        $this->assertEquals('2', $card2->getValue());
        $this->assertEquals(2, $card2->getNumericValue());
    }
}
