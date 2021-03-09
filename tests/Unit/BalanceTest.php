<?php

namespace Tests\Unit;

use App\Models\Balance;
use App\Models\Money;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BalanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_retrieve_balance_from_user()
    {
        $payee = User::factory()->create();

        // Creating some deposits transactions
        $transactions = Transaction::factory()
            ->count(10)
            ->deposit()
            ->to($payee)
            ->create();

        $total = $transactions->calculateMoneyForUser($payee);

        $balance = Balance::buildFromUser($payee);

        $this->assertTrue($total->equalsTo($balance->money));
    }

    public function test_can_create_from_array()
    {
        $data = ['amount' => 100, 'currency' => 'BRL'];

        $balance = Balance::createFromArray($data);

        $this->assertEquals($data['amount'], $balance->money->amount);
        $this->assertEquals($data['currency'], $balance->money->currency);
    }

    public function test_can_transform_to_array()
    {
        $balance = new Balance(new Money(100, 'BRL'));

        $data = $balance->toArray();

        $this->assertArrayHasKey('amount', $data);
        $this->assertEquals($balance->money->amount, $data['amount']);
        $this->assertArrayHasKey('currency', $data);
        $this->assertEquals($balance->money->currency, $data['currency']);
    }
}
