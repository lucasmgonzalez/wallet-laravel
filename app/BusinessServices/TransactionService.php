<?php

namespace App\BusinessServices;

use App\BusinessServices\Contracts\TransactionServiceContract;
use App\Exceptions\InsufficientBalance;
use App\Exceptions\MockTransactionAuthorizerError;
use App\Exceptions\TransactionNotAuthorized;
use App\Exceptions\UserLockedForTransaction;
use App\Exceptions\UserTypeCannotTransferMoney;
use App\Models\Money;
use App\Models\Transaction;
use App\Models\Transaction\Factory as TransactionFactory;
use App\Models\User;
use App\Notifications\MoneyReceived;
use App\Services\MockTransactionAuthorizerService;
use Illuminate\Support\Facades\Cache;

class TransactionService implements TransactionServiceContract
{
    public MockTransactionAuthorizerService $authorizerService;

    public function __construct(MockTransactionAuthorizerService $authorizer)
    {
        $this->authorizerService = $authorizer;
    }

    protected function transactionLockKey(User $user)
    {
        return "transaction-lock-{$user->id}";
    }

    protected function isUserLocked(User $user)
    {
        return Cache::has($this->transactionLockKey($user));
    }

    protected function lockUserForTransaction(User $user)
    {
        Cache::put($this->transactionLockKey($user), true, 30);
    }

    protected function unlockUserForTransaction(User $user)
    {
        Cache::forget($this->transactionLockKey($user));
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

        if ($this->isUserLocked($payer)) {
            throw new UserLockedForTransaction($payer);
        }

        $this->lockUserForTransaction($payer);

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

        $this->unlockUserForTransaction($payer);

        // Notify payee about the received money
        $payee->notify(new MoneyReceived($transaction));

        return $transaction;
    }
}
