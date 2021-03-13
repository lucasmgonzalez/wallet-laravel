<?php

namespace App\Http\Controllers\API\Me;

use App\Http\Controllers\Controller;
use App\Http\Resources\BalanceResource;
use App\Models\User;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function __invoke(Request $request)
    {
        return new BalanceResource($request->user()->balance());
    }
}
