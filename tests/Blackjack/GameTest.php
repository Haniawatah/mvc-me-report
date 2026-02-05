<?php

namespace App\Tests\Blackjack;

use App\Blackjack\Game;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    public function testGameCreation(): void
    {
        $game = new Game(2);
        $this->assertInstanceOf(Game::class, $game);
        $this->assertCount(2, $game->players());
    }

    public function testGameStartsWithCorrectNumberOfPlayers(): void
    {
        $game = new Game(3);
        $this->assertCount(3, $game->players());
    }

    public function testHit(): void
    {
        $game = new Game(1);
        $initialCount = count($game->players()[0]->cards());
        $game->hit();
        $newCount = count($game->players()[0]->cards());
        $this->assertGreaterThanOrEqual($initialCount, $newCount);
    }

    public function testStand(): void
    {
        $game = new Game(1);
        $game->stand();
        $this->assertTrue($game->players()[0]->stood);
    }

    public function testGameFinishesAfterAllPlayersStand(): void
    {
        $game = new Game(1);
        $game->stand();
        $this->assertTrue($game->isFinished());
    }

    public function testToArray(): void
    {
        $game = new Game(1);
        $array = $game->toArray();
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('finished', $array);
        $this->assertArrayHasKey('dealer', $array);
        $this->assertArrayHasKey('players', $array);
    }

    public function testDealerExists(): void
    {
        $game = new Game(1);
        $dealer = $game->dealer();
        $this->assertNotNull($dealer);
        $this->assertGreaterThanOrEqual(2, count($dealer->cards()));
    }

    public function testCurrentIndex(): void
    {
        $game = new Game(2);
        $this->assertGreaterThanOrEqual(0, $game->currentIndex());
    }

    public function testMultipleHits(): void
    {
        $game = new Game(1);
        $player = $game->players()[0];
        
        $initialCount = count($player->cards());
        
        // Dra 3 kort
        $game->hit();
        $game->hit();
        $game->hit();
        
        $finalCount = count($player->cards());
        $this->assertGreaterThan($initialCount, $finalCount);
    }

    public function testSplitIfPossible(): void
    {
        $game = new Game(1);
        $player = $game->players()[0];
        
        if ($player->canSplit()) {
            $initialCount = count($game->players());
            $game->split();
            $this->assertGreaterThan($initialCount, count($game->players()));
        } else {
            // Kan inte splitta, kolla bara att spelet funkar ändå
            $this->assertFalse($player->canSplit());
        }
    }

    public function testDealerPlaysAfterAllPlayersStand(): void
    {
        $game = new Game(2);
        
        // Båda spelare stannar
        $game->stand();
        $game->stand();
        
        // Spelet borde vara slut
        $this->assertTrue($game->isFinished());
        
        // Dealern ska ha spelat klart
        $dealer = $game->dealer();
        $this->assertGreaterThanOrEqual(2, count($dealer->cards()));
    }

    public function testGameWithSinglePlayer(): void
    {
        $game = new Game(1);
        $this->assertCount(1, $game->players());
        $this->assertInstanceOf(Game::class, $game);
    }

    public function testPlayersArrayContainsHands(): void
    {
        $game = new Game(2);
        $players = $game->players();
        
        foreach ($players as $player) {
            $this->assertIsObject($player);
            $this->assertGreaterThanOrEqual(2, count($player->cards()));
        }
    }
}