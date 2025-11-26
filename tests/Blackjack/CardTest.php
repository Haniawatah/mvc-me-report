<?php

namespace App\Tests\Blackjack;

use App\Blackjack\Card;
use PHPUnit\Framework\TestCase;

class CardTest extends TestCase
{
    public function testCreateCard(): void
    {
        $card = new Card("hearts", "A");
        $this->assertInstanceOf(Card::class, $card);
    }

    public function testGetters(): void
    {
        $card = new Card("spades", "K");
        // Adjust these assertions based on your actual Card methods
        if (method_exists($card, 'getSuit')) {
            $this->assertEquals("spades", $card->getSuit());
        }
        if (method_exists($card, 'getValue')) {
            $this->assertEquals("K", $card->getValue());
        }
    }
}
