<?php

namespace App\BusinessServices;

use App\BusinessServices\Contracts\TransactionServiceContract;
use App\Exceptions\InsufficientBalance;
use App\Exceptions\MockTransactionAuthorizerError;
use App\Exceptions\TransactionNotAuthorized;
use App\Exceptions\UserTypeCannotTransferMoney;
use App\Models\Money;
use App\Models\Transaction;
use App\Models\Transaction\Factory as TransactionFactory;
use App\Models\User;
use App\Notifications\MoneyReceived;
use App\Services\MockTransactionAuthorizerService;

class TransactionService implements TransactionServiceContract
{
    public MockTransactionAuthorizerService $authorizerService;

    public function __construct(MockTransactionAuthorizerService $authorizer)
    {
        $this->authorizerService = $authorizer;
    }

    public function makeTransactionBetweenUsers(int $amount, User $payer, User $payee): Transaction
    {
        $transaction = TransactionFactory::new()
            ->money(new Money($amount))
            ->payer($payer)
            ->payee($payee)
            ->make();

        // Check if payer is a juridical person
        if ($payer->isJuridicalPerson()) {
            throw new UserTypeCannotTransferMoney();
        }

        // Check payer's balance
        if ($payer->balance()->money->lessThan($transaction->money)) {
            throw new InsufficientBalance();
        }

        // Check Transaction Authorizer Service
        try {
            $this->authorizerService->authorize($transaction);
        } catch (MockTransactionAuthorizerError $e) {
            throw new TransactionNotAuthorized();
        }

        $transaction->save();

        // Notify payee about the received money
        $payee->notify(new MoneyReceived($transaction));

        return $transaction;
    }
}
