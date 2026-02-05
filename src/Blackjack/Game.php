<?php

declare(strict_types=1);

namespace App\Blackjack;

class Game
{
    private Deck $deck;
    private Hand $dealer;
    /** @var Hand[] */
    private array $players = [];
    private int $current = 0;
    private bool $finished = false;
    /** @var array{win:int,lose:int,push:int}|null[] */
    private array $results = [];
    /** @var array<int, string[]> Track when dealer drew each card */
    private array $dealerDrawLog = [];

    public function __construct(int $hands = 1)
    {
        $hands = max(1, min(3, $hands));
        $this->deck = new Deck();
        $this->dealer = new Hand();
        for ($i = 0; $i < $hands; $i++) {
            $this->players[$i] = new Hand();
        }
        $this->dealInitial();
    }

    private function dealInitial(): void
    {
        // Deal first card to each player
        foreach ($this->players as $p) { $p->add($this->deck->draw()); }
        $card = $this->deck->draw();
        $this->dealer->add($card);
        $this->dealerDrawLog[] = ['Initial deal', $card->label()];
        
        // Deal second card to each player
        foreach ($this->players as $p) { $p->add($this->deck->draw()); }
        $card = $this->deck->draw();
        $this->dealer->add($card);
        $this->dealerDrawLog[] = ['Initial deal (hidden)', $card->label()];
        
        $this->results = array_fill(0, count($this->players), null);
        $this->advanceIfDone();
    }

    private function advanceIfDone(): void
    {
        // Move to next hand that can still play
        while ($this->current < count($this->players)) {
            $h = $this->players[$this->current];
            if ($h->isBusted() || $h->stood || $h->isBlackjack()) {
                $this->current++;
                continue;
            }
            break;
        }
        // All hands done? Finish dealer and score
        if ($this->current >= count($this->players)) {
            $this->finishDealer();
            $this->scoreResults();
            $this->finished = true;
        }
    }

    private function finishDealer(): void
    {
        while ($this->dealer->bestScore() < 17) {
            $card = $this->deck->draw();
            $this->dealer->add($card);
            $this->dealerDrawLog[] = ['Dealer draws to reach 17', $card->label()];
        }
    }

    private function scoreResults(): void
    {
        $dealerScore = $this->dealer->bestScore();
        $dealerBust = $dealerScore > 21;
        foreach ($this->players as $i => $h) {
            if ($h->isBusted()) {
                $this->results[$i] = ['win' => 0, 'lose' => 1, 'push' => 0];
                continue;
            }
            $p = $h->bestScore();
            if ($dealerBust || $p > $dealerScore) {
                $this->results[$i] = ['win' => 1, 'lose' => 0, 'push' => 0];
            } elseif ($p === $dealerScore) {
                $this->results[$i] = ['win' => 0, 'lose' => 0, 'push' => 1];
            } else {
                $this->results[$i] = ['win' => 0, 'lose' => 1, 'push' => 0];
            }
        }
    }

    public function hit(): void
    {
        if ($this->finished) return;
        $this->players[$this->current]->add($this->deck->draw());
        $this->advanceIfDone();
    }

    public function stand(): void
    {
        if ($this->finished) return;
        $this->players[$this->current]->stood = true;
        $this->advanceIfDone();
    }

    public function split(): void
    {
        if ($this->finished) return;
        $h = $this->players[$this->current];
        if (!$h->canSplit()) return;
        $cards = $h->cards();

        $h1 = new Hand(); $h1->add($cards[0]);
        $h2 = new Hand(); $h2->add($cards[1]);

        $this->players[$this->current] = $h1;
        array_splice($this->players, $this->current + 1, 0, [$h2]);

        $this->players[$this->current]->add($this->deck->draw());
        $this->players[$this->current + 1]->add($this->deck->draw());
        $this->results = array_fill(0, count($this->players), null);
        $this->advanceIfDone();
    }

    public function isFinished(): bool { return $this->finished; }
    public function currentIndex(): int { return $this->current; }
    public function dealer(): Hand { return $this->dealer; }
    /** @return Hand[] */ public function players(): array { return $this->players; }
    /** @return array{win:int,lose:int,push:int}|null[] */ public function results(): array { return $this->results; }

    public function toArray(): array
    {
        $dealerCards = array_map(fn(Card $c) => $c->label(), $this->dealer->cards());
        if (!$this->finished && count($dealerCards) >= 2) {
            $dealerCards[1] = '??';
        }
        
        return [
            'finished' => $this->finished,
            'current' => $this->current,
            'dealer' => [
                'cards' => $dealerCards,
                'score' => $this->finished ? $this->dealer->bestScore() : null,
                'drawLog' => $this->finished ? $this->dealerDrawLog : null
            ],
            'players' => array_map(function(Hand $h) {
                return [
                    'cards' => array_map(fn(Card $c) => $c->label(), $h->cards()),
                    'score' => $h->bestScore(),
                    'bust' => $h->isBusted(),
                    'bj' => $h->isBlackjack(),
                    'canSplit' => $h->canSplit(),
                    'stood' => $h->stood,
                ];
            }, $this->players),
            'results' => $this->results,
            'actions' => $this->availableActions(),
        ];
    }

    private function availableActions(): array
    {
        if ($this->finished) return ['new'];
        $h = $this->players[$this->current];
        $acts = ['hit' => true, 'stand' => true];
        if ($h->canSplit()) $acts['split'] = true;
        return array_keys($acts);
    }
}
