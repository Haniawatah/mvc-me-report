<?php

namespace App\Tests\Card;

use App\Card\DeckOfCards;
use App\Card\CardHand;
use App\Card\CardGraphic;
use PHPUnit\Framework\TestCase;

class DeckOfCardsTest extends TestCase
{
    /**
     * Test creating a deck with 52 cards
     */
    public function testCreateDeck(): void
    {
        $deck = new DeckOfCards();
        $this->assertEquals(52, $deck->getCount());
        $cards = $deck->getCards();
        $this->assertCount(52, $cards);
        
        // Check first card
        $this->assertInstanceOf(CardGraphic::class, $cards[0]);
        
        // Check that we have 13 cards of each suit
        $suits = ['Hearts', 'Diamonds', 'Clubs', 'Spades'];
        foreach ($suits as $suit) {
            $cardsOfSuit = array_filter($cards, function($card) use ($suit) {
                return $card->getSuit() === $suit;
            });
            $this->assertCount(13, $cardsOfSuit, "Expected 13 cards of suit {$suit}");
        }
    }
    
    /**
     * Test shuffling the deck
     */
    public function testShuffle(): void
    {
        $deck = new DeckOfCards();
        $originalOrder = array_map(function($card) {
            return $card->getAsString();
        }, $deck->getCards());
        
        $deck->shuffle();
        
        $shuffledOrder = array_map(function($card) {
            return $card->getAsString();
        }, $deck->getCards());
        
        // Check that the count remains the same
        $this->assertEquals(52, $deck->getCount());
        
        // Test that the order has changed
        // Note: There's a tiny chance this could fail if shuffle
        // happens to produce the exact same order, but it's extremely unlikely
        $this->assertNotEquals($originalOrder, $shuffledOrder);
    }
    
    /**
     * Test drawing cards
     */
    public function testDraw(): void
    {
        $deck = new DeckOfCards();
        $initialCount = $deck->getCount();
        
        // Draw one card
        $drawnCards = $deck->draw(1);
        $this->assertCount(1, $drawnCards);
        $this->assertInstanceOf(CardGraphic::class, $drawnCards[0]);
        $this->assertEquals($initialCount - 1, $deck->getCount());
        
        // Draw multiple cards
        $drawnCards = $deck->draw(5);
        $this->assertCount(5, $drawnCards);
        $this->assertEquals($initialCount - 6, $deck->getCount());
    }
    
    /**
     * Test drawing more cards than available
     */
    public function testDrawMoreThanAvailable(): void
    {
        $deck = new DeckOfCards();
        $initialCount = $deck->getCount(); // 52
        
        $drawnCards = $deck->draw($initialCount + 10);
        $this->assertCount($initialCount, $drawnCards);
        $this->assertEquals(0, $deck->getCount());
    }
    
    /**
     * Test drawing zero or negative cards
     */
    public function testDrawZeroOrNegative(): void
    {
        $deck = new DeckOfCards();
        $initialCount = $deck->getCount();
        
        $drawnCards = $deck->draw(0);
        $this->assertEmpty($drawnCards);
        $this->assertEquals($initialCount, $deck->getCount());
        
        $drawnCards = $deck->draw(-5);
        $this->assertEmpty($drawnCards);
        $this->assertEquals($initialCount, $deck->getCount());
    }
    
    /**
     * Test drawing from an empty deck
     */
    public function testDrawFromEmptyDeck(): void
    {
        $deck = new DeckOfCards();
        $deck->draw(52); // Draw all cards
        
        $drawnCards = $deck->draw(1);
        $this->assertEmpty($drawnCards);
        $this->assertEquals(0, $deck->getCount());
    }
    
    /**
     * Test sorting the deck
     */
    public function testSortDeck(): void
    {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $deck->sortDeck();
        
        $cards = $deck->getCards();
        
        // All hearts should come first, followed by diamonds, clubs, and spades
        $this->assertEquals('Hearts', $cards[0]->getSuit());
        $this->assertEquals('Hearts', $cards[12]->getSuit());
        $this->assertEquals('Diamonds', $cards[13]->getSuit());
        $this->assertEquals('Diamonds', $cards[25]->getSuit());
        $this->assertEquals('Clubs', $cards[26]->getSuit());
        $this->assertEquals('Clubs', $cards[38]->getSuit());
        $this->assertEquals('Spades', $cards[39]->getSuit());
        $this->assertEquals('Spades', $cards[51]->getSuit());
        
        // Within each suit, cards should be sorted by numeric value
        for ($i = 0; $i < 12; $i++) {
            $this->assertLessThan(
                $cards[$i + 1]->getNumericValue(), 
                $cards[$i]->getNumericValue(),
                "Cards within suit should be sorted by value"
            );
        }
    }
    
    /**
     * Test dealing cards
     */
    public function testDeal(): void
    {
        $deck = new DeckOfCards();
        $initialCount = $deck->getCount();
        
        $hands = $deck->deal(3, 5);
        
        // Check that we have 3 hands
        $this->assertCount(3, $hands);
        
        // Check that each hand is a CardHand with 5 cards
        foreach ($hands as $hand) {
            $this->assertInstanceOf(CardHand::class, $hand);
            $this->assertEquals(5, $hand->getCount());
        }
        
        // Check that the right number of cards were removed from deck
        $this->assertEquals($initialCount - (3 * 5), $deck->getCount());
    }
    
    /**
     * Test dealing more cards than available
     */
    public function testDealMoreThanAvailable(): void
    {
        $deck = new DeckOfCards();
        $deck->draw(45); // Draw 45 cards, leaving 7
        
        $hands = $deck->deal(3, 5); // Try to deal 15 cards total
        
        // Check that we have 3 hands
        $this->assertCount(3, $hands);
        
        // First hand should have 3 cards, second should have 2, third should have 2
        $this->assertEquals(3, $hands[0]->getCount());
        $this->assertEquals(2, $hands[1]->getCount());
        $this->assertEquals(2, $hands[2]->getCount());
        
        // Deck should be empty
        $this->assertEquals(0, $deck->getCount());
    }
    
    /**
     * Test resetting the deck
     */
    public function testReset(): void
    {
        $deck = new DeckOfCards();
        $deck->draw(30);
        $this->assertEquals(22, $deck->getCount());
        
        $deck->reset();
        $this->assertEquals(52, $deck->getCount());
    }
    
    /**
     * Test getAsJson method
     */
    public function testGetAsJson(): void
    {
        $deck = new DeckOfCards();
        $json = $deck->getAsJson();
        
        $this->assertIsArray($json);
        $this->assertCount(52, $json);
        
        // Check structure of first card
        $this->assertArrayHasKey('suit', $json[0]);
        $this->assertArrayHasKey('value', $json[0]);
        $this->assertArrayHasKey('symbol', $json[0]);
        $this->assertArrayHasKey('representation', $json[0]);
    }
}
