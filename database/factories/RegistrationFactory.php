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
        // CapBay Vroom is the current promotional model — past records only have Lite and Sport
        $carModel = $this->faker->randomElement(['CapBay Lite', 'CapBay Lite', 'CapBay Sport']);
        $priceCents = $carModel === 'CapBay Lite' ? 12000000 : 25000000;

        // Down payment distributions relative to each model's price
        $dpOptions = [
            0,
            0,
            $this->faker->numberBetween(100000, 500000),  // RM 1,000 - RM 5,000 (partial)
            (int) round($priceCents * 0.10),               // Exact 10% of car price
            (int) round($priceCents * 0.20),               // 20% of car price
            (int) round($priceCents * 0.50),               // 50% of car price
        ];
        $dpPaidCents = $this->faker->randomElement($dpOptions);

        return [
            'customer_name'            => $this->faker->name(),
            'customer_email'           => $this->faker->unique()->safeEmail(),
            'customer_phone'           => $this->faker->phoneNumber(),
            'car_model'                => $carModel,
            'price_cents'              => $priceCents,
            'down_payment_paid_cents'  => $dpPaidCents,
            'loan_approved'            => $this->faker->boolean(50),
            'status'                   => $this->faker->randomElement(['registered', 'test_drive_scheduled', 'test_drive_completed', 'purchased', 'cancelled']),
            'created_at'               => $this->faker->dateTimeBetween('-2 years', '-1 month'),
        ];
    }
}
