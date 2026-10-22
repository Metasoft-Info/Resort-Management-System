<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingReconciliationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeSuperAdmin($request);

        $search = trim((string) $request->query('q', ''));
        $query = Booking::query()
            ->with(['room', 'bookingRooms.room', 'payments.recordedBy'])
            ->latest('id');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                if (ctype_digit($search)) {
                    $builder->where('id', (int) $search);
                }

                $builder->orWhere('customer_name', 'like', '%'.$search.'%')
                    ->orWhere('customer_phone', 'like', '%'.$search.'%')
                    ->orWhereHas('bookingRooms.room', function ($roomQuery) use ($search) {
                        $roomQuery->where('room_number', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('room', function ($roomQuery) use ($search) {
                        $roomQuery->where('room_number', 'like', '%'.$search.'%');
                    });
            });
        } else {
            $query->whereRaw('1 = 0');
        }

        $bookings = $query->paginate(15)->withQueryString();
        $selectedBooking = null;

        if ($request->filled('booking_id')) {
            $selectedBooking = Booking::with([
                'room.roomType', 'bookingRooms.room.roomType', 'roomShifts.fromRoom',
                'roomShifts.toRoom', 'payments.recordedBy', 'financialSnapshots.recordedBy',
            ])->findOrFail((int) $request->query('booking_id'));
        }

        return view('admin.booking-reconciliation.index', [
            'search' => $search,
            'bookings' => $bookings,
            'selectedBooking' => $selectedBooking,
            'financials' => $selectedBooking?->getFinancialBreakdown(),
        ]);
    }

    public function update(Request $request, Booking $booking)
    {
        $this->authorizeSuperAdmin($request);

        $validated = $request->validate([
            'discount_type' => 'required|in:none,flat,percentage',
            'discount_value' => 'nullable|numeric|min:0|max:99999999.99',
            'payment_amounts' => 'nullable|array',
            'payment_amounts.*' => 'required|numeric|min:0|max:99999999.99',
            'effective_date' => 'required|date_format:Y-m-d',
            'reason' => 'required|string|min:8|max:1000',
            'confirm_reconcile' => 'accepted',
        ]);

        $result = DB::transaction(function () use ($booking, $validated) {
            $lockedBooking = Booking::with([
                'room.roomType', 'bookingRooms.room.roomType', 'roomShifts.fromRoom', 'roomShifts.toRoom',
            ])->whereKey($booking->getKey())->lockForUpdate()->firstOrFail();
            $payments = $lockedBooking->payments()->lockForUpdate()->get();
            $lockedBooking->setRelation('payments', $payments);

            if ($lockedBooking->is_complimentary) {
                throw ValidationException::withMessages([
                    'discount_type' => 'Complimentary bookings cannot be financially reconciled here.',
                ]);
            }

            $paymentAmounts = $validated['payment_amounts'] ?? [];
            $existingPaymentIds = $payments->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all();
            $submittedPaymentIds = collect(array_keys($paymentAmounts))->map(fn ($id) => (string) $id)->sort()->values()->all();
            if ($existingPaymentIds !== $submittedPaymentIds) {
                throw ValidationException::withMessages([
                    'payment_amounts' => 'The payment ledger changed while this page was open. Reload the booking before reconciling.',
                ]);
            }

            $before = $lockedBooking->getFinancialBreakdown();
            $roomRent = (float) $before['room_rent'];
            $discountType = $validated['discount_type'];
            $discountValue = $discountType === 'none'
                ? 0.0
                : round((float) ($validated['discount_value'] ?? 0), 2);

            if ($discountType === 'percentage' && $discountValue > 100) {
                throw ValidationException::withMessages(['discount_value' => 'Percentage discount cannot exceed 100%.']);
            }
            if ($discountType === 'flat' && $discountValue > $roomRent) {
                throw ValidationException::withMessages(['discount_value' => 'Flat discount cannot exceed the room rent.']);
            }

            $nextDiscountAmount = $discountType === 'flat'
                ? $discountValue
                : ($discountType === 'percentage' ? round($roomRent * $discountValue / 100, 2) : 0.0);
            $nextDiscountPercentage = $discountType === 'percentage' ? $discountValue : 0.0;
            $reason = trim($validated['reason']);
            $actorId = (int) auth()->id();
            $paymentChanges = [];
            $candidateAdvance = 0.0;
            $candidatePayments = 0.0;
            $candidateRefunds = 0.0;
            $hasAdvanceLedger = false;

            foreach ($payments as $payment) {
                $type = $payment->type ?? 'payment';
                if (!in_array($type, ['advance', 'payment', 'refund'], true)) {
                    throw ValidationException::withMessages([
                        'payment_amounts' => 'This booking contains an unsupported payment type and cannot be reconciled safely.',
                    ]);
                }

                $hasAdvanceLedger = $hasAdvanceLedger || $type === 'advance';
                $amount = round((float) $paymentAmounts[$payment->id], 2);
                if ($type === 'advance') {
                    $candidateAdvance += $amount;
                } elseif ($type === 'payment') {
                    $candidatePayments += $amount;
                } else {
                    $candidateRefunds += $amount;
                }
            }

            $candidateAdvance = $hasAdvanceLedger
                ? $candidateAdvance
                : max(0, (float) ($lockedBooking->advance_payment ?? 0));
            if (round($candidateRefunds, 2) > round($candidateAdvance + $candidatePayments, 2)) {
                throw ValidationException::withMessages([
                    'payment_amounts' => 'Refunds cannot exceed the reconciled advance and payment deposits.',
                ]);
            }

            foreach ($payments as $payment) {
                $nextAmount = round((float) $paymentAmounts[$payment->id], 2);
                $previousAmount = round((float) $payment->amount, 2);
                if ($nextAmount === $previousAmount) {
                    continue;
                }

                $paymentChanges[] = [
                    'payment_id' => $payment->id,
                    'type' => $payment->type ?? 'payment',
                    'payment_date' => $payment->payment_date?->toDateString(),
                    'previous_amount' => $previousAmount,
                    'corrected_amount' => $nextAmount,
                ];
                $payment->amount = $nextAmount;
                $payment->note = trim(($payment->note ?? '')."\n[Financial reconciliation by superadmin #{$actorId}] ".$reason);
                $payment->save();
            }

            $oldDiscountType = $lockedBooking->discount_type ?: 'none';
            $oldDiscountValue = $oldDiscountType === 'percentage'
                ? round((float) $lockedBooking->discount_percentage, 2)
                : round((float) $lockedBooking->discount_amount, 2);
            $oldDiscountReference = trim((string) $lockedBooking->discount_reference);
            $discountChanged = $oldDiscountType !== $discountType
                || $oldDiscountValue !== $discountValue
                || ($nextDiscountAmount > 0 && $lockedBooking->discount_status !== 'approved');

            if (!$discountChanged && empty($paymentChanges)) {
                throw ValidationException::withMessages([
                    'reason' => 'There are no financial changes to apply. Adjust a discount or payment amount.',
                ]);
            }

            $lockedBooking->setRelation('payments', $lockedBooking->payments()->lockForUpdate()->get());
            if ($hasAdvanceLedger) {
                $lockedBooking->advance_payment = round((float) $lockedBooking->payments
                    ->where('type', 'advance')->sum('amount'), 2);
            }

            $lockedBooking->discount_type = $discountType;
            $lockedBooking->discount_amount = $discountType === 'flat' ? $nextDiscountAmount : 0;
            $lockedBooking->discount_percentage = $nextDiscountPercentage;
            $lockedBooking->discount_status = $nextDiscountAmount > 0 ? 'approved' : null;
            $lockedBooking->discount_requested_by = null;
            $lockedBooking->discount_approved_by = $nextDiscountAmount > 0 ? $actorId : null;
            $lockedBooking->discount_approved_at = $nextDiscountAmount > 0 ? now('Asia/Dhaka') : null;
            $lockedBooking->discount_reference = $nextDiscountAmount > 0
                ? substr($reason.($oldDiscountReference !== '' ? ' | Previous: '.$oldDiscountReference : ''), 0, 255)
                : null;
            $lockedBooking->updated_by_id = $actorId;

            $after = $lockedBooking->getFinancialBreakdown();
            $lockedBooking->total_amount = $after['room_rent'];
            $lockedBooking->vat_amount = $after['vat'];
            $lockedBooking->remaining_payment = $after['remaining'];
            $lockedBooking->payment_status = $lockedBooking->getCalculatedPaymentStatus();
            $lockedBooking->saveQuietly();

            $effectiveDate = $validated['effective_date'];
            if (\Illuminate\Support\Facades\Schema::hasTable('booking_financial_snapshots')) {
                $laterSnapshotDates = $lockedBooking->financialSnapshots()
                    ->reorder()
                    ->whereDate('effective_date', '>=', $effectiveDate)
                    ->orderBy('effective_date')
                    ->pluck('effective_date')
                    ->map(fn ($date) => $date instanceof \Carbon\CarbonInterface ? $date->toDateString() : (string) $date);

                $snapshotDates = $laterSnapshotDates->push($effectiveDate)->unique()->sort()->values();
                foreach ($snapshotDates as $snapshotDate) {
                    $lockedBooking->recordFinancialSnapshot(
                        substr('Super Admin reconciliation: '.$reason, 0, 255),
                        $snapshotDate
                    );
                }
            }

            ActivityLog::log('Super Admin reconciled room booking finances', 'Booking', $lockedBooking->id, [
                'reason' => $reason,
                'effective_date' => $effectiveDate,
                'discount_before' => [
                    'type' => $oldDiscountType,
                    'value' => $oldDiscountValue,
                    'amount' => $before['discount'],
                    'reference' => $oldDiscountReference,
                ],
                'discount_after' => [
                    'type' => $discountType,
                    'value' => $discountValue,
                    'amount' => $after['discount'],
                    'reference' => $lockedBooking->discount_reference,
                ],
                'payment_changes' => $paymentChanges,
                'financials_before' => $before,
                'financials_after' => $after,
            ]);

            return $after;
        });

        return redirect()
            ->route('admin.booking-reconciliation.index', ['q' => $booking->id, 'booking_id' => $booking->id])
            ->with('success', 'Booking financials reconciled. The payment ledger, bill status, and financial snapshot have been recalculated and audited.')
            ->with('reconciled_financials', $result);
    }

    private function authorizeSuperAdmin(Request $request): void
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);
    }
}
