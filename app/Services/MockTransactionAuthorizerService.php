<?php

namespace App\Services;

use App\Exceptions\MockTransactionAuthorizerError;
use App\Exceptions\TransactionNotAuthorized;
use App\Models\Transaction;
use Exception;
use Illuminate\Support\Facades\Http;

class MockTransactionAuthorizerService
{
    /**
     * Request service authorization for give Transaction
     *
     * @param Transaction $transaction
     * @return bool
     */
    public function authorize(Transaction $transaction): bool
    {
        $response = Http::get('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6');

        if ($response->status() !== 200) {
            throw new MockTransactionAuthorizerError("Error on authorizer response. Error code: {$response->status()}");
        }

        $data = $response->json();

        if ($data['message'] !== 'Autorizado') {
            throw new TransactionNotAuthorized('Transaction not authorized by the service');
        }

        return true;
    }
}
