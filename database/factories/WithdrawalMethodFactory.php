<?php

namespace TomatoPHP\FilamentWithdrawals\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalMethod;

/**
 * @extends Factory<WithdrawalMethod>
 */
class WithdrawalMethodFactory extends Factory
{
    protected $model = WithdrawalMethod::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Bank Transfer', 'PayPal', 'Vodafone Cash', 'Payoneer']),
            'min_amount' => 10,
            'max_amount' => 5000,
            'currency' => 'USD',
            'rate' => 1,
            'description' => $this->faker->sentence(),
            'status' => true,
        ];
    }
}
