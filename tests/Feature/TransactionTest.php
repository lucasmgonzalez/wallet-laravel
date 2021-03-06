<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientBalance;
use App\Exceptions\UserTypeCannotTransferMoney;
use App\Models\Money;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function testCanMakeTransaction()
    {
        $users = User::factory()->count(15)->create();
        $payer = $users->first(fn ($user) => $user->id === 4);
        $payee = $users->first(fn ($user) => $user->id === 15);

        // Make a deposit to payer
        Transaction::makeDeposit(new Money(100), $payer);

        $response = $this->postJson('/transaction', [
            "value" => 100,
            "payer" => $payer->id,
            "payee" => $payee->id
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'success']);
    }

    // User can send money to User
    public function testNaturalPersonCanMakeTransactionToNaturalPerson()
    {
        [$payer, $payee] = User::factory()->count(2)->create();

        // Adding money to payer
        Transaction::makeDeposit(new Money(100), $payer);

        $response = $this->postJson('/transaction', [
            "value" => 100,
            "payer" => $payer->id,
            "payee" => $payee->id
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'success']);
    }

    // User can send money to Business
    public function testNaturalPersonCanMakeTransactionToJuridicalPerson()
    {
        [$payer] = User::factory()->count(1)->create();
        [$payee] = User::factory()->count(1)->juridicalPerson()->create();

        // Adding money to payer
        Transaction::makeDeposit(new Money(100), $payer);

        $response = $this->postJson('/transaction', [
            "value" => 100,
            "payer" => $payer->id,
            "payee" => $payee->id
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'success']);
    }

    // Business cannot send money
    public function testJuridicalPersonCannotMakeTransaction()
    {
        [$payer] = User::factory()->count(1)->juridicalPerson()->create();
        [$payee] = User::factory()->count(1)->create();

        // Adding money to payer
        Transaction::makeDeposit(new Money(100), $payer);

        $response = $this->postJson('/transaction', [
            "value" => 100,
            "payer" => $payer->id,
            "payee" => $payee->id
        ]);

        $response->assertStatus(422);
        $response->assertJson(['type' => UserTypeCannotTransferMoney::class]);
    }

    public function testUserCannotMakeTransactionWithInsufficientBalance()
    {
        [$payer, $payee] = User::factory()->count(2)->create();

        $response = $this->postJson('/transaction', [
            "value" => 100,
            "payer" => $payer->id,
            "payee" => $payee->id
        ]);

        $response->assertStatus(422);
        $response->assertJson(['type' => InsufficientBalance::class]);
    }
}
