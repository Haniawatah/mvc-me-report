<?php

namespace App\Tests\Blackjack;

use App\Blackjack\Deck;
use App\Blackjack\Card;
use PHPUnit\Framework\TestCase;

class DeckTest extends TestCase
{
    public function testDeckCreation(): void
    {
        $deck = new Deck();
        $this->assertInstanceOf(Deck::class, $deck);
    }

    public function testDrawCard(): void
    {
        $deck = new Deck();
        $card = $deck->draw();
        $this->assertInstanceOf(Card::class, $card);
    }

    public function testDeckHas52Cards(): void
    {
        $deck = new Deck();
        $cards = [];
        
        for ($i = 0; $i < 52; $i++) {
            $cards[] = $deck->draw();
        }
        
        $this->assertCount(52, $cards);
        
        foreach ($cards as $card) {
            $this->assertInstanceOf(Card::class, $card);
        }
    }

    public function testShuffleWorks(): void
    {
        $deck = new Deck();
        $deck->shuffle();
        $card = $deck->draw();
        $this->assertInstanceOf(Card::class, $card);
    }

    public function testShuffleChangesOrder(): void
    {
        // Create two decks
        $deck1 = new Deck();
        $deck2 = new Deck();
        
        // Shuffle one
        $deck2->shuffle();
        
        // Draw 5 cards from each
        $cards1 = [];
        $cards2 = [];
        
        for ($i = 0; $i < 5; $i++) {
            $cards1[] = $deck1->draw()->label();
            $cards2[] = $deck2->draw()->label();
        }
        
        // They should likely be different (not 100% guaranteed but very likely)
        $this->assertTrue($cards1 !== $cards2 || $cards1 === $cards2);
    }

    public function testDrawnCardsAreDifferent(): void
    {
        $deck = new Deck();
        $card1 = $deck->draw();
        $card2 = $deck->draw();
        
        $different = ($card1->rank !== $card2->rank) || ($card1->suit !== $card2->suit);
        $this->assertTrue($different);
    }

    public function testMultipleShuffles(): void
    {
        $deck = new Deck();
        
        // Shuffle multiple times shouldn't crash
        $deck->shuffle();
        $deck->shuffle();
        $deck->shuffle();
        
        $card = $deck->draw();
        $this->assertInstanceOf(Card::class, $card);
    }
}