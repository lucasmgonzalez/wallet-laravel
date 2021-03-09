<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientBalance;
use App\Exceptions\MockNotifierError;
use App\Exceptions\MockTransactionAuthorizerError;
use App\Exceptions\TransactionNotAuthorized;
use App\Exceptions\UserTypeCannotTransferMoney;
use App\Models\Money;
use App\Models\Transaction;
use App\Models\User;
use App\Services\MockTransactionAuthorizerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    private $mockTransactionAuthorizerURL = 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';
    private $mockNotifierURL = 'https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04';

    private function mockMockTransactionAuthorizerSuccessfulResponse()
    {
        Http::fake([
            $this->mockTransactionAuthorizerURL => Http::response([
                'message' => 'Autorizado'
            ], 200)
        ]);
    }

    private function mockMockTransactionAuthorizerErrorResponse()
    {
        Http::fake([
            $this->mockTransactionAuthorizerURL => Http::response([
                'message' => 'Não autorizado'
            ], 403)
        ]);
    }

    private function mockMockNotifierSuccessfulResponse()
    {
        Http::fake([
            $this->mockNotifierURL => Http::response([
                'message' => 'Enviado'
            ], 200)
        ]);
    }

    private function mockMockNotifierErrorResponse()
    {
        Http::fake([
            $this->mockNotifierURL => Http::response([
                'message' => 'Não enviado'
            ], 422)
        ]);
    }

    // Default payload
    public function test_can_make_transaction()
    {
        $this->mockMockTransactionAuthorizerSuccessfulResponse();
        $this->mockMockNotifierSuccessfulResponse();

        $users = User::factory()->count(15)->create();
        $payer = $users->first(fn ($user) => $user->id === 4);
        $payee = $users->first(fn ($user) => $user->id === 15);

        // Make a deposit to payer
        Transaction::factory()
            ->money(new Money(100))
            ->deposit()
            ->to($payer)
            ->create();

        $response = $this->postJson(route('transaction'), [
            "value" => 100,
            "payer" => $payer->id,
            "payee" => $payee->id
        ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'success']);
    }

    // User can send money to User
    public function test_natural_person_can_make_transaction_to_natural_person()
    {
        $this->mockMockTransactionAuthorizerSuccessfulResponse();
        $this->mockMockNotifierSuccessfulResponse();

        [$payer, $payee] = User::factory()->count(2)->create();

        // Adding money to payer
        Transaction::factory()
            ->money(new Money(100))
            ->deposit()
            ->to($payer)
            ->create();

        $response = $this->postJson(route('transaction'), [
            "value" => 100,
            "payer" => $payer->id,
            "payee" => $payee->id
        ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'success']);
    }

    // User can send money to Business
    public function test_natural_person_can_make_transaction_to_juridical_person()
    {
        $this->mockMockTransactionAuthorizerSuccessfulResponse();
        $this->mockMockNotifierSuccessfulResponse();

        [$payer] = User::factory()->count(1)->create();
        [$payee] = User::factory()->count(1)->juridicalPerson()->create();

        // Adding money to payer
        Transaction::factory()
            ->money(new Money(100))
            ->deposit()
            ->to($payer)
            ->create();

        $response = $this->postJson(route('transaction'), [
            "value" => 100,
            "payer" => $payer->id,
            "payee" => $payee->id
        ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'success']);
    }

    // Business cannot send money
    public function test_juridical_person_cannot_make_transaction()
    {
        $this->mockMockTransactionAuthorizerSuccessfulResponse();
        $this->mockMockNotifierSuccessfulResponse();

        [$payer] = User::factory()->count(1)->juridicalPerson()->create();
        [$payee] = User::factory()->count(1)->create();

        // Adding money to payer
        Transaction::factory()
            ->money(new Money(100))
            ->deposit()
            ->to($payer)
            ->create();

        $response = $this->postJson(route('transaction'), [
            "value" => 100,
            "payer" => $payer->id,
            "payee" => $payee->id
        ]);

        $response->assertStatus(422);
        $response->assertJson(['type' => UserTypeCannotTransferMoney::class]);
    }

    public function test_user_cannot_make_transaction_with_insufficient_balance()
    {
        $this->mockMockTransactionAuthorizerSuccessfulResponse();
        $this->mockMockNotifierSuccessfulResponse();

        [$payer, $payee] = User::factory()->count(2)->create();

        $response = $this->postJson(route('transaction'), [
            "value" => 100,
            "payer" => $payer->id,
            "payee" => $payee->id
        ]);

        $response->assertStatus(422);
        $response->assertJson(['type' => InsufficientBalance::class]);
    }

    public function test_throws_transaction_not_authorized_on_service_disapproval()
    {
        $this->mockMockTransactionAuthorizerErrorResponse();
        $this->mockMockNotifierSuccessfulResponse();

        [$payer, $payee] = User::factory()->count(2)->create();

        // Adding money to payer
        Transaction::factory()
            ->money(new Money(100))
            ->deposit()
            ->to($payer)
            ->create();

        $response = $this->postJson(route('transaction'), [
            "value" => 100,
            "payer" => $payer->id,
            "payee" => $payee->id
        ]);

        $response->assertStatus(422);
        $response->assertJson(['type' => TransactionNotAuthorized::class]);
    }

    public function test_doesnt_throws_error_on_notifier_service_error()
    {
        $this->mockMockTransactionAuthorizerSuccessfulResponse();
        $this->mockMockNotifierErrorResponse();

        [$payer, $payee] = User::factory()->count(2)->create();

        // Adding money to payer
        Transaction::factory()
            ->money(new Money(100))
            ->deposit()
            ->to($payer)
            ->create();

        $response = $this->postJson(route('transaction'), [
            "value" => 100,
            "payer" => $payer->id,
            "payee" => $payee->id
        ]);

        $response->assertStatus(201);
        $response->assertDontSeeText(MockNotifierError::class);
    }
}
