<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Exceptions\InvalidStateTransitionException;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    /**
     * Display a listing of registrations with pagination and filtering.
     */
    public function index(Request $request)
    {
        $query = Registration::query();

        // Optimize search on name, email, phone, or id (exact matching or prefix)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            // If it starts with CB- followed by a number, parse the ID
            if (preg_match('/^CB-(\d+)$/i', $search, $matches)) {
                $query->where('id', $matches[1]);
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_email', 'like', "%{$search}%")
                      ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('car_model')) {
            $query->where('car_model', $request->input('car_model'));
        }

        // Fast paginated response (ordered by newest first)
        $registrations = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // --- Statistics (always across all records, ignoring active filters) ---

        // Pending = registrations that still need agent action
        $pendingCount = Registration::whereIn('status', ['registered', 'test_drive_scheduled', 'test_drive_completed'])->count();

        // Purchased count
        $purchasedCount = Registration::where('status', 'purchased')->count();

        // Cancelled count
        $cancelledCount = Registration::where('status', 'cancelled')->count();

        // Promo slots remaining: 10 minus how many active CapBay Vroom registrations are in top 10
        $activeVroomCount = Registration::where('car_model', 'CapBay Vroom')
            ->where('status', '!=', 'cancelled')
            ->count();
        $promoSlotsRemaining = max(0, 10 - $activeVroomCount);

        return view('agent.index', compact(
            'registrations',
            'pendingCount',
            'purchasedCount',
            'cancelledCount',
            'promoSlotsRemaining'
        ));
    }

    /**
     * Display a single registration lookup detail.
     */
    public function show($id)
    {
        $registration = Registration::findOrFail($id);

        return view('agent.show', compact('registration'));
    }

    /**
     * Update customer financial info (down payment, loan approval status).
     */
    public function update(Request $request, $id)
    {
        $registration = Registration::findOrFail($id);

        $validated = $request->validate([
            'down_payment_paid' => 'required|numeric|min:0',
            'loan_approved'     => 'nullable|boolean',
        ]);

        // Convert RM amount to cents
        $dpPaidCents = (int) round($validated['down_payment_paid'] * 100);
        $loanApproved = $request->has('loan_approved');

        $registration->update([
            'down_payment_paid_cents' => $dpPaidCents,
            'loan_approved'           => $loanApproved,
        ]);

        return redirect()->route('agent.show', $registration->id)
            ->with('success', "Customer details updated. Promotion status and loan amounts recalculated.");
    }

    /**
     * Transition the state of a registration.
     */
    public function transition(Request $request, $id)
    {
        $registration = Registration::findOrFail($id);
        $newStatus = $request->input('status');

        try {
            $registration->transitionTo($newStatus);
            return redirect()->route('agent.show', $registration->id)
                ->with('success', "Registration status successfully transitioned to '" . ucfirst($newStatus) . "'.");
        } catch (InvalidStateTransitionException $e) {
            return redirect()->route('agent.show', $registration->id)
                ->with('error', $e->getMessage());
        }
    }
}
