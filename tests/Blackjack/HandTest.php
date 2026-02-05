<?php

namespace App\Tests\Blackjack;

use App\Blackjack\Card;
use App\Blackjack\Hand;
use PHPUnit\Framework\TestCase;

class HandTest extends TestCase
{
    public function testCreateHand(): void
    {
        $hand = new Hand();
        $this->assertInstanceOf(Hand::class, $hand);
    }

    public function testAddCard(): void
    {
        $hand = new Hand();
        $card = new Card('K', '♠');
        $hand->add($card);
        
        // FIX: Använd cards() istället för getCards()
        $this->assertCount(1, $hand->cards());
    }

    public function testScore(): void
    {
        $hand = new Hand();
        $hand->add(new Card('K', '♠'));
        $hand->add(new Card('5', '♥'));
        
        // FIX: Använd bestScore() istället för getScore()
        $this->assertEquals(15, $hand->bestScore());
    }

    public function testBestScoreWithAce(): void
    {
        $hand = new Hand();
        $hand->add(new Card('A', '♠'));
        $hand->add(new Card('9', '♥'));
        
        $this->assertEquals(20, $hand->bestScore());
    }

    public function testIsBlackjack(): void
    {
        $hand = new Hand();
        $hand->add(new Card('A', '♠'));
        $hand->add(new Card('K', '♥'));
        
        $this->assertTrue($hand->isBlackjack());
    }

    public function testIsBusted(): void
    {
        $hand = new Hand();
        $hand->add(new Card('K', '♠'));
        $hand->add(new Card('Q', '♥'));
        $hand->add(new Card('5', '♦'));
        
        $this->assertTrue($hand->isBusted());
    }

    public function testCanSplit(): void
    {
        $hand = new Hand();
        $hand->add(new Card('K', '♠'));
        $hand->add(new Card('K', '♥'));
        
        $this->assertTrue($hand->canSplit());
    }

    public function testCannotSplitDifferentCards(): void
    {
        $hand = new Hand();
        $hand->add(new Card('K', '♠'));
        $hand->add(new Card('5', '♥'));
        
        $this->assertFalse($hand->canSplit());
    }
}
