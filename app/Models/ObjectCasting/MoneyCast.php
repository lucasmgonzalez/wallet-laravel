<?php

namespace App\Models\ObjectCasting;

use App\Models\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

class MoneyCast implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        return new Money($attributes['amount'], $attributes['currency']);
    }

    public function set($model, $key, $value, $attributes)
    {
        if (!$value instanceof Money) {
            throw new InvalidArgumentException('The given value is not a Money instance');
        }

        return [
            'amount' => $value->amount,
            'currency' => $value->currency
        ];
    }
}
