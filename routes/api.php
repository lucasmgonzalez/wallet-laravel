<?php

use App\Http\Controllers\API\BalanceController;
use App\Http\Controllers\API\TransactionController;
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

Route::get('user/{user}/balance', BalanceController::class)->name('user.balance');

// Route::middleware('auth:sanctum')
//     ->post('/transaction', TransactionController::class)
//     ->name('transaction');
Route::post('/transaction', TransactionController::class)
    ->name('transaction');

Route::prefix('me')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/', function (Request $request) {
            return $request->user();
        });

        Route::get('balance', function (Request $request) {
            return $request->user()->balance();
        });
    });
