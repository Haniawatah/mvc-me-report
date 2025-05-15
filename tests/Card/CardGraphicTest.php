<?php

namespace App\Tests\Card;

use App\Card\CardGraphic;
use PHPUnit\Framework\TestCase;

class CardGraphicTest extends TestCase
{
    /**
     * Test inheritance from Card class
     */
    public function testInheritance(): void
    {
        $card = new CardGraphic('Hearts', '10', 10);
        $this->assertEquals('Hearts', $card->getSuit());
        $this->assertEquals('10', $card->getValue());
        $this->assertEquals(10, $card->getNumericValue());
        $this->assertEquals('[10 of Hearts]', $card->getAsString());
    }

    /**
     * Test getSuitSymbol for regular suits
     */
    public function testGetSuitSymbol(): void
    {
        $hearts = new CardGraphic('Hearts', '10', 10);
        $this->assertEquals('♥', $hearts->getSuitSymbol());
        
        $diamonds = new CardGraphic('Diamonds', '5', 5);
        $this->assertEquals('♦', $diamonds->getSuitSymbol());
        
        $clubs = new CardGraphic('Clubs', 'Jack', 11);
        $this->assertEquals('♣', $clubs->getSuitSymbol());
        
        $spades = new CardGraphic('Spades', 'Queen', 12);
        $this->assertEquals('♠', $spades->getSuitSymbol());
    }
    
    /**
     * Test getSuitSymbol for joker
     */
    public function testGetSuitSymbolJoker(): void
    {
        $joker = new CardGraphic('Joker', 'Joker', 15);
        $this->assertEquals('🃏', $joker->getSuitSymbol());
    }
    
    /**
     * Test getSuitSymbol for unknown suit
     */
    public function testGetSuitSymbolUnknown(): void
    {
        $unknown = new CardGraphic('Wild', 'Wild', 20);
        $this->assertEquals('Wild', $unknown->getSuitSymbol());
    }

    /**
     * Test getAsHtml for regular card
     */
    public function testGetAsHtmlRegular(): void
    {
        $card = new CardGraphic('Hearts', '10', 10);
        $html = $card->getAsHtml();
        
        // Check that HTML contains key parts
        $this->assertStringContainsString('♥', $html);
        $this->assertStringContainsString('10', $html);
        $this->assertStringContainsString('#D40000', $html); // Red color
    }
    
    /**
     * Test getAsHtml for black suit (Clubs)
     */
    public function testGetAsHtmlBlackSuit(): void
    {
        $card = new CardGraphic('Clubs', 'Ace', 14);
        $html = $card->getAsHtml();
        
        $this->assertStringContainsString('♣', $html);
        $this->assertStringContainsString('Ace', $html);
        $this->assertStringContainsString('#000000', $html); // Black color
    }
    
    /**
     * Test getAsHtml for Joker
     */
    public function testGetAsHtmlJoker(): void
    {
        $joker = new CardGraphic('Joker', 'Joker', 15);
        $html = $joker->getAsHtml();
        
        $this->assertStringContainsString('🃏', $html);
        $this->assertStringContainsString('JOKER', $html);
    }
    
    /**
     * Test getAsString for Joker
     */
    public function testGetAsStringJoker(): void
    {
        $joker = new CardGraphic('Joker', 'Joker', 15);
        $this->assertEquals('[Joker]', $joker->getAsString());
    }
}
