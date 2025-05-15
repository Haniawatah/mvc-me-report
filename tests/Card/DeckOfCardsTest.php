<?php

namespace App\Tests\Card;

use App\Card\DeckOfCards;
use App\Card\CardHand;
use PHPUnit\Framework\TestCase;

class DeckOfCardsTest extends TestCase
{
    public function testConstructor(): void
    {
        $deck = new DeckOfCards();
        $this->assertEquals(52, $deck->getCount());
    }

    public function testShuffle(): void
    {
        $deck = new DeckOfCards();
        $originalCards = $deck->getCards();
        
        // Get string representation of first few cards
        $originalOrder = array_map(function($card) {
            return $card->getAsString();
        }, array_slice($originalCards, 0, 5));
        
        $deck->shuffle();
        
        $shuffledCards = $deck->getCards();
        $shuffledOrder = array_map(function($card) {
            return $card->getAsString();
        }, array_slice($shuffledCards, 0, 5));
        
        // Check that the deck still has 52 cards
        $this->assertEquals(52, $deck->getCount());
        
        // Check if the order has changed (not guaranteed but highly likely)
        $this->assertNotEquals($originalOrder, $shuffledOrder);
    }

    public function testDraw(): void
    {
        $deck = new DeckOfCards();
        $initialCount = $deck->getCount();
        
        // Draw one card
        $drawnCards = $deck->draw(1);
        $this->assertCount(1, $drawnCards);
        $this->assertEquals(51, $deck->getCount());
        
        // Draw multiple cards
        $drawnCards = $deck->draw(5);
        $this->assertCount(5, $drawnCards);
        $this->assertEquals(46, $deck->getCount());
        
        // Draw more cards than available
        $drawnCards = $deck->draw(50);
        $this->assertCount(46, $drawnCards);
        $this->assertEquals(0, $deck->getCount());
        
        // Draw from empty deck
        $drawnCards = $deck->draw(1);
        $this->assertCount(0, $drawnCards);
    }

    public function testSortDeck(): void
    {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $deck->sortDeck();
        
        $cards = $deck->getCards();
        
        // Check if the first card is 2 of Hearts
        $this->assertEquals('Hearts', $cards[0]->getSuit());
        
        // Check if suits are grouped together
        $this->assertEquals('Hearts', $cards[0]->getSuit());
        $this->assertEquals('Hearts', $cards[12]->getSuit());
        $this->assertEquals('Diamonds', $cards[13]->getSuit());
    }

    public function testDeal(): void
    {
        $deck = new DeckOfCards();
        $hands = $deck->deal(2, 5);
        
        $this->assertCount(2, $hands);
        $this->assertInstanceOf(CardHand::class, $hands[0]);
        $this->assertInstanceOf(CardHand::class, $hands[1]);
        
        $this->assertEquals(5, $hands[0]->getCount());
        $this->assertEquals(5, $hands[1]->getCount());
        $this->assertEquals(42, $deck->getCount());
    }

    public function testReset(): void
    {
        $deck = new DeckOfCards();
        $deck->draw(10);
        $this->assertEquals(42, $deck->getCount());
        
        $deck->reset();
        $this->assertEquals(52, $deck->getCount());
    }

    public function testGetAsJson(): void
    {
        $deck = new DeckOfCards();
        $jsonArray = $deck->getAsJson();
        
        $this->assertCount(52, $jsonArray);
        $this->assertArrayHasKey('suit', $jsonArray[0]);
        $this->assertArrayHasKey('value', $jsonArray[0]);
        $this->assertArrayHasKey('symbol', $jsonArray[0]);
        $this->assertArrayHasKey('representation', $jsonArray[0]);
    }
}
