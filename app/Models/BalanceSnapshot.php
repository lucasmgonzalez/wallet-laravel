<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class BalanceSnapshot
{
    public Balance $balance;
    public Carbon $created_at;

    public function __construct(Balance $balance, ?Carbon $created_at)
    {
        $this->balance = $balance;
        $this->created_at = $created_at ?? Carbon::now();
    }

    /**
     * Creating a balance cache key for given User
     *
     * @param User $user
     * @return string
     */
    public static function getUserSnapshotKey(User $user): string
    {
        return  "balance_snapshot_{$user->id}";
    }

    /**
     * Retrieve a BalanceSnapshot instance from cache
     *
     * @param string $key
     * @return BalanceSnapshot
     */
    public static function get(string $key): BalanceSnapshot
    {
        $data = Cache::get($key, [
            'balance' => new Balance(new Money(0)),
            'created_at' => Carbon::createFromTimeString('0000-00-00 00:00:00')
        ]);

        return new static(
            $data['balance'],
            $data['created_at']
        );
    }

    /**
     * Save a BalanceSnapshot instance to cache
     *
     * @param string $key
     * @param Balance $balance
     * @param Carbon $created_at
     * @return void
     */
    public static function set(string $key, Balance $balance, Carbon $created_at)
    {
        return Cache::forever($key, [
            'balance' => $balance,
            'created_at' => $created_at
        ]);
    }
}
