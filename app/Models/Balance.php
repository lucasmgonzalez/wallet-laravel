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

    /**
     * Create instance of Balance from a data array
     *
     * @param array $data
     * @return Balance
     */
    public static function createFromArray(array $data): Balance
    {
        return new static(new Money($data['amount'], $data['currency']));
    }

    /**
     * Cast Balance to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'amount' => $this->money->amount,
            'currency' => $this->money->currency,
        ];
    }

    /**
     * Calculate all transactions to create a Balance instance for an User
     *
     * @param User $user
     * @return Balance
     */
    public static function buildFromUser(User $user): Balance
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
            $money = $transactions->calculateMoneyForUser(
                $user,
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
