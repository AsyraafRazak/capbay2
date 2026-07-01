<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Show the registration form.
     */
    public function create()
    {
        return view('customer.register');
    }

    /**
     * Store a new registration.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:50',
            'car_model' => 'required|string|in:CapBay Vroom,CapBay Lite,CapBay Sport',
        ]);

        // Default prices in cents
        $prices = [
            'CapBay Vroom' => 20000000, // RM 200,000.00
            'CapBay Lite'  => 12000000, // RM 120,000.00
            'CapBay Sport' => 25000000, // RM 250,000.00
        ];

        $registration = Registration::create([
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'car_model' => $validated['car_model'],
            'price_cents' => $prices[$validated['car_model']] ?? 20000000,
            'down_payment_paid_cents' => 0,
            'loan_approved' => false,
            'status' => 'registered',
        ]);

        return redirect()->route('customer.register')
            ->with('success', "Registration for {$registration->customer_name} completed successfully! Your registration ID is CB-{$registration->id}. We will contact you soon for your test drive appointment.");
    }
}
