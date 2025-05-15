<?php

namespace App\Tests\Card;

use App\Card\CardGraphic;
use PHPUnit\Framework\TestCase;

class CardGraphicTest extends TestCase
{
    public function testGetSuitSymbol(): void
    {
        $card = new CardGraphic('Hearts', '10', 10);
        $this->assertEquals('♥', $card->getSuitSymbol());
    }

    public function testGetAsHtml(): void
    {
        $card = new CardGraphic('Spades', 'Ace', 14);
        $this->assertStringContainsString('Ace', $card->getAsHtml());
        $this->assertStringContainsString('♠', $card->getAsHtml());
    }
    
    public function testJokerCard(): void
    {
        $card = new CardGraphic('Joker', 'Joker', 15);
        $this->assertEquals('[Joker]', $card->getAsString());
        $this->assertStringContainsString('JOKER', $card->getAsHtml());
    }
}
