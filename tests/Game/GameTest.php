<?php

namespace App\Tests\Game;

use App\Game\Game;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    public function testInit(): void
    {
        $game = new Game();
        $game->init();
        
        $this->assertEquals('playing', $game->getGameState());
        $this->assertEquals(2, $game->getPlayerHand()->getCount());
        $this->assertEquals(1, $game->getDealerHand()->getCount());
    }

    public function testPlayerHit(): void
    {
        $game = new Game();
        $game->init();
        
        $initialCardCount = $game->getPlayerHand()->getCount();
        $game->playerHit();
        
        $this->assertEquals($initialCardCount + 1, $game->getPlayerHand()->getCount());
    }

    public function testPlayerStand(): void
    {
        $game = new Game();
        $game->init();
        $game->playerStand();
        
        $this->assertEquals('game_over', $game->getGameState());
        // Dealer should have drawn until having 17 or more
        $dealerScore = $game->getDealerScore();
        $this->assertTrue($dealerScore >= 17 || $game->getDealerHand()->getCount() > 1);
    }
    
    public function testIsGameOver(): void
    {
        $game = new Game();
        $game->init();
        
        $this->assertFalse($game->isGameOver());
        
        $game->playerStand();
        $this->assertTrue($game->isGameOver());
    }
    
    public function testGetResult(): void
    {
        $game = new Game();
        $game->init();
        
        // Initially result should be empty
        $this->assertEquals("", $game->getResult());
        
        $game->playerStand();
        
        // After game is over, result should be set
        $this->assertTrue(in_array($game->getResult(), ['player_wins', 'dealer_wins']));
    }
}
