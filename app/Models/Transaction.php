<?php

namespace App\Models;

use App\Models\Transaction\Collection as TransactionCollection;
use App\Models\Transaction\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'money' => Money::class
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amount',
        'currency',
        'payer_id',
        'payee_id',
    ];

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id', 'id');
    }

    public function payee()
    {
        return $this->belongsTo(User::class, 'payee_id', 'id');
    }

    // Scopes
    public static function scopeCreatedAfter($query, $timestamp)
    {
        return $query->where('created_at', '>', $timestamp);
    }

    public static function scopeWhereUserParticipated($query, User $user)
    {
        return $query->where(function ($query) use ($user) {
            return $query->where('payer_id', $user->id)
                ->orWhere('payee_id', $user->id);
        });
    }

    public static function makeDeposit(Money $money, User $payee): Transaction
    {
        $transaction = Factory::new()->money($money)->deposit()->payee($payee)->create();

        return $transaction;
    }

    // Custom Collection
    public function newCollection(array $models = [])
    {
        return new TransactionCollection($models);
    }
}
