<?php

namespace App\Models;

use App\Exceptions\InvalidStateTransitionException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'car_model',
        'price_cents',
        'down_payment_paid_cents',
        'loan_approved',
        'status',
    ];

    protected $casts = [
        'loan_approved' => 'boolean',
        'price_cents' => 'integer',
        'down_payment_paid_cents' => 'integer',
    ];

    protected $attributes = [
        'car_model' => 'CapBay Vroom',
        'price_cents' => 20000000,
        'down_payment_paid_cents' => 0,
        'loan_approved' => false,
        'status' => 'registered',
    ];

    // Define valid transitions from each state
    private static array $validTransitions = [
        'registered' => ['test_drive_scheduled', 'cancelled'],
        'test_drive_scheduled' => ['test_drive_completed', 'cancelled'],
        'test_drive_completed' => ['purchased', 'cancelled'],
        'purchased' => [],
        'cancelled' => [],
    ];

    /**
     * Transition the registration to a new status.
     *
     * @param string $newStatus
     * @throws InvalidStateTransitionException
     */
    public function transitionTo(string $newStatus): void
    {
        $currentStatus = $this->status ?? 'registered';

        if ($currentStatus === $newStatus) {
            return;
        }

        $allowed = self::$validTransitions[$currentStatus] ?? [];

        if (!in_array($newStatus, $allowed, true)) {
            throw new InvalidStateTransitionException($currentStatus, $newStatus);
        }

        $this->status = $newStatus;
        $this->save();
    }

    /**
     * Check if this registration is in the first 10 active (non-cancelled) registrations for CapBay Vroom.
     *
     * @return bool
     */
    public function getIsEligibleAttribute(): bool
    {
        if ($this->car_model !== 'CapBay Vroom') {
            return false;
        }

        // If the registration is already cancelled, it's not eligible
        if ($this->status === 'cancelled') {
            return false;
        }

        // Get the first 10 active registrations by ID ascending
        $top10ActiveIds = self::where('car_model', 'CapBay Vroom')
            ->where('status', '!=', 'cancelled')
            ->orderBy('id', 'asc')
            ->limit(10)
            ->pluck('id')
            ->toArray();

        // If this record hasn't been saved yet, check if there are fewer than 10 active records
        if (!$this->exists) {
            return count($top10ActiveIds) < 10;
        }

        return in_array($this->id, $top10ActiveIds, true);
    }

    /**
     * Check if the registration meets all conditions to receive the 15% discount.
     * Conditions:
     * 1. In top 10 active registrations.
     * 2. Paid at least 10% of the standard down payment (10% of RM 20,000 = RM 2,000 = 200,000 cents).
     * 3. Loan approved = true.
     *
     * @return bool
     */
    public function getMeetsPromotionConditionsAttribute(): bool
    {
        if (!$this->is_eligible) {
            return false;
        }

        // Standard down payment is 10% of RM 200,000 = RM 20,000 (2,000,000 cents).
        // 10% of down payment is RM 2,000 (200,000 cents).
        $minDownPaymentCents = 200000;

        return $this->down_payment_paid_cents >= $minDownPaymentCents && $this->loan_approved;
    }

    /**
     * Get final price after discount if applicable.
     *
     * @return int Price in cents
     */
    public function getFinalPriceCentsAttribute(): int
    {
        $basePrice = $this->price_cents;

        if ($this->meets_promotion_conditions) {
            // Apply 15% discount
            return (int) round($basePrice * 0.85);
        }

        return $basePrice;
    }

    /**
     * Get the loan amount (Price - Down Payment Paid).
     *
     * @return int Loan amount in cents
     */
    public function getLoanAmountCentsAttribute(): int
    {
        $loan = $this->final_price_cents - $this->down_payment_paid_cents;
        return max(0, $loan);
    }

    // --- Currency Formatter Helpers ---

    private function formatCentsToRm(int $cents): string
    {
        return 'RM ' . number_format($cents / 100, 2);
    }

    public function getFormattedPriceAttribute(): string
    {
        return $this->formatCentsToRm($this->price_cents);
    }

    public function getFormattedDownPaymentPaidAttribute(): string
    {
        return $this->formatCentsToRm($this->down_payment_paid_cents);
    }

    public function getFormattedFinalPriceAttribute(): string
    {
        return $this->formatCentsToRm($this->final_price_cents);
    }

    public function getFormattedLoanAmountAttribute(): string
    {
        return $this->formatCentsToRm($this->loan_amount_cents);
    }
}
