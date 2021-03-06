<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;

class TransactionCollection extends Collection
{
    public function calculateMoneyForUser(User $user, ?Money $money = null): Money
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
            $money ?? new Money(0)
        );
    }
}
