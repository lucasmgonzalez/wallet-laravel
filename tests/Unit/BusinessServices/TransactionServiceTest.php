<?php

namespace Tests\Unit\BusinessServices;

use App\BusinessServices\TransactionService;
use App\Exceptions\InsufficientBalance;
use App\Exceptions\MockTransactionAuthorizerError;
use App\Exceptions\TransactionNotAuthorized;
use App\Exceptions\UserLockedForTransaction;
use App\Exceptions\UserTypeCannotTransferMoney;
use App\Models\Transaction;
use App\Models\User;
use App\Services\MockNotifierService;
use App\Services\MockTransactionAuthorizerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function mockNotifierSuccessfulResponse()
    {
        $this->instance(
            MockNotifierService::class,
            Mockery::mock(MockNotifierService::class, function (MockInterface $mock) {
                $mock->shouldReceive('send')->andReturn(true);
            })
        );
    }

    protected function mockAuthorizerServiceSuccessfulResponse()
    {
        return Mockery::mock(MockTransactionAuthorizerService::class, function (MockInterface $mock) {
            $mock->shouldReceive('authorize')->andReturn(true);
        });
    }

    protected function mockAuthorizerServiceErrorResponse()
    {
        return Mockery::mock(MockTransactionAuthorizerService::class, function (MockInterface $mock) {
            $mock->shouldReceive('authorize')->andThrow(MockTransactionAuthorizerError::class);
        });
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_make_transaction_between_users()
    {
        $this->mockNotifierSuccessfulResponse();

        $serviceMock = $this->mockAuthorizerServiceSuccessfulResponse();

        $transactionMock = Transaction::factory()->make();

        // Adding funds to payer
        Transaction::factory()
            ->money($transactionMock->money)
            ->payee($transactionMock->payer)
            ->deposit()
            ->create();

        $transactionService = new TransactionService($serviceMock);

        $transaction = $transactionService->makeTransactionBetweenUsers(
            $transactionMock->money->amount,
            $transactionMock->payer,
            $transactionMock->payee,
        );

        $this->assertTrue($transactionMock->money->equalsTo($transaction->money));
        $this->assertEquals($transactionMock->payee_id, $transaction->payee_id);
        $this->assertEquals($transactionMock->payer_id, $transaction->payer_id);
    }

    public function test_cannot_make_transaction_with_insufficient_funds()
    {
        $this->mockNotifierSuccessfulResponse();

        $this->expectException(InsufficientBalance::class);

        $serviceMock = $this->mockAuthorizerServiceSuccessfulResponse();

        $transactionMock = Transaction::factory()->make();

        $transactionService = new TransactionService($serviceMock);

        $transaction = $transactionService->makeTransactionBetweenUsers(
            $transactionMock->money->amount,
            $transactionMock->payer,
            $transactionMock->payee,
        );
    }

    public function test_juridical_person_cannot_make_transactions()
    {
        $this->mockNotifierSuccessfulResponse();

        $this->expectException(UserTypeCannotTransferMoney::class);

        $serviceMock = $this->mockAuthorizerServiceSuccessfulResponse();

        $transactionMock = Transaction::factory()
            ->payer(User::factory()->juridicalPerson()->create())
            ->make();

        $transactionService = new TransactionService($serviceMock);

        $transaction = $transactionService->makeTransactionBetweenUsers(
            $transactionMock->money->amount,
            $transactionMock->payer,
            $transactionMock->payee,
        );
    }

    public function test_cannot_make_transaction_if_authorizer_has_error()
    {
        $this->mockNotifierSuccessfulResponse();

        $this->expectException(TransactionNotAuthorized::class);

        $serviceMock = $this->mockAuthorizerServiceErrorResponse();

        $transactionMock = Transaction::factory()->make();

        // Adding funds to payer
        Transaction::factory()
            ->money($transactionMock->money)
            ->payee($transactionMock->payer)
            ->deposit()
            ->create();

        $transactionService = new TransactionService($serviceMock);

        $transaction = $transactionService->makeTransactionBetweenUsers(
            $transactionMock->money->amount,
            $transactionMock->payer,
            $transactionMock->payee,
        );
    }
    public function test_cannot_make_transaction_if_user_is_locked()
    {
        $this->mockNotifierSuccessfulResponse();
        $serviceMock = $this->mockAuthorizerServiceSuccessfulResponse();

        $this->expectException(UserLockedForTransaction::class);

        $transactionMock = Transaction::factory()->make();

        // Mock a locked payer
        Cache::shouldReceive('has')
            ->with("transaction-lock-{$transactionMock->payer->id}")
            ->andReturn(true);

        // Adding funds to payer
        Transaction::factory()
            ->money($transactionMock->money)
            ->payee($transactionMock->payer)
            ->deposit()
            ->create();

        $transactionService = new TransactionService($serviceMock);

        $transaction = $transactionService->makeTransactionBetweenUsers(
            $transactionMock->money->amount,
            $transactionMock->payer,
            $transactionMock->payee,
        );
    }
}
