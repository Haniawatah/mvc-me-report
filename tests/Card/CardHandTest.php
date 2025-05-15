<?php

namespace App\Tests\Card;

use App\Card\CardHand;
use App\Card\CardGraphic;
use PHPUnit\Framework\TestCase;

class CardHandTest extends TestCase
{
    public function testAddAndGetCards(): void
    {
        $hand = new CardHand();
        $this->assertCount(0, $hand->getCards());
        
        $card = new CardGraphic('Hearts', '10', 10);
        $hand->addCard($card);
        
        $this->assertCount(1, $hand->getCards());
        $this->assertSame($card, $hand->getCards()[0]);
    }

    public function testGetCount(): void
    {
        $hand = new CardHand();
        $this->assertEquals(0, $hand->getCount());
        
        $hand->addCard(new CardGraphic('Hearts', '10', 10));
        $this->assertEquals(1, $hand->getCount());
        
        $hand->addCard(new CardGraphic('Clubs', 'Ace', 14));
        $this->assertEquals(2, $hand->getCount());
    }

    public function testGetAsHtml(): void
    {
        $hand = new CardHand();
        $hand->addCard(new CardGraphic('Hearts', '10', 10));
        
        $html = $hand->getAsHtml();
        $this->assertStringContainsString('<div class=\'card-hand\'>', $html);
        $this->assertStringContainsString('♥', $html);
    }

    public function testGetAsJson(): void
    {
        $hand = new CardHand();
        $hand->addCard(new CardGraphic('Hearts', '10', 10));
        
        $json = $hand->getAsJson();
        $this->assertIsArray($json);
        $this->assertCount(1, $json);
        $this->assertEquals('Hearts', $json[0]['suit']);
        $this->assertEquals('10', $json[0]['value']);
        $this->assertEquals('♥', $json[0]['symbol']);
    }
}
