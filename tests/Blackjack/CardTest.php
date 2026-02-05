<?php

namespace App\Tests\Blackjack;

use App\Blackjack\Card;
use PHPUnit\Framework\TestCase;

class CardTest extends TestCase
{
    public function testCardCreation(): void
    {
        $card = new Card('A', '♠');
        $this->assertEquals('A', $card->rank);
        $this->assertEquals('♠', $card->suit);
    }

    public function testGetters(): void
    {
        $card = new Card('K', '♥');
        // FIX: Lägg till assertions
        $this->assertEquals('K', $card->rank);
        $this->assertEquals('♥', $card->suit);
    }

    public function testIsAce(): void
    {
        $ace = new Card('A', '♥');
        $king = new Card('K', '♦');
        
        $this->assertTrue($ace->isAce());
        $this->assertFalse($king->isAce());
    }

    public function testFaceValue(): void
    {
        $ace = new Card('A', '♠');
        $king = new Card('K', '♥');
        $five = new Card('5', '♦');
        
        $this->assertEquals(11, $ace->faceValue());
        $this->assertEquals(10, $king->faceValue());
        $this->assertEquals(5, $five->faceValue());
    }

    public function testLabel(): void
    {
        $card = new Card('Q', '♣');
        $this->assertEquals('Q♣', $card->label());
    }
}
