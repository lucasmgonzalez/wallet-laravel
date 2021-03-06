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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Return an User's Balance
     *
     * @param boolean $forceRecalculation
     * @return Balance
     */
    public function balance($forceRecalculation = false): Balance
    {
        if (!isset($this->balance) || $forceRecalculation) {
            $this->balance = Balance::buildFromUser($this);
        }

        return $this->balance;
    }

    /**
     * Check if User is a juridical person
     *
     * @return boolean
     */
    public function isJuridicalPerson(): bool
    {
        return strlen($this->cpf_cnpj) === 14;
    }

    /**
     * Check if User is a natural person
     *
     * @return boolean
     */
    public function isNaturalPerson(): bool
    {
        return strlen($this->cpf_cnpj) === 11;
    }
}
