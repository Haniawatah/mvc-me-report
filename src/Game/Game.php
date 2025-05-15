<?php

namespace App\Game;

use App\Card\CardHand;
use App\Card\DeckOfCards;

/**
 * Game class for the Game 21 (Blackjack) implementation
 * 
 * This class handles all game logic for the Game 21 card game
 * including dealing cards, managing player and dealer actions,
 * calculating scores, and determining the game winner.
 */
class Game
{
    /**
     * @var DeckOfCards The deck of cards used in the game
     */
    private DeckOfCards $deck;

    /**
     * @var CardHand The player's hand of cards
     */
    private CardHand $playerHand;

    /**
     * @var CardHand The dealer's hand of cards
     */
    private CardHand $dealerHand;

    /**
     * @var string Current state of the game ("waiting", "playing", "dealer_playing", or "game_over")
     */
    private string $gameState = "waiting";

    /**
     * @var string Result of the game ("player_wins", "dealer_wins", or empty string if game is ongoing)
     */
    private string $result = "";

    /**
     * Initialize the game
     * 
     * Creates a new shuffled deck, deals initial cards (2 to player, 1 to dealer),
     * and sets the game state to "playing". Also checks if the player has 21 from the start.
     *
     * @return void
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
     * Player takes a card (hit)
     * 
     * Draws a card from the deck and adds it to the player's hand.
     * Checks if player busts (score > 21) or gets exactly 21.
     *
     * @return void
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
     * 
     * Dealer draws cards until reaching a score of 17 or higher.
     * Then determines the winner based on the final scores.
     *
     * @return void
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
     * 
     * Returns the current player's hand object
     *
     * @return CardHand The player's hand of cards
     */
    public function getPlayerHand(): CardHand
    {
        return $this->playerHand;
    }

    /**
     * Get dealer's hand
     * 
     * Returns the current dealer's hand object
     *
     * @return CardHand The dealer's hand of cards
     */
    public function getDealerHand(): CardHand
    {
        return $this->dealerHand;
    }

    /**
     * Calculate player score
     * 
     * Returns the current score of the player's hand
     *
     * @return int The total score of the player's hand
     */
    public function getPlayerScore(): int
    {
        return $this->calculateHandScore($this->playerHand);
    }

    /**
     * Calculate dealer score
     * 
     * Returns the current score of the dealer's hand
     *
     * @return int The total score of the dealer's hand
     */
    public function getDealerScore(): int
    {
        return $this->calculateHandScore($this->dealerHand);
    }

    /**
     * Calculate the score of a hand
     * 
     * Adds up the values of all cards in the hand.
     * Number cards are worth their face value, face cards are worth 10,
     * and Aces can be worth either 1 or 14 (calculated automatically to
     * maximize the hand score without exceeding 21).
     *
     * @param CardHand $hand The hand to calculate the score for
     * @return int The total score of the hand
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
     * 
     * Possible states: "waiting", "playing", "dealer_playing", "game_over"
     *
     * @return string The current game state
     */
    public function getGameState(): string
    {
        return $this->gameState;
    }

    /**
     * Get the result of the game
     * 
     * Returns the result of the game: "player_wins", "dealer_wins",
     * or an empty string if the game is still ongoing
     *
     * @return string The result of the game
     */
    public function getResult(): string
    {
        return $this->result;
    }

    /**
     * Check if game is over
     * 
     * Returns true if the game state is "game_over"
     *
     * @return bool True if the game is over, false otherwise
     */
    public function isGameOver(): bool
    {
        return $this->gameState === "game_over";
    }
}
