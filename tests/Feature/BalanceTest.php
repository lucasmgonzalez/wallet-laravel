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

    public function testCanRetrieveUserBalance()
    {
        [$user] = User::factory()->count(1)->create();

        // Adding money to payer
        Transaction::makeDeposit(new Money(100), $user);

        $response = $this->get("/{$user->id}/balance");

        $response->assertStatus(200);
        $response->assertJson([
            'amount' => 100,
            'currency' => 'BRL'
        ]);
    }
}
