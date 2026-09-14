<?php

namespace TomatoPHP\FilamentWithdrawals\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalMethod;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalRequest;

/**
 * @extends Factory<WithdrawalRequest>
 */
class WithdrawalRequestFactory extends Factory
{
    protected $model = WithdrawalRequest::class;

    public function definition(): array
    {
        return [
            'withdrawal_method_id' => WithdrawalMethod::factory(),
            'model_type' => config('filament-withdrawals.user_model'),
            'model_id' => 1,
            'status' => 'pending',
            'payload' => ['account_number' => $this->faker->iban()],
            'amount' => $this->faker->numberBetween(20, 400),
            'rate' => 1,
            'currency' => 'USD',
            'description' => $this->faker->sentence(),
            'date' => now()->toDateString(),
            'time' => now()->toTimeString(),
        ];
    }
}
