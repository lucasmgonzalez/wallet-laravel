<?php

namespace Tests\Unit\Controller;

use App\BusinessServices\Contracts\TransactionServiceContract;
use App\BusinessServices\TransactionService;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_message()
    {
        $transaction = Transaction::factory()->create();

        $this->instance(
            TransactionServiceContract::class,
            Mockery::mock(TransactionService::class, function (MockInterface $mock) use ($transaction) {
                $mock->shouldReceive('makeTransactionBetweenUsers')->andReturn($transaction);
            })
        );

        $response = $this->postJson('/transaction', [
            'value' => $transaction->money->amount,
            'payer' => $transaction->payer->id,
            'payee' => $transaction->payee->id,
        ]);

        $response->assertJson(['message' => 'success']);
    }
}
