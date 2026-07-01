<?php

namespace Database\Factories;

use App\Models\Registration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Registration>
 */
class RegistrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $carModel = $this->faker->randomElement(['CapBay Vroom', 'CapBay Vroom', 'CapBay Vroom', 'CapBay Vroom', 'CapBay Lite', 'CapBay Sport']);
        $priceCents = $carModel === 'CapBay Vroom' ? 20000000 : ($carModel === 'CapBay Lite' ? 12000000 : 25000000);
        
        // Down payment distributions
        $dpPaidCents = $this->faker->randomElement([
            0,
            0,
            $this->faker->numberBetween(10000, 150000), // RM 100 - RM 1500
            200000, // RM 2000 (Exactly 10% of DP for Vroom)
            400000, // RM 4000 (20% of DP)
            2000000, // RM 20,000 (Full DP)
        ]);

        return [
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->unique()->safeEmail(),
            'customer_phone' => $this->faker->phoneNumber(),
            'car_model' => $carModel,
            'price_cents' => $priceCents,
            'down_payment_paid_cents' => $dpPaidCents,
            'loan_approved' => $this->faker->boolean(50), // 50% chance of true
            'status' => $this->faker->randomElement(['registered', 'test_drive_scheduled', 'test_drive_completed', 'purchased', 'cancelled']),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
