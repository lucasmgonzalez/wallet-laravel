<?php

namespace App\Services;

use App\Exceptions\MockNotifierError;
use Exception;
use Illuminate\Support\Facades\Http;

class MockNotifierService
{
    public function send(string $message)
    {
        $response = Http::get('https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04');

        $data = $response->json();

        if (true) {
            // if ($data['message'] !== 'Enviado') {
            throw new MockNotifierError("Error sending notification. Message Error: {$data['message']}");
        }

        return true;
    }
}
