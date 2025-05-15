<?php

namespace App\Tests\Card;

use App\Card\DeckOfCardsWithJokers;
use PHPUnit\Framework\TestCase;

class DeckOfCardsWithJokersTest extends TestCase
{
    /**
     * Test that deck has 54 cards (52 + 2 jokers)
     */
    public function testCreateDeckWithJokers(): void
    {
        $deck = new DeckOfCardsWithJokers();
        $this->assertEquals(54, $deck->getCount());
        
        // Check for jokers
        $cards = $deck->getCards();
        $jokers = array_filter($cards, function($card) {
            return $card->getSuit() === 'Joker';
        });
        
        $this->assertCount(2, $jokers, "Expected 2 jokers in the deck");
    }
    
    /**
     * Test shuffle with jokers
     */
    public function testShuffleWithJokers(): void
    {
        $deck = new DeckOfCardsWithJokers();
        $originalOrder = array_map(function($card) {
            return $card->getAsString();
        }, $deck->getCards());
        
        $deck->shuffle();
        
        $shuffledOrder = array_map(function($card) {
            return $card->getAsString();
        }, $deck->getCards());
        
        // Check that the count remains the same
        $this->assertEquals(54, $deck->getCount());
        
        // Test that the order has changed
        $this->assertNotEquals($originalOrder, $shuffledOrder);
    }
    
    /**
     * Test reset with jokers
     */
    public function testResetWithJokers(): void
    {
        $deck = new DeckOfCardsWithJokers();
        $deck->draw(30);
        $this->assertEquals(24, $deck->getCount());
        
        $deck->reset();
        $this->assertEquals(54, $deck->getCount());
        
        // Check for jokers after reset
        $cards = $deck->getCards();
        $jokers = array_filter($cards, function($card) {
            return $card->getSuit() === 'Joker';
        });
        
        $this->assertCount(2, $jokers, "Expected 2 jokers after reset");
    }
    
    /**
     * Test that jokers are included in JSON representation
     */
    public function testGetAsJsonWithJokers(): void
    {
        $deck = new DeckOfCardsWithJokers();
        $json = $deck->getAsJson();
        
        $this->assertIsArray($json);
        $this->assertCount(54, $json);
        
        // Find jokers in JSON array
        $jokers = array_filter($json, function($card) {
            return $card['suit'] === 'Joker';
        });
        
        $this->assertCount(2, $jokers, "Expected 2 jokers in JSON output");
    }
}
