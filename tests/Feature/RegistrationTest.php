<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Exceptions\InvalidStateTransitionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the state machine transitions.
     */
    public function test_state_machine_valid_and_invalid_transitions(): void
    {
        $registration = Registration::create([
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '0123456789',
            'car_model' => 'CapBay Vroom',
            'status' => 'registered',
        ]);

        $this->assertEquals('registered', $registration->status);

        // Valid transition: registered -> test_drive_scheduled
        $registration->transitionTo('test_drive_scheduled');
        $this->assertEquals('test_drive_scheduled', $registration->status);

        // Valid transition: test_drive_scheduled -> test_drive_completed
        $registration->transitionTo('test_drive_completed');
        $this->assertEquals('test_drive_completed', $registration->status);

        // Valid transition: test_drive_completed -> purchased
        $registration->transitionTo('purchased');
        $this->assertEquals('purchased', $registration->status);

        // Invalid transition: purchased -> cancelled (purchased is final)
        $this->expectException(InvalidStateTransitionException::class);
        $registration->transitionTo('cancelled');
    }

    /**
     * Test another invalid transition from start directly to final.
     */
    public function test_invalid_direct_transition_fails(): void
    {
        $registration = Registration::create([
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '0123456789',
            'car_model' => 'CapBay Vroom',
            'status' => 'registered',
        ]);

        // Invalid transition: registered -> purchased directly (must go through scheduled and completed)
        $this->expectException(InvalidStateTransitionException::class);
        $registration->transitionTo('purchased');
    }



    /**
     * Explicit test for Customer A, Customer B, Customer C scenario.
     */
    public function test_customer_abc_scenario(): void
    {
        // Customer A (1st to register, paid 20% of car price = RM 40,000 = 4000000 cents, loan approved)
        $customerA = Registration::create([
            'customer_name' => 'Customer A',
            'customer_email' => 'customera@example.com',
            'customer_phone' => '0123456789',
            'car_model' => 'CapBay Vroom',
            'down_payment_paid_cents' => 4000000,
            'loan_approved' => true,
            'status' => 'registered',
        ]);

        // Customer B (2nd to register)
        $customerB = Registration::create([
            'customer_name' => 'Customer B',
            'customer_email' => 'customerb@example.com',
            'customer_phone' => '0123456789',
            'car_model' => 'CapBay Vroom',
            'down_payment_paid_cents' => 0,
            'loan_approved' => false,
            'status' => 'registered',
        ]);

        // Customers 3 to 10 (8 customers) to fill up the top 10 queue
        for ($i = 3; $i <= 10; $i++) {
            Registration::create([
                'customer_name' => "Customer {$i}",
                'customer_email' => "customer{$i}@example.com",
                'customer_phone' => '0123456789',
                'car_model' => 'CapBay Vroom',
                'down_payment_paid_cents' => 0,
                'loan_approved' => false,
                'status' => 'registered',
            ]);
        }

        // Customer C (11th to register, paid 10% of car price = RM 20,000 = 2000000 cents, loan approved)
        $customerC = Registration::create([
            'customer_name' => 'Customer C',
            'customer_email' => 'customerc@example.com',
            'customer_phone' => '0123456789',
            'car_model' => 'CapBay Vroom',
            'down_payment_paid_cents' => 2000000,
            'loan_approved' => true,
            'status' => 'registered',
        ]);

        // Check initial state:
        // Customer A: in top 10, eligible, meets conditions, gets discount
        $this->assertTrue($customerA->is_eligible);
        $this->assertTrue($customerA->meets_promotion_conditions);
        $this->assertEquals(17000000, $customerA->final_price_cents); // RM 170k (15% off RM 200k)
        $this->assertEquals(13000000, $customerA->loan_amount_cents); // RM 130k (170k - 40k DP)

        // Customer B: in top 10, eligible, but does not meet conditions (no DP, no loan approval)
        $this->assertTrue($customerB->is_eligible);
        $this->assertFalse($customerB->meets_promotion_conditions);
        $this->assertEquals(20000000, $customerB->final_price_cents); // RM 200k

        // Customer C: 11th registration, NOT eligible initially
        $this->assertFalse($customerC->is_eligible);
        $this->assertFalse($customerC->meets_promotion_conditions);
        $this->assertEquals(20000000, $customerC->final_price_cents); // RM 200k

        // Customer B decides not to buy and cancels
        $customerB->transitionTo('cancelled');

        // Refresh/re-fetch models to evaluate updated DB state
        $customerC = $customerC->fresh();
        $customerB = $customerB->fresh();

        // Customer B is cancelled, so they are no longer eligible
        $this->assertFalse($customerB->is_eligible);

        // Customer C moves up to 10th active registration, and becomes eligible!
        $this->assertTrue($customerC->is_eligible);
        $this->assertTrue($customerC->meets_promotion_conditions);
        $this->assertEquals(17000000, $customerC->final_price_cents); // RM 170k (Discount applied!)
        $this->assertEquals(15000000, $customerC->loan_amount_cents); // RM 150k (170k - 20k DP)
    }
}
