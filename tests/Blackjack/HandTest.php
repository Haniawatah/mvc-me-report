<?php

namespace App\Tests\Blackjack;

use App\Blackjack\Hand;
use App\Blackjack\Card;
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
        $card = $this->createMock(Card::class);
        $hand->add($card);
        
        // Assuming getCards returns an array
        $this->assertCount(1, $hand->getCards());
    }

    public function testScore(): void
    {
        $hand = new Hand();
        // Add logic here to test score calculation if implemented in Hand
        $this->assertIsInt($hand->getScore());
    }
}
