<?php

use App\Http\Controllers\API\BalanceController;
use App\Http\Controllers\API\TransactionController;
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

Route::get('user/{user}/balance', BalanceController::class)->name('user.balance');

Route::post('/transaction', TransactionController::class)->name('transaction');
