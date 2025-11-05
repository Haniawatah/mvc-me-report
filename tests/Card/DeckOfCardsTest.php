<?php

namespace App\Tests\Card;

use App\Card\DeckOfCards;
use PHPUnit\Framework\TestCase;

class DeckOfCardsTest extends TestCase
{
    /**
     * Test that deck is created with 52 cards.
     */
    public function testCreateDeck(): void
    {
        $deck = new DeckOfCards();
        $this->assertCount(52, $deck->getCards());
    }

    /**
     * Test counting cards in the deck.
     */
    public function testCountDeck(): void
    {
        $deck = new DeckOfCards();
        $this->assertEquals(52, $deck->getCount());

        // Draw one card and test count again
        $deck->draw(1);
        $this->assertEquals(51, $deck->getCount());
    }

    /**
     * Test that shuffle changes the order of cards.
     */
    public function testShuffleDeck(): void
    {
        $deck = new DeckOfCards();
        $originalCards = $deck->getCards();
        $originalOrder = array_map(function ($card) {
            return $card->getAsString();
        }, $originalCards);

        $deck->shuffle();
        $shuffledCards = $deck->getCards();
        $shuffledOrder = array_map(function ($card) {
            return $card->getAsString();
        }, $shuffledCards);

        // The shuffled order should be different from the original order
        // Note: There's a very small chance this test could fail if the
        // shuffle happens to result in the same order
        $this->assertNotSame($originalOrder, $shuffledOrder);
    }

    /**
     * Test drawing a card reduces the deck size.
     */
    public function testDrawCard(): void
    {
        $deck = new DeckOfCards();
        $initialCount = $deck->getCount();
        $drawn = $deck->draw();

        $this->assertCount(1, $drawn);
        $this->assertEquals($initialCount - 1, $deck->getCount());
    }

    /**
     * Test drawing multiple cards returns correct number of cards
     * and reduces the deck size accordingly.
     */
    public function testDrawMultipleCards(): void
    {
        $deck = new DeckOfCards();
        $initialCount = $deck->getCount();
        $numberToDraw = 5;
        $drawn = $deck->draw($numberToDraw);

        $this->assertCount($numberToDraw, $drawn);
        $this->assertEquals($initialCount - $numberToDraw, $deck->getCount());
    }
}
