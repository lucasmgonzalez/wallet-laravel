<?php

namespace App\Models;

use App\Models\ObjectCasting\MoneyCast;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Castable;

class Money implements Castable
{
    public int $amount;
    public string $currency;

    public function __construct(int $amount, string $currency = 'BRL')
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function equalsTo(Money $money): bool
    {
        return $this->hasSameCurrency($money)
            && $this->amount === $money->amount;
    }

    public function lessThan(Money $money): bool
    {
        if (!$this->hasSameCurrency($money)) {
            throw new Exception('You can only operate with money with the same currency');
        }

        return $this->amount < $money->amount;
    }

    public function greaterThan(Money $money): bool
    {
        if (!$this->hasSameCurrency($money)) {
            throw new Exception('You can only operate with money with the same currency');
        }

        return $this->amount > $money->amount;
    }

    public function hasSameCurrency(Money $money): bool
    {
        return $this->currency === $money->currency;
    }

    public function sum(Money $money): Money
    {
        if (!$this->hasSameCurrency($money)) {
            throw new Exception('You can only operate with money with the same currency');
        }

        $newAmount = $this->amount + $money->amount;

        return new Money($newAmount, $this->currency);
    }

    public function subtract(Money $money): Money
    {
        if (!$this->hasSameCurrency($money)) {
            throw new Exception('You can only operate with money with the same currency');
        }

        $newAmount = $this->amount - $money->amount;

        return new Money($newAmount, $this->currency);
    }

    // Castable
    public static function castUsing(array $arguments)
    {
        return MoneyCast::class;
    }
}
