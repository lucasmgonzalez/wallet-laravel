<?php

namespace Database\Factories;

use App\Models\Money;
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

        return [
            'amount' => $this->faker->numberBetween(1, 100),
            'currency' => 'BRL',
            'payer_id' => User::factory(),
            'payee_id' => User::factory()
        ];
    }

    public function money(Money $money)
    {
        return $this->state(function (array $attributes) use ($money) {
            return [
                'amount' => $money->amount,
                'currency' => $money->currency
            ];
        });
    }

    public function payer(?User $user)
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'payer_id' => $user->id ?? null
            ];
        });
    }

    public function payee(User $user)
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'payee_id' => $user->id
            ];
        });
    }

    public function deposit()
    {
        return $this->payer(null);
    }

    public function from(User $user)
    {
        return $this->payer($user);
    }

    public function to(User $user)
    {
        return $this->payee($user);
    }

    public function randomPayee()
    {
        return $this->state(function (array $attributes) {
            $query = User::query();

            if (is_int($attributes['payer_id'])) {
                $query->where('id', '!=', $attributes['payer_id']);
            }

            $user = $query->inRandomOrder()->limit(1)->first();

            return [
                'payee_id' => $user->id
            ];
        });
    }

    public function randomPayer()
    {
        return $this->state(function (array $attributes) {
            $query = User::query();

            if (is_int($attributes['payer_id'])) {
                $query->where('id', '!=', $attributes['payee_id']);
            }

            $user = $query->inRandomOrder()->limit(1)->first();

            return [
                'payer_id' => $user->id
            ];
        });
    }

    public function randomUsers()
    {
        return $this->randomPayer()->randomPayee();
    }
}

