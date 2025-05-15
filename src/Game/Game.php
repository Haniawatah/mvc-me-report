<?php

namespace App\Game;

use App\Card\CardHand;
use App\Card\DeckOfCards;

class Game
{
    private DeckOfCards $deck;
    private CardHand $playerHand;
    private CardHand $dealerHand;
    private string $gameState = "waiting";
    private string $result = "";

    /**
     * Initialize the game
     */
    public function init(): void
    {
        $this->deck = new DeckOfCards();
        $this->deck->shuffle();
        
        $this->playerHand = new CardHand();
        $this->dealerHand = new CardHand();
        
        // Initial deal: 2 cards for player, 1 for dealer
        $drawnCards = $this->deck->draw(2);
        foreach ($drawnCards as $card) {
            $this->playerHand->addCard($card);
        }
        
        $drawnCards = $this->deck->draw(1);
        $this->dealerHand->addCard($drawnCards[0]);
        
        $this->gameState = "playing";
        
        // Check if player has 21 from the start
        if ($this->getPlayerScore() === 21) {
            $this->playerStand();
        }
    }

    /**
     * Player takes a card
     */
    public function playerHit(): void
    {
        if ($this->gameState !== "playing") {
            return;
        }

        $drawnCards = $this->deck->draw(1);
        $this->playerHand->addCard($drawnCards[0]);
        
        $score = $this->getPlayerScore();
        
        if ($score > 21) {
            $this->gameState = "game_over";
            $this->result = "dealer_wins";
        } elseif ($score === 21) {
            $this->playerStand();
        }
    }

    /**
     * Player stands, dealer plays
     */
    public function playerStand(): void
    {
        if ($this->gameState !== "playing") {
            return;
        }

        $this->gameState = "dealer_playing";
        
        // Dealer plays until 17 or more
        while ($this->getDealerScore() < 17) {
            $drawnCards = $this->deck->draw(1);
            $this->dealerHand->addCard($drawnCards[0]);
        }
        
        $playerScore = $this->getPlayerScore();
        $dealerScore = $this->getDealerScore();
        
        $this->gameState = "game_over";
        
        // Determine winner
        if ($dealerScore > 21) {
            $this->result = "player_wins";
        } elseif ($dealerScore > $playerScore) {
            $this->result = "dealer_wins";
        } elseif ($dealerScore < $playerScore) {
            $this->result = "player_wins";
        } else {
            $this->result = "dealer_wins"; // Tie goes to dealer
        }
    }

    /**
     * Get player's hand
     */
    public function getPlayerHand(): CardHand
    {
        return $this->playerHand;
    }

    /**
     * Get dealer's hand
     */
    public function getDealerHand(): CardHand
    {
        return $this->dealerHand;
    }

    /**
     * Calculate player score
     */
    public function getPlayerScore(): int
    {
        return $this->calculateHandScore($this->playerHand);
    }

    /**
     * Calculate dealer score
     */
    public function getDealerScore(): int
    {
        return $this->calculateHandScore($this->dealerHand);
    }

    /**
     * Calculate the score of a hand
     */
    private function calculateHandScore(CardHand $hand): int
    {
        $cards = $hand->getCards();
        $score = 0;
        $aces = 0;
        
        foreach ($cards as $card) {
            if ($card->getValue() === 'Ace') {
                $aces++;
                $score += 1; // Count aces as 1 initially
            } elseif (in_array($card->getValue(), ['Jack', 'Queen', 'King'])) {
                $score += 10;
            } else {
                $score += intval($card->getValue());
            }
        }
        
        // Optionally count aces as 14 if it would be beneficial
        for ($i = 0; $i < $aces; $i++) {
            if ($score + 13 <= 21) { // 13 more because we already counted ace as 1
                $score += 13;
            }
        }
        
        return $score;
    }

    /**
     * Get the current game state
     */
    public function getGameState(): string
    {
        return $this->gameState;
    }

    /**
     * Get the result of the game
     */
    public function getResult(): string
    {
        return $this->result;
    }

    /**
     * Check if game is over
     */
    public function isGameOver(): bool
    {
        return $this->gameState === "game_over";
    }
}
