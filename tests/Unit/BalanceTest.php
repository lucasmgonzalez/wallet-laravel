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

    public function testCanRetrieveBalanceFromUser()
    {
        [$payer, $payee] = User::factory()->count(2)->create();

        $transactions = Transaction::factory()->count(10)->create([
            'payer_id' => $payer->id,
            'payee_id' => $payee->id
        ]);

        $total = $transactions->reduce(function($acc, $transaction) {
            return $acc->sum($transaction->money);
        }, new Money(0, 'BRL'));

        $balance = Balance::buildFromUser($payee);

        $this->assertTrue($total->equalsTo($balance->money));
    }

    public function testCanCreateFromArray()
    {
        $data = ['amount' => 100, 'currency' => 'BRL'];

        $balance = Balance::createFromArray($data);

        $this->assertEquals($data['amount'], $balance->money->amount);
        $this->assertEquals($data['currency'], $balance->money->currency);
    }

    public function testCanTransformToArray()
    {
        $balance = new Balance(new Money(100, 'BRL'));

        $data = $balance->toArray();

        $this->assertArrayHasKey('amount', $data);
        $this->assertEquals($balance->money->amount, $data['amount']);
        $this->assertArrayHasKey('currency', $data);
        $this->assertEquals($balance->money->currency, $data['currency']);
    }
}
