<?php

namespace Tests\Unit;

use App\Models\Money;
use Exception;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_function_has_same_currency()
    {
        $money = new Money(10, 'BRL');

        $this->assertTrue($money->hasSameCurrency($money));
    }

    public function test_function_equals_to()
    {
        $money = new Money(10, 'BRL');

        $this->assertTrue($money->equalsTo($money));
    }

    public function test_function_sum()
    {
        $money1 = new Money(10, 'BRL');
        $money2 = new Money(5, 'BRL');

        $sum = $money1->sum($money2);

        $this->assertEquals(15, $sum->amount);
        $this->assertEquals('BRL', $sum->currency);
    }

    public function test_cannot_sum_with_different_currency()
    {
        $this->expectException(Exception::class);

        $money1 = new Money(10, 'BRL');
        $money2 = new Money(5, 'USD');

        $money1->sum($money2);
    }

    public function test_function_subtract()
    {
        $money1 = new Money(10, 'BRL');
        $money2 = new Money(5, 'BRL');

        $subtraction = $money1->subtract($money2);

        $this->assertEquals(5, $subtraction->amount);
        $this->assertEquals('BRL', $subtraction->currency);
    }

    public function test_cannot_subtract_with_different_currency()
    {
        $this->expectException(Exception::class);

        $money1 = new Money(10, 'BRL');
        $money2 = new Money(5, 'USD');

        $money1->sum($money2);
    }

    public function test_function_less_than()
    {
        $money1 = new Money(10, 'BRL');
        $money2 = new Money(5, 'BRL');

        $this->assertFalse($money1->lessThan($money2));
        $this->assertTrue($money2->lessThan($money1));
    }

    public function test_function_greater_than()
    {
        $money1 = new Money(10, 'BRL');
        $money2 = new Money(5, 'BRL');

        $this->assertTrue($money1->greaterThan($money2));
        $this->assertFalse($money2->greaterThan($money1));
    }
}
