<?php

use App\Http\Controllers\API\Me\BalanceController;
use App\Http\Controllers\API\User\BalanceController as UserBalanceController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\Me\IndexController as MeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('user/{user}/balance', UserBalanceController::class)
    ->name('user.balance');

// Route::middleware('auth:sanctum')
//     ->post('/transaction', TransactionController::class)
//     ->name('transaction');
Route::post('/transaction', TransactionController::class)
    ->name('transaction');

Route::prefix('me')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/', MeController::class)
            ->name('me');

        Route::get('balance', BalanceController::class)
            ->name('me.balance');
    });
