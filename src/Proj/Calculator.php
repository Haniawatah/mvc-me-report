<?php
declare(strict_types=1);
namespace App\Proj;

/** Enkel räknare för test/demo. */
class Calculator
{
    public function add(float $a, float $b): float { return $a + $b; }
    public function sub(float $a, float $b): float { return $a - $b; }
    public function mul(float $a, float $b): float { return $a * $b; }
    /** @throws \DivisionByZeroError */
    public function div(float $a, float $b): float {
        if ($b == 0.0) throw new \DivisionByZeroError("Division by zero");
        return $a / $b;
    }
}
