<?php

namespace Tests\Feature\Feature;

use App\Models\Money;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BalanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_retrieve_user_balance()
    {
        $user = User::factory()->create();

        // Adding money to payer
        Transaction::makeDeposit(new Money(100), $user);

        $response = $this->get(route("user.balance", [ 'user'=> $user ]));

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'amount' => 100,
                'currency' => 'BRL'
            ]
        ]);
    }
}
