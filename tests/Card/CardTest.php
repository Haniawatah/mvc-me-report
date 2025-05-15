<?php

namespace App\Tests\Card;

use App\Card\Card;
use PHPUnit\Framework\TestCase;

class CardTest extends TestCase
{
    public function testCreateCard(): void
    {
        $card = new Card('Hearts', '10', 10);
        $this->assertEquals('Hearts', $card->getSuit());
        $this->assertEquals('10', $card->getValue());
        $this->assertEquals(10, $card->getNumericValue());
    }

    public function testGetAsString(): void
    {
        $card = new Card('Spades', 'Ace', 14);
        $this->assertEquals('[Ace of Spades]', $card->getAsString());
    }
}
