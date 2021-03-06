<?php

namespace App\Models\Transaction;

use App\Models\Money;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as BaseCollection;

class Collection extends BaseCollection
{
    public function calculateMoneyForUser(User $user, ?Money $initial = null): Money
    {
        return $this->filter(
            fn ($transaction) => in_array(
                $user->id,
                [$transaction->payer_id, $transaction->payee_id]
            )
        )->reduce(
            function (
                Money $acc,
                Transaction $transaction
            ) use ($user): Money {
                if ($transaction->payee_id == $user->id) {
                    return $acc->sum($transaction->money);
                } else if ($transaction->payer_id == $user->id) {
                    return $acc->subtract($transaction->money);
                }
                return $acc;
            },
            $initial ?? new Money(0)
        );
    }
}
