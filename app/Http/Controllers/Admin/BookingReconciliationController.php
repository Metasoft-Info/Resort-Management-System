<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\ConventionBooking;
use App\Models\ConventionPayment;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
            'roomOptions' => Room::orderBy('room_number')->get(['id', 'room_number']),
        ]);
    }

    public function conventionIndex(Request $request)
    {
        $this->authorizeSuperAdmin($request);

        $search = trim((string) $request->query('q', ''));
        $query = ConventionBooking::query()->with(['conventionHall', 'payments.receivedBy'])->latest('id');
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                if (ctype_digit($search)) {
                    $builder->where('id', (int) $search);
                }
                $builder->orWhere('customer_name', 'like', '%'.$search.'%')
                    ->orWhere('customer_phone', 'like', '%'.$search.'%')
                    ->orWhere('organization_name', 'like', '%'.$search.'%')
                    ->orWhereHas('conventionHall', fn ($hallQuery) => $hallQuery->where('name', 'like', '%'.$search.'%'));
            });
        } else {
            $query->whereRaw('1 = 0');
        }

        $bookings = $query->paginate(15)->withQueryString();
        $selectedBooking = null;
        $groupBookings = collect();
        $groupFinancials = collect();
        $groupPayments = collect();
        if ($request->filled('convention_booking_id')) {
            $selectedBooking = ConventionBooking::with(['conventionHall', 'payments.receivedBy'])
                ->findOrFail((int) $request->query('convention_booking_id'));
            $groupBookings = ConventionBooking::with('conventionHall')
                ->where('customer_phone', $selectedBooking->customer_phone)
                ->whereDate('event_date', $selectedBooking->event_date?->toDateString())
                ->where('time_slot', $selectedBooking->time_slot)
                ->where('status', '!=', 'cancelled')
                ->orderBy('id')->get();
            if (!$groupBookings->contains('id', $selectedBooking->id)) {
                $groupBookings->push($selectedBooking);
            }
            $groupFinancials = $groupBookings->mapWithKeys(fn ($item) => [
                $item->id => $this->getConventionFinancials($item),
            ]);
            $groupPayments = ConventionPayment::with('receivedBy')
                ->whereIn('convention_booking_id', $groupBookings->pluck('id'))
                ->orderBy('payment_date')->orderBy('id')->get();
        }

        return view('admin.booking-reconciliation.convention', compact(
            'search', 'bookings', 'selectedBooking', 'groupBookings', 'groupFinancials', 'groupPayments'
        ));
    }

    public function updateConvention(Request $request, ConventionBooking $conventionBooking)
    {
        $this->authorizeSuperAdmin($request);
        $validated = $request->validate([
            'discount_type' => 'required|in:none,flat,percentage',
            'discount_value' => 'nullable|numeric|min:0|max:99999999',
            'payment_amounts' => 'nullable|array',
            'payment_amounts.*' => 'required|numeric|min:0|max:99999999',
            'effective_date' => 'required|date_format:Y-m-d',
            'reason' => 'required|string|min:8|max:1000',
            'confirm_reconcile' => 'accepted',
        ]);

        DB::transaction(function () use ($conventionBooking, $validated) {
            $lockedSelected = ConventionBooking::whereKey($conventionBooking->getKey())->lockForUpdate()->firstOrFail();
            $groupQuery = ConventionBooking::where('customer_phone', $lockedSelected->customer_phone)
                ->whereDate('event_date', $lockedSelected->event_date?->toDateString())
                ->where('time_slot', $lockedSelected->time_slot)
                ->where('status', '!=', 'cancelled');
            $groupIds = $groupQuery->orderBy('id')->lockForUpdate()->pluck('id')->all();
            if (!in_array($lockedSelected->id, $groupIds, true)) {
                $groupIds[] = $lockedSelected->id;
            }
            $group = ConventionBooking::whereIn('id', $groupIds)->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $payments = ConventionPayment::whereIn('convention_booking_id', $groupIds)
                ->orderBy('id')->lockForUpdate()->get();
            $paymentNoteColumn = Schema::hasColumn('convention_payments', 'notes')
                ? 'notes'
                : (Schema::hasColumn('convention_payments', 'note') ? 'note' : null);
            $paymentAmounts = $validated['payment_amounts'] ?? [];
            $currentPaymentIds = $payments->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all();
            $submittedPaymentIds = collect(array_keys($paymentAmounts))->map(fn ($id) => (string) $id)->sort()->values()->all();
            if ($currentPaymentIds !== $submittedPaymentIds) {
                throw ValidationException::withMessages(['payment_amounts' => 'The convention payment ledger changed while the page was open. Reload the event before reconciling.']);
            }

            $beforeFinancials = $group->mapWithKeys(fn ($item) => [$item->id => $this->getConventionFinancials($item)]);
            $oldPaymentTotal = round((float) $payments->sum('amount'));
            $newPaymentTotal = 0.0;
            $paymentChanges = [];
            $actorId = (int) auth()->id();
            foreach ($payments as $payment) {
                $next = round((float) $paymentAmounts[$payment->id]);
                $newPaymentTotal += $next;
                $previous = round((float) $payment->amount);
                if ($next !== $previous) {
                    $paymentChanges[] = [
                        'payment_id' => $payment->id,
                        'booking_id' => $payment->convention_booking_id,
                        'payment_date' => $payment->payment_date?->toDateString(),
                        'previous_amount' => $previous,
                        'corrected_amount' => $next,
                    ];
                    $payment->amount = $next;
                    if ($paymentNoteColumn) {
                        $payment->setAttribute($paymentNoteColumn, trim(($payment->getAttribute($paymentNoteColumn) ?? '')."\n[Super Admin reconciliation #{$actorId}] ".trim($validated['reason'])));
                    }
                    $payment->save();
                }
            }

            $target = $group->get($lockedSelected->id);
            $oldDiscountType = in_array($target->discount_type, ['flat', 'percentage'], true) ? $target->discount_type : 'none';
            $oldDiscountValue = $oldDiscountType === 'percentage'
                ? round((float) $target->discount_value, 2)
                : round((float) $target->discount);
            $discountType = $validated['discount_type'];
            $discountValue = $discountType === 'none'
                ? 0.0
                : ($discountType === 'percentage'
                    ? round((float) ($validated['discount_value'] ?? 0), 2)
                    : round((float) ($validated['discount_value'] ?? 0)));
            $targetFinancials = $beforeFinancials->get($target->id);
            $subtotal = $targetFinancials['hall_rent'] + $targetFinancials['food_cost'] + $targetFinancials['addons_cost'];
            if ($discountType === 'percentage' && $discountValue > 100) {
                throw ValidationException::withMessages(['discount_value' => 'Percentage discount cannot exceed 100%.']);
            }
            if ($discountType === 'flat' && $discountValue > $subtotal) {
                throw ValidationException::withMessages(['discount_value' => 'Discount cannot exceed this hall booking subtotal.']);
            }
            $discountAmount = $discountType === 'percentage'
                ? min($subtotal, round($subtotal * $discountValue / 100))
                : ($discountType === 'flat' ? min($subtotal, $discountValue) : 0.0);

            $discountChanged = $oldDiscountType !== $discountType || $oldDiscountValue !== $discountValue;
            if (!$discountChanged && empty($paymentChanges)) {
                throw ValidationException::withMessages(['reason' => 'There are no changes to apply. Adjust the discount or an existing payment amount.']);
            }

            $currentGroupDeposits = round((float) $group->sum('advance_payment'));
            $newGroupDeposits = round($currentGroupDeposits + ($newPaymentTotal - $oldPaymentTotal));
            if ($newGroupDeposits < 0) {
                throw ValidationException::withMessages(['payment_amounts' => 'The correction would make the event deposit negative.']);
            }

            $target->discount_type = $discountType === 'none' ? 'flat' : $discountType;
            $target->discount_value = $discountValue;
            $target->discount = $discountAmount;
            $target->discount_status = $discountAmount > 0 ? 'approved' : null;
            $target->discount_requested_by = null;
            $target->discount_approved_by = $discountAmount > 0 ? $actorId : null;
            $target->discount_approved_at = $discountAmount > 0 ? now('Asia/Dhaka') : null;
            if (Schema::hasColumn('convention_bookings', 'updated_by_id')) {
                $target->updated_by_id = $actorId;
            }

            $afterTotals = [];
            foreach ($group as $item) {
                if ((int) $item->id === (int) $target->id) {
                    $item->discount_type = $target->discount_type;
                    $item->discount_value = $target->discount_value;
                    $item->discount = $target->discount;
                }
                $afterTotals[$item->id] = $this->getConventionFinancials($item);
            }
            $totalBills = round((float) collect($afterTotals)->sum('total_amount'));
            if (!empty($paymentChanges) && $newGroupDeposits > $totalBills) {
                throw ValidationException::withMessages(['payment_amounts' => 'Event deposits cannot exceed the recalculated group bill. Check refunds or payment entries first.']);
            }

            $allocations = empty($paymentChanges)
                ? $group->mapWithKeys(fn ($item) => [$item->id => round((float) $item->advance_payment)])->all()
                : $this->allocateConventionDeposits($group, $newGroupDeposits, $afterTotals);
            foreach ($group as $item) {
                $totals = $afterTotals[$item->id];
                $deposit = $allocations[$item->id] ?? 0;
                $remaining = max(0, round($totals['total_amount'] - $deposit));
                $item->advance_payment = $deposit;
                $item->total_amount = $totals['total_amount'];
                $item->vat_amount = $totals['vat_amount'];
                $item->remaining_payment = $remaining <= 5 ? 0 : $remaining;
                $item->payment_status = $item->remaining_payment <= 0 ? 'paid' : ($deposit > 0 ? 'partial' : 'pending');
                if (Schema::hasColumn('convention_bookings', 'vat_enabled')) {
                    $item->vat_enabled = $totals['vat_enabled'];
                }
                if (Schema::hasColumn('convention_bookings', 'updated_by_id')) {
                    $item->updated_by_id = $actorId;
                }
                $item->save();
            }

            ActivityLog::log('Super Admin reconciled convention booking finances', 'ConventionBooking', $target->id, [
                'group_booking_ids' => $groupIds,
                'reason' => trim($validated['reason']),
                'effective_date' => $validated['effective_date'],
                'discount_before' => ['type' => $oldDiscountType, 'value' => $oldDiscountValue, 'amount' => $targetFinancials['discount']],
                'discount_after' => ['type' => $discountType, 'value' => $discountValue, 'amount' => $discountAmount],
                'payment_changes' => $paymentChanges,
                'group_deposit_before' => $currentGroupDeposits,
                'group_deposit_after' => $newGroupDeposits,
                'financials_before' => $beforeFinancials->all(),
                'financials_after' => $afterTotals,
                'deposit_allocations_after' => $allocations,
            ]);
        });

        return redirect()->route('admin.booking-reconciliation.convention', [
            'q' => $conventionBooking->id,
            'convention_booking_id' => $conventionBooking->id,
        ])->with('success', 'Convention event bills and payment allocations were reconciled and audited.');
    }

    public function correctStay(Request $request, Booking $booking)
    {
        $this->authorizeSuperAdmin($request);
        $validated = $request->validate([
            'check_in_date' => 'required|date_format:Y-m-d',
            'check_out_date' => 'required|date_format:Y-m-d|after:check_in_date',
            'shift_dates' => 'nullable|array',
            'shift_dates.*' => 'required|date_format:Y-m-d',
            'shift_rates' => 'nullable|array',
            'shift_rates.*' => 'required|numeric|min:0|max:99999999.99',
            'active_room_rate' => 'nullable|numeric|min:0|max:99999999.99',
            'add_missing_shift' => 'nullable|boolean',
            'missing_from_room_id' => 'required_if:add_missing_shift,1|integer|exists:rooms,id',
            'missing_shift_date' => 'required_if:add_missing_shift,1|date_format:Y-m-d',
            'missing_previous_rate' => 'required_if:add_missing_shift,1|numeric|min:0|max:99999999.99',
            'reason' => 'required|string|min:8|max:1000',
            'confirm_reconcile' => 'accepted',
        ]);

        DB::transaction(function () use ($booking, $validated) {
            $locked = Booking::whereKey($booking->getKey())->lockForUpdate()->firstOrFail();
            $assignments = $locked->bookingRooms()->orderBy('id')->lockForUpdate()->get();
            $shifts = $locked->roomShifts()->reorder()->orderBy('id')->lockForUpdate()->get();
            if ($shifts->isNotEmpty() && $assignments->count() !== 1) {
                throw ValidationException::withMessages(['shift_dates' => 'This booking has multiple active rooms and historical shifts. Correcting its timeline needs a room-by-room shift assignment; no data was changed.']);
            }
            $addMissingShift = (bool) ($validated['add_missing_shift'] ?? false);
            if ($addMissingShift && ($shifts->isNotEmpty() || $assignments->count() !== 1)) {
                throw ValidationException::withMessages(['add_missing_shift' => 'A missing prior-room segment can only be added when there is one active room and no existing shift history.']);
            }

            $shiftDates = $validated['shift_dates'] ?? [];
            $shiftRates = $validated['shift_rates'] ?? [];
            $shiftIds = $shifts->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all();
            $submittedShiftIds = collect(array_keys($shiftDates))->map(fn ($id) => (string) $id)->sort()->values()->all();
            $submittedRateIds = collect(array_keys($shiftRates))->map(fn ($id) => (string) $id)->sort()->values()->all();
            if ($shiftIds !== $submittedShiftIds || $shiftIds !== $submittedRateIds) {
                throw ValidationException::withMessages(['shift_dates' => 'The room-shift history changed while the page was open. Reload before correcting it.']);
            }

            $newCheckIn = Carbon::createFromFormat('Y-m-d', $validated['check_in_date'])->startOfDay();
            $newCheckOut = Carbon::createFromFormat('Y-m-d', $validated['check_out_date'])->startOfDay();
            $cursor = $newCheckIn->copy();
            $segments = [];
            $shiftChanges = [];
            $expectedFromRoomId = null;

            foreach ($shifts as $shift) {
                $nextBoundary = Carbon::createFromFormat('Y-m-d', $shiftDates[$shift->id])->startOfDay();
                if ($nextBoundary->lt($cursor) || !$nextBoundary->lt($newCheckOut)) {
                    throw ValidationException::withMessages(['shift_dates' => 'Shift dates must remain in order, on or after check-in, and before check-out.']);
                }
                if (!$shift->from_room_id || !$shift->to_room_id) {
                    throw ValidationException::withMessages(['shift_dates' => 'A historical shift is missing a linked room. Repair its room link before changing this timeline.']);
                }
                if ($expectedFromRoomId !== null && (int) $shift->from_room_id !== $expectedFromRoomId) {
                    throw ValidationException::withMessages(['shift_dates' => 'The saved room-shift records do not form one continuous room chain. No changes were saved.']);
                }
                $expectedFromRoomId = (int) $shift->to_room_id;
                $rate = round((float) $shiftRates[$shift->id], 2);
                $nights = (int) $cursor->diffInDays($nextBoundary);
                $amount = round($nights * $rate, 2);
                $segments[] = [
                    'room_id' => (int) $shift->from_room_id,
                    'room_number' => $shift->from_room_number,
                    'start' => $cursor->toDateString(),
                    'end' => $nextBoundary->toDateString(),
                ];
                $shiftChanges[] = [
                    'id' => $shift->id,
                    'from_room_id' => $shift->from_room_id,
                    'to_room_id' => $shift->to_room_id,
                    'occupied_from_before' => $shift->occupied_from?->toDateString(),
                    'shift_date_before' => $shift->shift_date?->toDateString(),
                    'billed_amount_before' => (float) $shift->billed_amount,
                    'occupied_from_after' => $cursor->toDateString(),
                    'shift_date_after' => $nextBoundary->toDateString(),
                    'billed_nights_after' => $nights,
                    'billed_amount_after' => $amount,
                ];
                $shift->occupied_from = $cursor->toDateString();
                $shift->shift_date = $nextBoundary->toDateString();
                $shift->previous_rate = $rate;
                $shift->billed_nights = $nights;
                $shift->billed_amount = $amount;
                $shift->save();
                $cursor = $nextBoundary;
            }
            if ($shifts->isNotEmpty() && (int) $expectedFromRoomId !== (int) $assignments->first()->room_id) {
                throw ValidationException::withMessages(['shift_dates' => 'The last saved shift does not lead to the currently assigned room. No changes were saved.']);
            }

            if ($addMissingShift) {
                $active = $assignments->first();
                $previousRoom = Room::whereKey($validated['missing_from_room_id'])->lockForUpdate()->firstOrFail();
                if ((int) $previousRoom->id === (int) $active->room_id) {
                    throw ValidationException::withMessages(['missing_from_room_id' => 'The previous room must be different from the currently assigned room.']);
                }
                $boundary = Carbon::createFromFormat('Y-m-d', $validated['missing_shift_date'])->startOfDay();
                if ($boundary->lt($newCheckIn) || !$boundary->lt($newCheckOut)) {
                    throw ValidationException::withMessages(['missing_shift_date' => 'The prior-room shift date must be on/after check-in and before check-out.']);
                }
                $previousRate = round((float) $validated['missing_previous_rate'], 2);
                $nights = (int) $newCheckIn->diffInDays($boundary);
                $newRate = round((float) ($validated['active_room_rate'] ?? $active->price_per_night), 2);
                $newShift = $locked->roomShifts()->create([
                    'from_room_id' => $previousRoom->id,
                    'to_room_id' => $active->room_id,
                    'from_room_number' => $previousRoom->room_number,
                    'to_room_number' => $active->room?->room_number ?? (string) $active->room_id,
                    'occupied_from' => $newCheckIn->toDateString(),
                    'shift_date' => $boundary->toDateString(),
                    'previous_rate' => $previousRate,
                    'new_rate' => $newRate,
                    'billed_nights' => $nights,
                    'billed_amount' => round($nights * $previousRate, 2),
                    'reason' => trim($validated['reason']),
                    'shifted_by_id' => auth()->id(),
                ]);
                $shiftChanges[] = [
                    'id' => $newShift->id,
                    'added_missing_history' => true,
                    'from_room_id' => $previousRoom->id,
                    'to_room_id' => $active->room_id,
                    'occupied_from' => $newCheckIn->toDateString(),
                    'shift_date' => $boundary->toDateString(),
                    'previous_rate' => $previousRate,
                    'billed_nights' => $nights,
                    'billed_amount' => round($nights * $previousRate, 2),
                ];
                $segments[] = [
                    'room_id' => (int) $previousRoom->id,
                    'room_number' => $previousRoom->room_number,
                    'start' => $newCheckIn->toDateString(),
                    'end' => $boundary->toDateString(),
                ];
                $cursor = $boundary;
            }

            if ($shifts->isNotEmpty() || $addMissingShift) {
                for ($index = 0; $index < $shifts->count(); $index++) {
                    $shifts[$index]->new_rate = $index + 1 < $shifts->count()
                        ? (float) $shifts[$index + 1]->previous_rate
                        : round((float) ($validated['active_room_rate'] ?? $assignments->first()->price_per_night), 2);
                    $shifts[$index]->save();
                }
                $active = $assignments->first();
                $activeStart = $cursor->toDateString();
                $activeRate = round((float) ($validated['active_room_rate'] ?? $active->price_per_night), 2);
                $active->check_in_date = $activeStart;
                $active->check_out_date = $newCheckOut->toDateString();
                $active->price_per_night = $activeRate;
                $active->save();
                $segments[] = [
                    'room_id' => (int) $active->room_id,
                    'room_number' => $active->room?->room_number,
                    'start' => $activeStart,
                    'end' => $newCheckOut->toDateString(),
                ];
            } else {
                foreach ($assignments as $assignment) {
                    $assignment->check_in_date = $newCheckIn->toDateString();
                    $assignment->check_out_date = $newCheckOut->toDateString();
                    $assignment->save();
                    $segments[] = [
                        'room_id' => (int) $assignment->room_id,
                        'room_number' => null,
                        'start' => $newCheckIn->toDateString(),
                        'end' => $newCheckOut->toDateString(),
                    ];
                }
                if ($assignments->isEmpty() && $locked->room_id) {
                    $segments[] = [
                        'room_id' => (int) $locked->room_id,
                        'room_number' => $locked->room?->room_number,
                        'start' => $newCheckIn->toDateString(),
                        'end' => $newCheckOut->toDateString(),
                    ];
                }
            }

            $this->assertNoActiveRoomConflicts($locked, $segments);
            $oldDates = ['check_in_date' => $locked->check_in_date?->toDateString(), 'check_out_date' => $locked->check_out_date?->toDateString()];
            $locked->check_in_date = $newCheckIn->toDateString();
            $locked->check_out_date = $newCheckOut->toDateString();
            $locked->unsetRelation('bookingRooms')->unsetRelation('roomShifts')->unsetRelation('room');
            $locked->load(['room', 'bookingRooms.room.roomType', 'roomShifts.fromRoom.roomType', 'payments']);
            $locked->total_amount = $locked->getCalculatedTotal();
            $locked->vat_amount = $locked->getVatAmount();
            $locked->remaining_payment = $locked->getCalculatedRemaining();
            $locked->payment_status = $locked->getCalculatedPaymentStatus();
            $locked->updated_by_id = auth()->id();
            $locked->saveQuietly();

            if (Schema::hasTable('booking_financial_snapshots')) {
                $effectiveDate = $newCheckIn->toDateString();
                $dates = $locked->financialSnapshots()->reorder()->whereDate('effective_date', '>=', $effectiveDate)
                    ->orderBy('effective_date')->pluck('effective_date')
                    ->map(fn ($date) => $date instanceof \Carbon\CarbonInterface ? $date->toDateString() : (string) $date)
                    ->push($effectiveDate)->unique()->sort()->values();
                foreach ($dates as $date) {
                    $locked->recordFinancialSnapshot(substr('Stay/shift timeline reconciliation: '.trim($validated['reason']), 0, 255), $date);
                }
            }

            ActivityLog::log('Super Admin reconciled room stay and shift timeline', 'Booking', $locked->id, [
                'reason' => trim($validated['reason']),
                'dates_before' => $oldDates,
                'dates_after' => ['check_in_date' => $newCheckIn->toDateString(), 'check_out_date' => $newCheckOut->toDateString()],
                'shift_changes' => $shiftChanges,
                'room_segments' => $segments,
                'financials_after' => $locked->getFinancialBreakdown(),
            ]);
        });

        return redirect()->route('admin.booking-reconciliation.index', ['q' => $booking->id, 'booking_id' => $booking->id])
            ->with('success', 'Stay dates and retained room-shift billing history were reconciled, recalculated, and audited.');
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

    private function getConventionFinancials(ConventionBooking $booking): array
    {
        $hallRent = round((float) ($booking->hall_rent ?? 0));
        $foodCost = round((float) ($booking->food_cost ?? 0));
        $addonsCost = round((float) ($booking->addons_cost ?? 0));
        $subtotal = max(0, $hallRent + $foodCost + $addonsCost);
        $discount = min($subtotal, max(0, round((float) ($booking->discount ?? 0))));
        $vatEnabled = array_key_exists('vat_enabled', $booking->getAttributes())
            ? (bool) $booking->vat_enabled
            : ((float) ($booking->vat_amount ?? 0) > 0 && (float) ($booking->vat_percentage ?? 0) > 0);
        $vatPercentage = $vatEnabled ? max(0, (float) ($booking->vat_percentage ?? 0)) : 0.0;
        $vatAmount = round(max(0, $subtotal - $discount) * $vatPercentage / 100);
        $totalAmount = round(max(0, $subtotal - $discount) + $vatAmount);
        $deposit = max(0, round((float) ($booking->advance_payment ?? 0)));
        $remaining = max(0, round($totalAmount - $deposit));

        return [
            'hall_rent' => $hallRent,
            'food_cost' => $foodCost,
            'addons_cost' => $addonsCost,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'vat_enabled' => $vatEnabled,
            'vat_percentage' => $vatPercentage,
            'vat_amount' => $vatAmount,
            'total_amount' => $totalAmount,
            'advance_payment' => $deposit,
            'remaining_payment' => $remaining <= 5 ? 0 : $remaining,
        ];
    }

    private function allocateConventionDeposits($bookings, float $amount, array $financials): array
    {
        $allocations = $bookings->mapWithKeys(fn ($booking) => [$booking->id => 0.0])->all();
        $previousTotal = (float) $bookings->sum('advance_payment');
        $weights = [];
        foreach ($bookings as $booking) {
            $weights[$booking->id] = $previousTotal > 0
                ? max(0, (float) $booking->advance_payment)
                : max(0, (float) ($financials[$booking->id]['total_amount'] ?? 0));
        }

        $remaining = round($amount);
        $attempts = 0;
        while ($remaining > 0 && $attempts++ <= $bookings->count()) {
            $eligible = $bookings->filter(function ($booking) use ($allocations, $financials) {
                $cap = max(0, (float) ($financials[$booking->id]['total_amount'] ?? 0));
                return $allocations[$booking->id] < $cap;
            })->values();
            if ($eligible->isEmpty()) {
                break;
            }

            $weightTotal = (float) $eligible->sum(fn ($booking) => $weights[$booking->id] ?? 0);
            if ($weightTotal <= 0) {
                foreach ($eligible as $booking) {
                    $weights[$booking->id] = max(1, (float) ($financials[$booking->id]['total_amount'] ?? 0));
                }
                $weightTotal = (float) $eligible->sum(fn ($booking) => $weights[$booking->id]);
            }

            $toAllocate = $remaining;
            $allocatedThisRound = 0.0;
            foreach ($eligible as $index => $booking) {
                $share = $index === $eligible->count() - 1
                    ? $toAllocate - $allocatedThisRound
                    : floor($toAllocate * ($weights[$booking->id] / $weightTotal));
                $capacity = max(0, (float) ($financials[$booking->id]['total_amount'] ?? 0) - $allocations[$booking->id]);
                $applied = min($capacity, $share);
                $allocations[$booking->id] += $applied;
                $allocatedThisRound += $applied;
            }
            if ($allocatedThisRound <= 0) {
                break;
            }
            $remaining -= $allocatedThisRound;
        }

        if ($remaining > 0) {
            throw ValidationException::withMessages(['payment_amounts' => 'The corrected event deposit could not be safely allocated across the hall bookings.']);
        }

        return array_map(fn ($value) => round($value), $allocations);
    }

    private function assertNoActiveRoomConflicts(Booking $booking, array $segments): void
    {
        if (in_array($booking->status, ['cancelled', 'checked_out'], true)) {
            return;
        }

        foreach ($segments as $segment) {
            $roomId = (int) ($segment['room_id'] ?? 0);
            $start = (string) ($segment['start'] ?? '');
            $end = (string) ($segment['end'] ?? '');
            if (!$roomId || !$start || !$end || $start >= $end) {
                continue;
            }

            $assignmentConflict = DB::table('booking_rooms as br')
                ->join('bookings as b', 'b.id', '=', 'br.booking_id')
                ->where('br.room_id', $roomId)
                ->where('b.id', '!=', $booking->id)
                ->whereNotIn('b.status', ['cancelled', 'checked_out'])
                ->whereRaw('COALESCE(br.check_in_date, b.check_in_date) < ?', [$end])
                ->whereRaw('COALESCE(br.check_out_date, b.check_out_date) > ?', [$start])
                ->exists();
            $legacyConflict = DB::table('bookings as b')
                ->where('b.room_id', $roomId)
                ->where('b.id', '!=', $booking->id)
                ->whereNotIn('b.status', ['cancelled', 'checked_out'])
                ->whereDate('b.check_in_date', '<', $end)
                ->whereDate('b.check_out_date', '>', $start)
                ->whereNotExists(fn ($query) => $query->selectRaw('1')->from('booking_rooms as br')->whereColumn('br.booking_id', 'b.id'))
                ->exists();
            $shiftConflict = DB::table('booking_room_shifts as s')
                ->join('bookings as b', 'b.id', '=', 's.booking_id')
                ->where('s.from_room_id', $roomId)
                ->where('b.id', '!=', $booking->id)
                ->whereNotIn('b.status', ['cancelled', 'checked_out'])
                ->whereDate('s.occupied_from', '<', $end)
                ->whereDate('s.shift_date', '>', $start)
                ->exists();

            if ($assignmentConflict || $legacyConflict || $shiftConflict) {
                throw ValidationException::withMessages([
                    'check_in_date' => 'The corrected stay overlaps another active booking in room '.($segment['room_number'] ?? $roomId).'. No changes were saved.',
                ]);
            }
        }
    }

    private function authorizeSuperAdmin(Request $request): void
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);
    }
}
