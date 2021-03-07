<?php

namespace App\Http\Controllers\API;

use App\BusinessServices\Contracts\TransactionServiceContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Models\Money;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\MoneyReceived;

class TransactionController extends Controller
{
    public function __construct(TransactionServiceContract $transactionService) {
        $this->transactionService = $transactionService;
    }

    public function __invoke(TransactionRequest $request)
    {
        $data = $request->validated();

        $payer = User::find($data['payer']);
        $payee = User::find($data['payee']);

        $transaction = $this->transactionService->makeTransactionBetweenUsers(
            $data['value'],
            $payer,
            $payee
        );

        return response()->json(['message' => 'success']);
    }
}
