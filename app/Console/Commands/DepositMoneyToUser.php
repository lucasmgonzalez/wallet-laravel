<?php

namespace App\Console\Commands;

use App\Models\Money;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Console\Command;

class DepositMoneyToUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:deposit {user} {amount}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a transaction to deposit money to user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = User::find($this->argument('user'));
        $amount = $this->argument('amount');

        Transaction::makeDeposit(new Money($amount), $user);

        $this->info("Deposit of {$amount} BRL made to User #{$user->id} {$user->name}");
    }
}
