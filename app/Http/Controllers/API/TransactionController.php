<?php

namespace App\Http\Controllers\API;

use App\Exceptions\InsufficientBalance;
use App\Exceptions\MockTransactionAuthorizerError;
use App\Exceptions\TransactionNotAuthorized;
use App\Exceptions\UserTypeCannotTransferMoney;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Models\Money;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\MoneyReceived;
use App\Services\MockTransactionAuthorizerService;

class TransactionController extends Controller
{
    public function __invoke(TransactionRequest $request)
    {
        $data = $request->validated();

        $payer = User::find($data['payer']);

        $transaction = new Transaction();
        $transaction->money = new Money($data['value']);
        $transaction->payer_id = $data['payer'];
        $transaction->payee_id = $data['payee'];

        // Check if payer is a juridical person
        if ($payer->isJuridicalPerson()) {
            throw new UserTypeCannotTransferMoney();
        }

        // Check payer's balance
        if ($payer->balance()->money->lessThan($transaction->money)) {
            throw new InsufficientBalance();
        }

        // Check Transaction Authorizer Service
        $authorizerService = app(MockTransactionAuthorizerService::class);
        try {
            $authorizerService->authorize($transaction);
        } catch (MockTransactionAuthorizerError $e) {
            throw new TransactionNotAuthorized();
        }

        // Save Transaction
        $transaction->save();

        // Notify payee about the received money
        $payee = User::find($data['payee']);
        $payee->notify(new MoneyReceived($transaction));

        return response()->json(['message' => 'success']);
    }
}
