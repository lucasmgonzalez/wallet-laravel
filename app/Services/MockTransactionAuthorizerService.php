<?php

namespace App\Services;

use App\Exceptions\MockTransactionAuthorizerError;
use App\Models\Transaction;
use Exception;
use Illuminate\Support\Facades\Http;

class MockTransactionAuthorizerService
{
    public function authorize(Transaction $transaction)
    {
        $response = Http::get('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6');

        if ($response->status() !== 200) {
            throw new MockTransactionAuthorizerError("Error on authorizer response. Error code: {$response->status()}");
        }

        $data = $response->json();

        if ($response->json()['message'] !== 'Autorizado') {
            // throw new \Exception('Transaction not authorized');
            return false;
        }

        return true;
    }
}
