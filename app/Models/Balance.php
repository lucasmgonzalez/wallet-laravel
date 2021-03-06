<?php

namespace App\Models;

use Illuminate\Contracts\Support\Arrayable;

class Balance implements Arrayable
{
    public Money $money;

    public function __construct(Money $money)
    {
        $this->money = $money;
    }

    public static function createFromArray(array $data) : Balance
    {
        return new static(new Money($data['amount'], $data['currency']));
    }

    public function toArray() : array
    {
        return [
            'amount' => $this->money->amount,
            'currency' => $this->money->currency,
        ];
    }

    public static function buildFromUser(User $user) : Balance
    {
        $snapshotKey = BalanceSnapshot::getUserSnapshotKey($user);

        $snapshot = BalanceSnapshot::get($snapshotKey);

        $balance = $snapshot->balance;

        // Get all Transactions after snapshot
        $transactions = Transaction::with(['payer', 'payee'])
            ->createdAfter($snapshot->created_at)
            ->whereUserParticipated($user)
            ->orderBy('created_at', 'asc')
            ->get();

        // Calculate current money amount
        if ($transactions->count() > 0) {
            $money = $transactions->reduce(
                function (Money $acc, Transaction $transaction) use ($user) : Money{
                    if ($transaction->payee->id === $user->id) {
                        return $acc->sum($transaction->money);
                    }else if ($transaction->payer->id === $user->id) {
                        return $acc->subtract($transaction->money);
                    }
                },
                $balance->money
            );

            $balance = new static($money);

            // Save new balance snapshot
            BalanceSnapshot::set(
                $snapshotKey,
                $balance,
                $transactions->last()->created_at
            );
        }

        return $balance;
    }
}
