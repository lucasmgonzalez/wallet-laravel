<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $casts = [
        'money' => Money::class
    ];

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
        $transaction = new Transaction();

        $transaction->money = $money;
        $transaction->payer_id = null;
        $transaction->payee_id = $payee->id;

        $transaction->save();

        return $transaction;
    }
}
