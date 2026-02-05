<?php

namespace App\Tests\Game;

use App\Game\Game;
use App\Card\CardHand;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    // Testa starta nytt spel
    public function testInit(): void
    {
        $game = new Game();
        $game->init();

        $this->assertEquals('playing', $game->getGameState());
        $this->assertEquals(2, $game->getPlayerHand()->getCount());
        $this->assertEquals(1, $game->getDealerHand()->getCount());
        $this->assertEmpty($game->getResult());
    }

    // Spelaren tar kort
    public function testPlayerHit(): void
    {
        $game = new Game();
        $game->init();

        $initialCount = $game->getPlayerHand()->getCount();
        $game->playerHit();

        $this->assertEquals($initialCount + 1, $game->getPlayerHand()->getCount());
    }

    // Spelaren blir tjock
    public function testPlayerBust(): void
    {
        $game = $this->getMockBuilder(Game::class)
            ->onlyMethods(['getPlayerScore'])
            ->getMock();

        $game->method('getPlayerScore')
            ->willReturn(22); // Spelaren har 22 (tjock)

        $game->init();
        $game->playerHit();

        $this->assertEquals('game_over', $game->getGameState());
        $this->assertEquals('dealer_wins', $game->getResult());
    }

    // Spelaren får 21
    public function testPlayerGets21(): void
    {
        $game = $this->getMockBuilder(Game::class)
            ->onlyMethods(['getPlayerScore', 'playerStand'])
            ->getMock();

        $game->method('getPlayerScore')
            ->willReturn(21);

        // Kolla att playerStand anropas när man får 21
        $game->expects($this->once())
            ->method('playerStand');

        $game->init();
        $game->playerHit();
    }

    // Spelaren stannar
    public function testPlayerStand(): void
    {
        $game = new Game();
        $game->init();
        $game->playerStand();

        $this->assertEquals('game_over', $game->getGameState());
        $this->assertNotEmpty($game->getResult());

        // Dealern ska ha minst 1 kort
        $this->assertGreaterThanOrEqual(1, $game->getDealerHand()->getCount());
    }

    // Dealern drar kort tills 17
    public function testDealerDrawsUntil17(): void
    {
        $game = $this->getMockBuilder(Game::class)
            ->onlyMethods(['getDealerScore'])
            ->getMock();

        // Först 10, sen 16, sen 17
        $game->expects($this->exactly(3))
            ->method('getDealerScore')
            ->willReturnOnConsecutiveCalls(10, 16, 17);

        $game->init();
        $initialDealerCards = $game->getDealerHand()->getCount();
        $game->playerStand();

        // Ska ha dragit 2 kort för att nå 17
        $this->assertEquals($initialDealerCards + 2, $game->getDealerHand()->getCount());
    }

    // Dealern blir tjock
    public function testDealerBust(): void
    {
        $game = $this->getMockBuilder(Game::class)
            ->onlyMethods(['getDealerScore', 'getPlayerScore'])
            ->getMock();

        $game->method('getDealerScore')
            ->willReturn(22); // Dealern tjock

        $game->method('getPlayerScore')
            ->willReturn(18);

        $game->init();
        $game->playerStand();

        $this->assertEquals('game_over', $game->getGameState());
        $this->assertEquals('player_wins', $game->getResult());
    }

    // Dealern vinner
    public function testDealerWins(): void
    {
        $game = $this->getMockBuilder(Game::class)
            ->onlyMethods(['getDealerScore', 'getPlayerScore'])
            ->getMock();

        $game->method('getDealerScore')
            ->willReturn(20);

        $game->method('getPlayerScore')
            ->willReturn(18);

        $game->init();
        $game->playerStand();

        $this->assertEquals('game_over', $game->getGameState());
        $this->assertEquals('dealer_wins', $game->getResult());
    }

    // Spelaren vinner
    public function testPlayerWins(): void
    {
        $game = $this->getMockBuilder(Game::class)
            ->onlyMethods(['getDealerScore', 'getPlayerScore'])
            ->getMock();

        $game->method('getDealerScore')
            ->willReturn(17);

        $game->method('getPlayerScore')
            ->willReturn(19);

        $game->init();
        $game->playerStand();

        $this->assertEquals('game_over', $game->getGameState());
        $this->assertEquals('player_wins', $game->getResult());
    }

    // Lika (dealern vinner vid lika)
    public function testTieScenario(): void
    {
        $game = $this->getMockBuilder(Game::class)
            ->onlyMethods(['getDealerScore', 'getPlayerScore'])
            ->getMock();

        $game->method('getDealerScore')
            ->willReturn(19);
        $game->method('getPlayerScore')
            ->willReturn(19);

        $game->init();
        $game->playerStand();

        $this->assertEquals('game_over', $game->getGameState());
        $this->assertEquals('dealer_wins', $game->getResult(), 'Dealern ska vinna vid lika');
    }

    // Kolla spelets status
    public function testGameStateChecks(): void
    {
        $game = new Game();
        $game->init();

        $this->assertFalse($game->isGameOver());

        $game->playerStand();

        $this->assertTrue($game->isGameOver());
    }

    // Ingenting ska hända efter spelet är slut
    public function testActionsAfterGameOver(): void
    {
        $game = new Game();
        $game->init();
        $game->playerStand(); // Avsluta spelet

        // Kolla state efter spelet är slut
        $state = $game->getGameState();
        $playerCards = $game->getPlayerHand()->getCount();
        $dealerCards = $game->getDealerHand()->getCount();

        // Försök göra nåt
        $game->playerHit();
        $game->playerStand();

        // Inget ska ha ändrats
        $this->assertEquals($state, $game->getGameState());
        $this->assertEquals($playerCards, $game->getPlayerHand()->getCount());
        $this->assertEquals($dealerCards, $game->getDealerHand()->getCount());
    }

    // Räkna poäng med klädda kort
    public function testCalculateHandScore(): void
    {
        $game = new Game();
        $game->init();

        // Använd reflection för att testa privat metod
        $reflector = new \ReflectionClass(Game::class);
        $method = $reflector->getMethod('calculateHandScore');
        $method->setAccessible(true);

        $hand = new CardHand();

        // Vanliga kort
        $hand->addCard(new \App\Card\CardGraphic('Hearts', '2', 2));
        $hand->addCard(new \App\Card\CardGraphic('Clubs', '3', 3));
        $this->assertEquals(5, $method->invoke($game, $hand));

        // Lägg till klädda kort
        $hand->addCard(new \App\Card\CardGraphic('Diamonds', 'King', 13));
        $this->assertEquals(15, $method->invoke($game, $hand));

        // Ny hand med ess
        $aceHand = new CardHand();
        $aceHand->addCard(new \App\Card\CardGraphic('Spades', 'Ace', 14));
        $this->assertEquals(14, $method->invoke($game, $aceHand)); // Ess är 14

        // Lägg till ett kort så ess blir 1
        $aceHand->addCard(new \App\Card\CardGraphic('Hearts', '10', 10));
        $this->assertEquals(11, $method->invoke($game, $aceHand)); // Nu är ess 1
    }

    // Spelaren börjar med 21
    public function testPlayerStartsWith21(): void
    {
        $game = $this->getMockBuilder(Game::class)
            ->onlyMethods(['getPlayerScore'])
            ->getMock();

        $game->method('getPlayerScore')
            ->willReturn(21);

        $game->init();

        // Spelaren ska automatiskt stanna vid 21
        $this->assertEquals('game_over', $game->getGameState());
    }
}
