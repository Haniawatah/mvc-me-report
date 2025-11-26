<?php

declare(strict_types=1);

namespace App\Blackjack;

class Hand
{
    /** @var Card[] */
    private array $cards = [];
    public bool $stood = false;
    public bool $splitOnce = false;

    public function add(Card $card): void
    {
        $this->cards[] = $card;
    }

    /** @return Card[] */
    public function cards(): array
    {
        return $this->cards;
    }

    public function canSplit(): bool
    {
        if ($this->splitOnce) return false;
        if (count($this->cards) !== 2) return false;
        return $this->cards[0]->rank === $this->cards[1]->rank;
    }

    public function isBlackjack(): bool
    {
        return count($this->cards) === 2 && $this->bestScore() === 21;
    }

    public function isBusted(): bool
    {
        return $this->minScore() > 21;
    }

    public function bestScore(): int
    {
        $min = $this->minScore();
        $aces = $this->aceCount();
        $score = $min;
        while ($aces > 0 && $score + 10 <= 21) {
            $score += 10;
            $aces--;
        }
        return $score;
    }

    private function minScore(): int
    {
        $sum = 0;
        foreach ($this->cards as $c) {
            $sum += $c->isAce() ? 1 : $c->faceValue();
        }
        return $sum;
    }

    private function aceCount(): int
    {
        $n = 0;
        foreach ($this->cards as $c) {
            if ($c->isAce()) $n++;
        }
        return $n;
    }
}
