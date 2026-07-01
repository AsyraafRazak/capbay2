<?php

namespace Database\Seeders;

use App\Models\Registration;
use Illuminate\Database\Seeder;

/**
 * DemoSeeder - Creates the Customer A, B, C scenario for live demo purposes.
 *
 * Run with: php artisan db:seed --class=DemoSeeder
 *
 * This seeds exactly 11 CapBay Vroom registrations:
 * - Customers 1-9: fill up positions 1-9 of the promo queue
 * - Customer B (position 2): no down payment, no loan — will be cancelled in the demo
 * - Customer C (position 11): has paid RM 20,000 DP and loan approved — waiting outside top 10
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Positions 1 and 3-10: generic active registrations to fill the queue
        // Position 1: Customer A — paid 20% DP (RM 40,000), loan approved, meets all promo conditions
        Registration::create([
            'customer_name'           => 'Customer A (Demo)',
            'customer_email'          => 'customer.a@demo.com',
            'customer_phone'          => '+60111111111',
            'car_model'               => 'CapBay Vroom',
            'price_cents'             => 20000000,
            'down_payment_paid_cents' => 4000000, // RM 40,000 (20% of RM 200k)
            'loan_approved'           => true,
            'status'                  => 'registered',
        ]);

        // Position 2: Customer B — no DP, no loan, will cancel in demo to free a slot
        Registration::create([
            'customer_name'           => 'Customer B (Demo)',
            'customer_email'          => 'customer.b@demo.com',
            'customer_phone'          => '+60122222222',
            'car_model'               => 'CapBay Vroom',
            'price_cents'             => 20000000,
            'down_payment_paid_cents' => 0,
            'loan_approved'           => false,
            'status'                  => 'registered',
        ]);

        // Positions 3-10: filler active registrations
        for ($i = 3; $i <= 10; $i++) {
            Registration::create([
                'customer_name'           => "Demo Filler #{$i}",
                'customer_email'          => "filler{$i}@demo.com",
                'customer_phone'          => '+6013' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'car_model'               => 'CapBay Vroom',
                'price_cents'             => 20000000,
                'down_payment_paid_cents' => 0,
                'loan_approved'           => false,
                'status'                  => 'registered',
            ]);
        }

        // Position 11: Customer C — paid exactly RM 20,000 DP, loan approved, but outside top 10
        Registration::create([
            'customer_name'           => 'Customer C (Demo)',
            'customer_email'          => 'customer.c@demo.com',
            'customer_phone'          => '+60133333333',
            'car_model'               => 'CapBay Vroom',
            'price_cents'             => 20000000,
            'down_payment_paid_cents' => 2000000, // RM 20,000 (10% of RM 200k)
            'loan_approved'           => true,
            'status'                  => 'registered',
        ]);

        $this->command->info('Demo seeder complete. 11 CapBay Vroom registrations created.');
        $this->command->info('Customer A: position 1, promo eligible.');
        $this->command->info('Customer B: position 2, no DP/loan — cancel this in the demo.');
        $this->command->info('Customer C: position 11, will become eligible after B cancels.');
    }
}
