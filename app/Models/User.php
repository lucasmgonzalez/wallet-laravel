<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public Balance $balance;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function balance($forceRecalculation = false) : Balance
    {
        if (!isset($this->balance) || $forceRecalculation) {
            $this->balance = Balance::buildFromUser($this);
        }

        return $this->balance;
    }

    public function isJuridicalPerson() : bool
    {
        return strlen($this->cpf_cnpj) === 14;
    }

    public function isNaturalPerson() : bool
    {
        return strlen($this->cpf_cnpj) === 11;
    }
}
