<?php

namespace App\Tests\Game;

use App\Game\Game;
use App\Card\CardHand;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    /**
     * Test initializing a new game
     */
    public function testInit(): void
    {
        $game = new Game();
        $game->init();
        
        $this->assertEquals('playing', $game->getGameState());
        $this->assertEquals(2, $game->getPlayerHand()->getCount());
        $this->assertEquals(1, $game->getDealerHand()->getCount());
        $this->assertEmpty($game->getResult());
    }
    
    /**
     * Test player hit action
     */
    public function testPlayerHit(): void
    {
        $game = new Game();
        $game->init();
        
        $initialCount = $game->getPlayerHand()->getCount();
        $game->playerHit();
        
        $this->assertEquals($initialCount + 1, $game->getPlayerHand()->getCount());
    }
    
    /**
     * Test player bust scenario
     */
    public function testPlayerBust(): void
    {
        // Create a custom game class that will force a player bust
        $game = $this->getMockBuilder(Game::class)
            ->onlyMethods(['getPlayerScore'])
            ->getMock();
            
        $game->method('getPlayerScore')
            ->willReturn(22); // Player always has 22 (bust)
            
        $game->init();
        $game->playerHit();
        
        $this->assertEquals('game_over', $game->getGameState());
        $this->assertEquals('dealer_wins', $game->getResult());
    }
    
    /**
     * Test player gets 21
     */
    public function testPlayerGets21(): void
    {
        // Create a game that will simulate player getting exactly 21
        $game = $this->getMockBuilder(Game::class)
            ->onlyMethods(['getPlayerScore', 'playerStand'])
            ->getMock();
            
        $game->method('getPlayerScore')
            ->willReturn(21);
            
        // Verify playerStand is called when score hits 21
        $game->expects($this->once())
            ->method('playerStand');
            
        $game->init();
        $game->playerHit();
    }
    
    /**
     * Test player stand action
     */
    public function testPlayerStand(): void
    {
        $game = new Game();
        $game->init();
        $game->playerStand();
        
        $this->assertEquals('game_over', $game->getGameState());
        $this->assertNotEmpty($game->getResult());
        
        // Dealer should have at least 1 card
        $this->assertGreaterThanOrEqual(1, $game->getDealerHand()->getCount());
    }
    
    /**
     * Test dealer drawing until 17
     */
    public function testDealerDrawsUntil17(): void
    {
        // Create a game where dealer starts with low score but stops at 17
        $game = $this->getMockBuilder(Game::class)
            ->onlyMethods(['getDealerScore'])
            ->getMock();
            
        // First call returns 10, second call returns 16, third returns 17
        $game->expects($this->exactly(3))
            ->method('getDealerScore')
            ->willReturnOnConsecutiveCalls(10, 16, 17);
            
        $game->init();
        $initialDealerCards = $game->getDealerHand()->getCount();
        $game->playerStand();
        
        // Should have drawn 2 more cards to reach 17
        $this->assertEquals($initialDealerCards + 2, $game->getDealerHand()->getCount());
    }
    
    /**
     * Test dealer bust scenario
     */
    public function testDealerBust(): void
    {
        // Create a game where dealer always busts
        $game = $this->getMockBuilder(Game::class)
            ->onlyMethods(['getDealerScore', 'getPlayerScore'])
            ->getMock();
            
        // Dealer always has 22 (bust)
        $game->method('getDealerScore')
            ->willReturn(22);
            
        // Player has a valid score
        $game->method('getPlayerScore')
            ->willReturn(18);
            
        $game->init();
        $game->playerStand();
        
        $this->assertEquals('game_over', $game->getGameState());
        $this->assertEquals('player_wins', $game->getResult());
    }
    
    /**
     * Test dealer wins scenario
     */
    public function testDealerWins(): void
    {
        // Create a game where dealer has higher score
        $game = $this->getMockBuilder(Game::class)
            ->onlyMethods(['getDealerScore', 'getPlayerScore'])
            ->getMock();
            
        // Dealer has 20
        $game->method('getDealerScore')
            ->willReturn(20);
            
        // Player has 18
        $game->method('getPlayerScore')
            ->willReturn(18);
            
        $game->init();
        $game->playerStand();
        
        $this->assertEquals('game_over', $game->getGameState());
        $this->assertEquals('dealer_wins', $game->getResult());
    }
    
    /**
     * Test player wins scenario
     */
    public function testPlayerWins(): void
    {
        // Create a game where player has higher score
        $game = $this->getMockBuilder(Game::class)
            ->onlyMethods(['getDealerScore', 'getPlayerScore'])
            ->getMock();
            
        // Dealer has 17
        $game->method('getDealerScore')
            ->willReturn(17);
            
        // Player has 19
        $game->method('getPlayerScore')
            ->willReturn(19);
            
        $game->init();
        $game->playerStand();
        
        $this->assertEquals('game_over', $game->getGameState());
        $this->assertEquals('player_wins', $game->getResult());
    }
    
    /**
     * Test tie scenario (dealer wins on tie)
     */
    public function testTieScenario(): void
    {
        // Create a game with tied scores
        $game = $this->getMockBuilder(Game::class)
            ->onlyMethods(['getDealerScore', 'getPlayerScore'])
            ->getMock();
            
        // Both have 19
        $game->method('getDealerScore')
            ->willReturn(19);
        $game->method('getPlayerScore')
            ->willReturn(19);
            
        $game->init();
        $game->playerStand();
        
        $this->assertEquals('game_over', $game->getGameState());
        $this->assertEquals('dealer_wins', $game->getResult(), "Dealer should win on tie");
    }
    
    /**
     * Test game state checks
     */
    public function testGameStateChecks(): void
    {
        $game = new Game();
        $game->init();
        
        $this->assertFalse($game->isGameOver());
        
        $game->playerStand();
        
        $this->assertTrue($game->isGameOver());
    }
    
    /**
     * Test that actions do nothing after game over
     */
    public function testActionsAfterGameOver(): void
    {
        $game = new Game();
        $game->init();
        $game->playerStand(); // End the game
        
        // Get state after game is over
        $state = $game->getGameState();
        $playerCards = $game->getPlayerHand()->getCount();
        $dealerCards = $game->getDealerHand()->getCount();
        
        // Try to perform actions
        $game->playerHit();
        $game->playerStand();
        
        // Nothing should have changed
        $this->assertEquals($state, $game->getGameState());
        $this->assertEquals($playerCards, $game->getPlayerHand()->getCount());
        $this->assertEquals($dealerCards, $game->getDealerHand()->getCount());
    }
    
    /**
     * Test calculation of hand score with face cards
     */
    public function testCalculateHandScore(): void
    {
        $game = new Game();
        $game->init();
        
        // We need to use reflection to test private method
        $reflector = new \ReflectionClass(Game::class);
        $method = $reflector->getMethod('calculateHandScore');
        $method->setAccessible(true);
        
        $hand = new CardHand();
        
        // Test with regular number cards
        $hand->addCard(new \App\Card\CardGraphic('Hearts', '2', 2));
        $hand->addCard(new \App\Card\CardGraphic('Clubs', '3', 3));
        $this->assertEquals(5, $method->invoke($game, $hand));
        
        // Add a face card
        $hand->addCard(new \App\Card\CardGraphic('Diamonds', 'King', 13));
        $this->assertEquals(15, $method->invoke($game, $hand));
        
        // Create new hand with Ace
        $aceHand = new CardHand();
        $aceHand->addCard(new \App\Card\CardGraphic('Spades', 'Ace', 14));
        $this->assertEquals(14, $method->invoke($game, $aceHand)); // Ace counts as 14
        
        // Add another card to make Ace worth 1
        $aceHand->addCard(new \App\Card\CardGraphic('Hearts', '10', 10));
        $this->assertEquals(11, $method->invoke($game, $aceHand)); // Ace now counts as 1
    }
    
    /**
     * Test game with player starting with 21
     */
    public function testPlayerStartsWith21(): void
    {
        // Create a game where player starts with 21
        $game = $this->getMockBuilder(Game::class)
            ->onlyMethods(['getPlayerScore'])
            ->getMock();
            
        $game->method('getPlayerScore')
            ->willReturn(21);
            
        $game->init();
        
        // Player should automatically stand with 21
        $this->assertEquals('game_over', $game->getGameState());
    }
}
