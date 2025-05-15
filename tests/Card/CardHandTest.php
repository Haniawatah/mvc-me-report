<?php

namespace App\Tests\Card;

use App\Card\CardHand;
use App\Card\CardGraphic;
use PHPUnit\Framework\TestCase;

class CardHandTest extends TestCase
{
    /**
     * Test creating an empty hand
     */
    public function testCreateEmptyHand(): void
    {
        $hand = new CardHand();
        $this->assertEmpty($hand->getCards());
        $this->assertEquals(0, $hand->getCount());
    }

    /**
     * Test adding and getting cards
     */
    public function testAddAndGetCards(): void
    {
        $hand = new CardHand();
        $card1 = new CardGraphic('Hearts', '10', 10);
        $card2 = new CardGraphic('Spades', 'Ace', 14);
        
        $hand->addCard($card1);
        $this->assertEquals(1, $hand->getCount());
        $this->assertCount(1, $hand->getCards());
        $this->assertSame($card1, $hand->getCards()[0]);
        
        $hand->addCard($card2);
        $this->assertEquals(2, $hand->getCount());
        $this->assertCount(2, $hand->getCards());
        $this->assertSame($card1, $hand->getCards()[0]);
        $this->assertSame($card2, $hand->getCards()[1]);
    }

    /**
     * Test getAsHtml method
     */
    public function testGetAsHtml(): void
    {
        $hand = new CardHand();
        $card = new CardGraphic('Hearts', '10', 10);
        $hand->addCard($card);
        
        $html = $hand->getAsHtml();
        $this->assertStringContainsString('<div class=\'card-hand\'>', $html);
        $this->assertStringContainsString('♥', $html);
        $this->assertStringContainsString('10', $html);
    }

    /**
     * Test getAsHtml with multiple cards
     */
    public function testGetAsHtmlMultipleCards(): void
    {
        $hand = new CardHand();
        $card1 = new CardGraphic('Hearts', '10', 10);
        $card2 = new CardGraphic('Spades', 'Ace', 14);
        $hand->addCard($card1);
        $hand->addCard($card2);
        
        $html = $hand->getAsHtml();
        $this->assertStringContainsString('♥', $html);
        $this->assertStringContainsString('10', $html);
        $this->assertStringContainsString('♠', $html);
        $this->assertStringContainsString('Ace', $html);
    }
    
    /**
     * Test getAsHtml with empty hand
     */
    public function testGetAsHtmlEmpty(): void
    {
        $hand = new CardHand();
        $html = $hand->getAsHtml();
        $this->assertEquals("<div class='card-hand'></div>", $html);
    }
    
    /**
     * Test getAsJson method
     */
    public function testGetAsJson(): void
    {
        $hand = new CardHand();
        $card = new CardGraphic('Hearts', '10', 10);
        $hand->addCard($card);
        
        $json = $hand->getAsJson();
        $this->assertIsArray($json);
        $this->assertCount(1, $json);
        $this->assertEquals('Hearts', $json[0]['suit']);
        $this->assertEquals('10', $json[0]['value']);
        $this->assertEquals('♥', $json[0]['symbol']);
        $this->assertEquals('[10 of Hearts]', $json[0]['representation']);
    }
    
    /**
     * Test getAsJson with multiple cards
     */
    public function testGetAsJsonMultipleCards(): void
    {
        $hand = new CardHand();
        $card1 = new CardGraphic('Hearts', '10', 10);
        $card2 = new CardGraphic('Spades', 'Ace', 14);
        $hand->addCard($card1);
        $hand->addCard($card2);
        
        $json = $hand->getAsJson();
        $this->assertIsArray($json);
        $this->assertCount(2, $json);
        
        $this->assertEquals('Hearts', $json[0]['suit']);
        $this->assertEquals('10', $json[0]['value']);
        
        $this->assertEquals('Spades', $json[1]['suit']);
        $this->assertEquals('Ace', $json[1]['value']);
    }
    
    /**
     * Test getAsJson with empty hand
     */
    public function testGetAsJsonEmpty(): void
    {
        $hand = new CardHand();
        $json = $hand->getAsJson();
        $this->assertIsArray($json);
        $this->assertEmpty($json);
    }
}
