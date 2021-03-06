<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        [$payer_id, $payee_id] = User::inRandomOrder()->limit(2)->get()->pluck('id');

        return [
            'amount' => $this->faker->numberBetween(1, 100),
            'currency' => 'BRL',
            'payer_id' => $payer_id,
            'payee_id' => $payee_id
        ];
    }

    public function deposit()
    {
        return $this->state(function (array $attributes) {
            return [
                'payer_id' => null
            ];
        });
    }
}
