<?php

namespace App\Tests\Card;

use App\Card\DeckOfCardsWithJokers;
use PHPUnit\Framework\TestCase;

class DeckOfCardsWithJokersTest extends TestCase
{
    /**
     * Test that deck is created with 54 cards (includes 2 jokers).
     */
    public function testCreateDeckWithJokers(): void
    {
        $deck = new DeckOfCardsWithJokers();
        $this->assertCount(54, $deck->getCards());
    }

    /**
     * Test that shuffle changes the order of cards.
     */
    public function testShuffleDeckWithJokers(): void
    {
        $deck = new DeckOfCardsWithJokers();
        $originalCards = $deck->getCards();
        $originalOrder = array_map(function ($card) {
            return $card->getAsString();
        }, $originalCards);

        $deck->shuffle();
        $shuffledCards = $deck->getCards();
        $shuffledOrder = array_map(function ($card) {
            return $card->getAsString();
        }, $shuffledCards);

        $this->assertNotSame($originalOrder, $shuffledOrder);
    }

    /**
     * Test drawing a card reduces the deck size.
     */
    public function testDrawCardWithJokers(): void
    {
        $deck = new DeckOfCardsWithJokers();
        $initialCount = $deck->getCount();
        $drawn = $deck->draw();

        $this->assertCount(1, $drawn);
        $this->assertEquals($initialCount - 1, $deck->getCount());
    }

    /**
     * Test drawing multiple cards returns correct number of cards
     * and reduces the deck size accordingly.
     */
    public function testDrawMultipleCardsWithJokers(): void
    {
        $deck = new DeckOfCardsWithJokers();
        $initialCount = $deck->getCount();
        $numberToDraw = 5;
        $drawn = $deck->draw($numberToDraw);

        $this->assertCount($numberToDraw, $drawn);
        $this->assertEquals($initialCount - $numberToDraw, $deck->getCount());
    }

    /**
     * Test the deck includes jokers.
     */
    public function testDeckContainsJokers(): void
    {
        $deck = new DeckOfCardsWithJokers();
        $cards = $deck->getCards();
        $jokerCount = 0;

        foreach ($cards as $card) {
            if ($card instanceof JokerCard) {
                $jokerCount++;
            }
        }

        $this->assertEquals(2, $jokerCount, 'The deck should contain exactly 2 jokers');
    }

    /**
     * Test sort restores original order with jokers at the end.
     */
    public function testSortDeckWithJokers(): void
    {
        $deck = new DeckOfCardsWithJokers();
        $originalCards = $deck->getCards();

        $deck->shuffle();
        $deck->sort();
        $sortedCards = $deck->getCards();

        $this->assertEquals(count($originalCards), count($sortedCards));

        // Last two cards should be jokers
        $lastCard = $sortedCards[count($sortedCards) - 1];
        $secondLastCard = $sortedCards[count($sortedCards) - 2];

        $this->assertInstanceOf(JokerCard::class, $lastCard);
        $this->assertInstanceOf(JokerCard::class, $secondLastCard);
    }
}
