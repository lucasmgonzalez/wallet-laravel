<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\BalanceResource;
use App\Models\User;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function __invoke(User $user)
    {
        return new BalanceResource($user->balance());
    }
}
