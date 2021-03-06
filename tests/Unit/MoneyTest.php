<?php

namespace Tests\Unit;

use App\Models\Money;
use Exception;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testFunctionHasSameCurrency()
    {
        $money = new Money(10, 'BRL');

        $this->assertTrue($money->hasSameCurrency($money));
    }

    public function testFunctionEqualsTo()
    {
        $money = new Money(10, 'BRL');

        $this->assertTrue($money->equalsTo($money));
    }

    public function testFunctionSum()
    {
        $money1 = new Money(10, 'BRL');
        $money2 = new Money(5, 'BRL');

        $sum = $money1->sum($money2);

        $this->assertEquals(15, $sum->amount);
        $this->assertEquals('BRL', $sum->currency);
    }

    public function testCannotSumWithDifferentCurrency()
    {
        $this->expectException(Exception::class);

        $money1 = new Money(10, 'BRL');
        $money2 = new Money(5, 'USD');

        $money1->sum($money2);
    }

    public function testFunctionSubtract()
    {
        $money1 = new Money(10, 'BRL');
        $money2 = new Money(5, 'BRL');

        $subtraction = $money1->subtract($money2);

        $this->assertEquals(5, $subtraction->amount);
        $this->assertEquals('BRL', $subtraction->currency);
    }

    public function testCannotSubtractWithDifferentCurrency()
    {
        $this->expectException(Exception::class);

        $money1 = new Money(10, 'BRL');
        $money2 = new Money(5, 'USD');

        $money1->sum($money2);
    }

    public function testFunctionLessThan()
    {
        $money1 = new Money(10, 'BRL');
        $money2 = new Money(5, 'BRL');

        $this->assertFalse($money1->lessThan($money2));
        $this->assertTrue($money2->lessThan($money1));
    }

    public function testFunctionGreaterThan()
    {
        $money1 = new Money(10, 'BRL');
        $money2 = new Money(5, 'BRL');

        $this->assertTrue($money1->greaterThan($money2));
        $this->assertFalse($money2->greaterThan($money1));
    }
}
