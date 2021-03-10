<?php

namespace App\Models;

use App\Exceptions\DifferentMoneyCurrency;
use App\Exceptions\MoneyCannotBeNegative;
use App\Exceptions\MoneySubtractionOverflow;
use App\Models\ObjectCasting\MoneyCast;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Castable;

class Money implements Castable
{
    public int $amount;
    public string $currency;

    public function __construct(int $amount, string $currency = 'BRL')
    {
        if ($amount < 0) {
            throw new MoneyCannotBeNegative('Amount cannot be a negative number');
        }

        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * Check if current instance and a given other have the same currency
     *
     * @param Money $money
     * @return boolean
     */
    public function hasSameCurrency(Money $money): bool
    {
        return $this->currency === $money->currency;
    }

    /**
     * Verify if instance of Money is equals to another Money (They are the same ValueObject)
     *
     * @param Money $money
     * @return boolean
     */
    public function equalsTo(Money $money): bool
    {
        return $this->hasSameCurrency($money)
            && $this->amount === $money->amount;
    }

    /**
     * Check if current instance has less amount value than another instance of Money
     *
     * @param Money $money
     * @return boolean
     */
    public function lessThan(Money $money): bool
    {
        if (!$this->hasSameCurrency($money)) {
            throw new DifferentMoneyCurrency('You can only operate with money with the same currency');
        }

        return $this->amount < $money->amount;
    }

    /**
     * Check if current instance has greater amount value than another instance of Money
     *
     * @param Money $money
     * @return boolean
     */
    public function greaterThan(Money $money): bool
    {
        if (!$this->hasSameCurrency($money)) {
            throw new DifferentMoneyCurrency('You can only operate with money with the same currency');
        }

        return $this->amount > $money->amount;
    }

    /**
     * Sum current instance of Money with another creating a new instance with the new amount
     *
     * @param Money $money
     * @return Money
     */
    public function sum(Money $money): Money
    {
        if (!$this->hasSameCurrency($money)) {
            throw new DifferentMoneyCurrency('You can only operate with money with the same currency');
        }

        $newAmount = $this->amount + $money->amount;

        return new Money($newAmount, $this->currency);
    }

    /**
     * Subtract current instance of Money with another creating a new instance with the new amount
     *
     * @param Money $money
     * @return Money
     */
    public function subtract(Money $money): Money
    {
        if (!$this->hasSameCurrency($money)) {
            throw new DifferentMoneyCurrency('You can only operate with money with the same currency');
        }

        if ($this->lessThan($money)) {
            throw new MoneySubtractionOverflow('You cannot remove an amount greater than the current value');
        }

        $newAmount = $this->amount - $money->amount;

        return new Money($newAmount, $this->currency);
    }

    public function __toString()
    {
        return "{$this->amount} {$this->currency}";
    }

    // Castable
    /**
     * Declaring Casting Class
     *
     * @param array $arguments
     * @return string
     */
    public static function castUsing(array $arguments): string
    {
        return MoneyCast::class;
    }
}
