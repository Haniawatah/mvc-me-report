<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Proj\Calculator;

require_once __DIR__ . '/../../src/Proj/Calculator.php';

class CalculatorTest extends TestCase
{
    private Calculator $c;
    protected function setUp(): void { $this->c = new Calculator(); }

    public function testAdd(): void { $this->assertSame(5.0, $this->c->add(2,3)); }
    public function testSub(): void { $this->assertSame(-1.0, $this->c->sub(2,3)); }
    public function testMul(): void { $this->assertSame(6.0, $this->c->mul(2,3)); }
    public function testDiv(): void { $this->assertSame(2.5, $this->c->div(5,2)); }
    public function testDivZero(): void {
        $this->expectException(DivisionByZeroError::class);
        $this->c->div(1,0);
    }
}
