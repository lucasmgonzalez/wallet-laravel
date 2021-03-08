<?php

namespace App\BusinessServices\Contracts;

use App\Models\Transaction;
use App\Models\User;

interface TransactionServiceContract
{
    public function makeTransactionBetweenUsers(int $amount, User $payer, User $payee): Transaction;
}
