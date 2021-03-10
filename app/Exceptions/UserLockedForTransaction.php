<?php

namespace App\Exceptions;

use App\Exceptions\Traits\ApiRenderable;
use App\Models\User;
use Exception;

class UserLockedForTransaction extends Exception
{
    use ApiRenderable;

    public function __construct(User $user)
    {
        parent::__construct("O usuário {$user->id} está trancado para transações enquanto outras transações são finalizadas.");
    }
}
