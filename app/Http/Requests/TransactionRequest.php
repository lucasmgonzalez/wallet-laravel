<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Check if payer is the user making the request.

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $userClass = User::class;

        return [
            'value' => ['required', 'numeric', 'integer', 'min:1'],
            'payer' => ['required', "exists:{$userClass},id"],
            'payee' => ['required', "exists:{$userClass},id"]
        ];
    }
}
