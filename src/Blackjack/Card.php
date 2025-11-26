<?php

declare(strict_types=1);

namespace App\Blackjack;

class Card
{
    public function __construct(
        public readonly string $rank,
        public readonly string $suit
    ) {}

    public function isAce(): bool
    {
        return $this->rank === 'A';
    }

    public function faceValue(): int
    {
        if ($this->rank === 'A') return 11;
        if (in_array($this->rank, ['K','Q','J'], true)) return 10;
        return (int) $this->rank;
    }

    public function label(): string
    {
        return $this->rank . $this->suit;
    }
}
