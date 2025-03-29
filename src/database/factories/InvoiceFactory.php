<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['B', 'P', 'V']);
        $paid_date = $status === 'P' ? $this->faker->dateTimeThisDecade() : null;
        return [
            //
            'status' => $status,
            'customer_id' => Customer::factory(),
            'amount' => $this->faker->numberBetween(100, 20000),
            'billed_date' => $this->faker->dateTimeThisDecade(),
            'paid_date' => $paid_date
        ];
    }
}
