<?php

namespace App\Models\Transaction;

use App\Models\Money;
use App\Models\Transaction;
use App\Models\User;
use Ramsey\Uuid\Uuid;

class Factory
{
    protected Transaction $instance;

    public function __construct()
    {
        $this->instance = new Transaction();
        $this->instance->id = Uuid::uuid4();
    }

    public static function new(): Factory
    {
        return new static;
    }

    public function money(Money $money): Factory
    {
        $this->instance->money = $money;

        return $this;
    }

    public function payer(User $payer): Factory
    {
        $this->instance->payer()->associate($payer);

        return $this;
    }

    public function deposit(): Factory
    {
        $this->instance->payer_id = null;

        return $this;
    }

    public function payee(User $payee): Factory
    {
        $this->instance->payee()->associate($payee);

        return $this;
    }

    public function make(): Transaction
    {
        return $this->instance;
    }

    public function create(): Transaction
    {
        $this->instance->save();

        return $this->instance;
    }
}
