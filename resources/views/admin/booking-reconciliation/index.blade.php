@extends('layouts.admin')

@section('title', 'Financial Reconciliation')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-10">
    <div class="rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-indigo-800 p-6 text-white shadow-lg">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-indigo-200">Super Admin only</p>
                <h1 class="mt-1 text-2xl font-bold md:text-3xl">Financial Reconciliation</h1>
                <p class="mt-2 max-w-3xl text-sm text-indigo-100">Correct room bills, receipts, stay dates, or existing shift history. Calculations and audit history are updated together.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.booking-reconciliation.convention') }}" class="rounded-xl border border-white/25 bg-white/10 px-4 py-3 text-sm font-semibold hover:bg-white/20"><i class="fas fa-building mr-2"></i>Convention bills</a>
                <span class="rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm"><i class="fas fa-shield-halved mr-2"></i>Reason + confirmation required</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
            <i class="fas fa-circle-check mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
            <p class="font-semibold"><i class="fas fa-triangle-exclamation mr-2"></i>Please review the correction.</p>
            <ul class="mt-2 list-inside list-disc text-sm">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.booking-reconciliation.index') }}" class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input name="q" value="{{ $search }}" placeholder="Booking ID, guest name, phone, or room number" class="w-full rounded-xl border border-slate-300 py-3 pl-11 pr-4 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
            </div>
            <button class="rounded-xl bg-indigo-700 px-6 py-3 font-semibold text-white hover:bg-indigo-800"><i class="fas fa-search mr-2"></i>Find booking</button>
        </form>
        <p class="mt-2 text-xs text-slate-500">Search room bookings here. Use Convention bills above for hall/event reconciliation. Stay-date and shift corrections preserve existing history.</p>
    </section>

    @if($search !== '')
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="font-bold text-slate-800">Search results</h2>
            </div>
            @if($bookings->count())
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr><th class="px-5 py-3">Booking</th><th class="px-5 py-3">Guest</th><th class="px-5 py-3">Room(s)</th><th class="px-5 py-3">Stay</th><th class="px-5 py-3">Status</th><th class="px-5 py-3"></th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($bookings as $row)
                                @php $roomNumbers = $row->bookingRooms->map(fn($assignment) => $assignment->room?->room_number)->filter()->unique()->implode(', ') ?: $row->room?->room_number; @endphp
                                <tr class="hover:bg-indigo-50/40">
                                    <td class="px-5 py-4 font-bold text-indigo-800">#{{ str_pad($row->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-5 py-4"><div class="font-semibold text-slate-800">{{ $row->customer_name }}</div><div class="text-xs text-slate-500">{{ $row->customer_phone }}</div></td>
                                    <td class="px-5 py-4">{{ $roomNumbers ?: '—' }}</td>
                                    <td class="px-5 py-4 whitespace-nowrap">{{ $row->check_in_date?->format('d M Y') }} – {{ $row->check_out_date?->format('d M Y') }}</td>
                                    <td class="px-5 py-4">{{ ucfirst(str_replace('_', ' ', $row->status)) }}</td>
                                    <td class="px-5 py-4 text-right"><a class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-xs font-bold text-white hover:bg-indigo-700" href="{{ route('admin.booking-reconciliation.index', ['q' => $search, 'booking_id' => $row->id]) }}">Review <i class="fas fa-arrow-right ml-2"></i></a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 px-5 py-4">{{ $bookings->links() }}</div>
            @else
                <p class="px-5 py-8 text-center text-slate-500">No matching room bookings found.</p>
            @endif
        </section>
    @endif

    @if($selectedBooking)
        @php
            $roomNumbers = $selectedBooking->bookingRooms->map(fn($assignment) => $assignment->room?->room_number)->filter()->unique()->implode(', ') ?: $selectedBooking->room?->room_number;
            $discountType = in_array($selectedBooking->discount_type, ['flat', 'percentage'], true) ? $selectedBooking->discount_type : 'none';
            $discountValue = $discountType === 'percentage' ? $selectedBooking->discount_percentage : ($discountType === 'flat' ? $selectedBooking->discount_amount : 0);
            $activeAssignment = $selectedBooking->bookingRooms->count() === 1 ? $selectedBooking->bookingRooms->first() : null;
            $stayRates = $selectedBooking->getRoomBreakdown()->pluck('price_per_night')->values()->all();
            $stayDiscountType = $selectedBooking->discount_type ?: 'none';
            $stayDiscountValue = $stayDiscountType === 'percentage' ? (float) $selectedBooking->discount_percentage : (float) $selectedBooking->discount_amount;
            $stayErrorFields = ['check_in_date', 'check_out_date', 'shift_dates', 'shift_rates', 'active_room_rate', 'missing_from_room_id', 'missing_shift_date', 'missing_previous_rate'];
            $openStayPanel = old('correction_scope') === 'stay' || collect($stayErrorFields)->contains(fn ($field) => $errors->has($field));
        @endphp

        <section class="rounded-2xl border border-indigo-100 bg-white p-5 shadow-sm md:p-6">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 pb-5">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-indigo-600">Selected room booking</p>
                    <h2 class="mt-1 text-2xl font-bold text-slate-900">#{{ str_pad($selectedBooking->id, 5, '0', STR_PAD_LEFT) }} · {{ $selectedBooking->customer_name }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $selectedBooking->customer_phone }} · Room {{ $roomNumbers ?: '—' }} · {{ $selectedBooking->check_in_date?->format('d M Y') }} to {{ $selectedBooking->check_out_date?->format('d M Y') }}</p>
                </div>
                <a href="{{ route('admin.bookings.show', $selectedBooking) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"><i class="fas fa-arrow-up-right-from-square mr-2"></i>Open booking</a>
            </div>

            <div class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-6">
                @foreach([
                    ['Room rent', $financials['room_rent'], 'slate'],
                    ['Discount', $financials['discount'], 'amber'],
                    ['Extra + VAT', $financials['extra_charges'] + $financials['vat'], 'violet'],
                    ['Bill total', $financials['grand_total'], 'indigo'],
                    ['Deposited', $financials['deposited'], 'emerald'],
                    ['Remaining', $financials['remaining'], 'rose'],
                ] as [$label, $value, $tone])
                    <div class="rounded-xl border border-{{ $tone }}-100 bg-{{ $tone }}-50 p-3">
                        <p class="text-xs text-slate-500">{{ $label }}</p>
                        <p class="mt-1 text-base font-extrabold text-slate-800">BDT {{ number_format((float)$value, 2) }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="rounded-2xl border border-slate-200 bg-white p-2 shadow-sm" role="tablist" aria-label="Reconciliation tools">
            <div class="grid gap-2 sm:grid-cols-2">
                <button type="button" id="reconciliation-finance-tab" role="tab" aria-controls="reconciliation-finance-panel" aria-selected="{{ $openStayPanel ? 'false' : 'true' }}" class="reconciliation-tab flex items-center gap-3 rounded-xl px-4 py-3 text-left transition {{ $openStayPanel ? 'text-slate-600 hover:bg-slate-50' : 'bg-indigo-50 text-indigo-800 shadow-sm ring-1 ring-indigo-100' }}">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700"><i class="fas fa-receipt"></i></span>
                    <span><span class="block font-bold">Bill &amp; receipts</span><span class="mt-0.5 block text-xs font-normal opacity-75">Correct bill amounts and existing payments</span></span>
                </button>
                <button type="button" id="reconciliation-stay-tab" role="tab" aria-controls="reconciliation-stay-panel" aria-selected="{{ $openStayPanel ? 'true' : 'false' }}" class="reconciliation-tab flex items-center gap-3 rounded-xl px-4 py-3 text-left transition {{ $openStayPanel ? 'bg-cyan-50 text-cyan-800 shadow-sm ring-1 ring-cyan-100' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-cyan-100 text-cyan-700"><i class="fas fa-clock-rotate-left"></i></span>
                    <span><span class="block font-bold">Stay &amp; room history</span><span class="mt-0.5 block text-xs font-normal opacity-75">Correct dates and preserved room shifts</span></span>
                </button>
            </div>
        </div>

        <div id="reconciliation-finance-panel" role="tabpanel" tabindex="0" aria-labelledby="reconciliation-finance-tab" {{ $openStayPanel ? 'hidden' : '' }}>
        <section class="rounded-2xl border border-indigo-100 bg-white p-5 shadow-sm md:p-6">
            <div class="mt-6 grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
                <div>
                    <h3 class="mb-3 font-bold text-slate-800"><i class="fas fa-receipt mr-2 text-indigo-600"></i>Existing payment ledger</h3>
                    @if($selectedBooking->payments->isNotEmpty())
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full min-w-[620px] text-left text-sm">
                                <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-3 py-3">Date / Type</th><th class="px-3 py-3">Method</th><th class="px-3 py-3">Recorded by</th><th class="px-3 py-3">Amount</th></tr></thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($selectedBooking->payments as $payment)
                                        <tr>
                                            <td class="px-3 py-3"><div>{{ $payment->payment_date?->format('d M Y') ?: $payment->created_at?->format('d M Y') }}</div><div class="text-xs uppercase text-slate-500">{{ $payment->type ?? 'payment' }}</div></td>
                                            <td class="px-3 py-3 uppercase">{{ $payment->method }}</td>
                                            <td class="px-3 py-3">{{ $payment->recordedBy?->name ?? '—' }}</td>
                                            <td class="px-3 py-3 font-semibold">BDT {{ number_format((float)$payment->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="rounded-xl border border-dashed border-slate-300 p-5 text-sm text-slate-500">No payment rows. Use the normal Add Payment flow to record a new receipt; reconciliation does not fabricate payments.</div>
                    @endif

                    <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                        <p class="font-bold"><i class="fas fa-lock mr-2"></i>Controlled correction</p>
                        <p class="mt-1">This changes existing receipt amounts only. Receipt IDs, payment methods, payment dates, and recorded-by history are retained. Room changes and room shifts must use the Room Shift workflow.</p>
                    </div>
                </div>

                <div>
                    <form method="POST" action="{{ route('admin.booking-reconciliation.room.update', $selectedBooking) }}" id="reconcileForm" class="space-y-5 rounded-2xl border border-slate-200 bg-slate-50 p-4 md:p-5">
                        @csrf
                        <input type="hidden" name="correction_scope" value="bill">
                        <div>
                            <h3 class="font-bold text-slate-900"><i class="fas fa-sliders mr-2 text-indigo-600"></i>Correct bill and receipts</h3>
                            <p class="mt-1 text-xs text-slate-500">Enter the discount and the amount actually received for each existing payment row.</p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="block text-sm font-semibold text-slate-700">Discount type
                                <select name="discount_type" id="discountType" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5">
                                    <option value="none" @selected(old('discount_type', $discountType) === 'none')>No discount</option>
                                    <option value="flat" @selected(old('discount_type', $discountType) === 'flat')>Fixed amount (BDT)</option>
                                    <option value="percentage" @selected(old('discount_type', $discountType) === 'percentage')>Percentage (%)</option>
                                </select>
                            </label>
                            <label class="block text-sm font-semibold text-slate-700"><span id="discountValueLabel">Discount value</span>
                                <input type="number" min="0" step="0.01" name="discount_value" id="discountValue" value="{{ old('discount_value', $discountValue) }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5">
                            </label>
                        </div>

                        @if($selectedBooking->payments->isNotEmpty())
                            <div>
                                <h4 class="mb-2 text-sm font-bold text-slate-700">Corrected receipt amounts</h4>
                                <div class="space-y-2">
                                    @foreach($selectedBooking->payments as $payment)
                                        <label class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white p-3 text-sm">
                                            <span><span class="font-semibold">{{ ucfirst($payment->type ?? 'payment') }}</span><span class="ml-2 text-xs text-slate-500">{{ $payment->payment_date?->format('d M Y') ?: $payment->created_at?->format('d M Y') }} · {{ strtoupper($payment->method) }} · #{{ $payment->id }}</span></span>
                                            <span class="flex items-center gap-2"><span class="text-xs text-slate-500">BDT</span><input type="number" min="0" step="0.01" max="99999999.99" name="payment_amounts[{{ $payment->id }}]" value="{{ old('payment_amounts.'.$payment->id, number_format((float)$payment->amount, 2, '.', '')) }}" data-payment-type="{{ $payment->type ?? 'payment' }}" class="payment-amount w-36 rounded-lg border border-slate-300 px-3 py-2 text-right font-semibold"></span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="block text-sm font-semibold text-slate-700">Bill correction effective from
                                <input type="date" name="effective_date" value="{{ old('effective_date', $selectedBooking->check_in_date?->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5" required>
                                <span class="mt-1 block text-xs font-normal text-slate-500">For past bill errors, usually choose the check-in date so later reports use the corrected bill.</span>
                            </label>
                            <label class="block text-sm font-semibold text-slate-700">Reason for correction <span class="text-rose-600">*</span>
                                <textarea name="reason" rows="3" minlength="8" maxlength="1000" required placeholder="Example: Actual cash received was BDT 1,800; BDT 200 discount was omitted." class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 font-normal">{{ old('reason') }}</textarea>
                            </label>
                        </div>

                        <div id="reconcilePreview" class="rounded-xl border border-indigo-200 bg-white p-4"
                             data-room-rent="{{ $financials['room_rent'] }}"
                             data-extra="{{ $financials['extra_charges'] }}"
                             data-vat-enabled="{{ $selectedBooking->vat_enabled ? 1 : 0 }}"
                             data-current-advance="{{ $selectedBooking->getAdvancePaymentTotal() }}">
                            <div class="mb-3 flex items-center justify-between"><h4 class="font-bold text-indigo-950">Live calculation preview</h4><span class="rounded-full bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700">Not saved yet</span></div>
                            <div class="grid grid-cols-2 gap-2 text-sm sm:grid-cols-4">
                                <div><p class="text-xs text-slate-500">Discount</p><p id="previewDiscount" class="font-bold">—</p></div>
                                <div><p class="text-xs text-slate-500">Bill total</p><p id="previewTotal" class="font-bold">—</p></div>
                                <div><p class="text-xs text-slate-500">Deposited</p><p id="previewDeposited" class="font-bold">—</p></div>
                                <div><p class="text-xs text-slate-500">Remaining</p><p id="previewRemaining" class="font-bold">—</p></div>
                            </div>
                        </div>

                        <label class="flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-900">
                            <input type="checkbox" name="confirm_reconcile" value="1" required class="mt-1 rounded border-rose-300 text-rose-700">
                            <span><strong>I verified this against the actual receipt/guest confirmation.</strong> Apply the recalculated bill and retain this correction in the audit history.</span>
                        </label>

                        <button type="submit" class="w-full rounded-xl bg-indigo-700 px-5 py-3 font-bold text-white shadow hover:bg-indigo-800">
                            <i class="fas fa-check-double mr-2"></i>Reconcile booking and save audit
                        </button>
                    </form>
                </div>
            </div>
        </section>
        </div>

        <div id="reconciliation-stay-panel" role="tabpanel" tabindex="0" aria-labelledby="reconciliation-stay-tab" {{ $openStayPanel ? '' : 'hidden' }}>
        <section class="rounded-2xl border border-cyan-100 bg-white p-5 shadow-sm md:p-6">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 pb-5">
                <div><p class="text-xs font-bold uppercase tracking-wider text-cyan-700">Stay and room timeline</p><h3 class="mt-1 text-xl font-bold text-slate-900">Correct dates or retain the previous-room bill</h3><p class="mt-1 max-w-3xl text-sm text-slate-500">This updates the booking dates and recalculates nights/rent without deleting existing shift rows, room IDs, or payment history.</p></div>
                <span class="rounded-full bg-cyan-50 px-3 py-1.5 text-xs font-bold text-cyan-800">Super Admin only · audited</span>
            </div>

            @if($selectedBooking->roomShifts->isNotEmpty() && !$activeAssignment)
                <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900"><strong>This booking has multiple active rooms plus shift history.</strong> The current schema cannot reliably pair each shift with its room line, so this tool blocks timeline edits rather than risking incorrect room bills.</div>
            @else
                <form method="POST" action="{{ route('admin.booking-reconciliation.stay.update', $selectedBooking) }}" class="mt-5 space-y-5">
                    @csrf
                    <input type="hidden" name="correction_scope" value="stay">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block text-sm font-semibold text-slate-700">Correct check-in date<input type="date" name="check_in_date" value="{{ old('check_in_date', $selectedBooking->check_in_date?->format('Y-m-d')) }}" required class="stay-checkin mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5"></label>
                        <label class="block text-sm font-semibold text-slate-700">Correct check-out date<input type="date" name="check_out_date" value="{{ old('check_out_date', $selectedBooking->check_out_date?->format('Y-m-d')) }}" required class="stay-checkout mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5"></label>
                    </div>

                    <div id="stayPreview" class="rounded-xl border border-cyan-200 bg-cyan-50/60 p-4" data-has-shifts="{{ $selectedBooking->roomShifts->isNotEmpty() ? 1 : 0 }}" data-unshifted-rates="{{ json_encode($stayRates) }}" data-active-room-rate="{{ $activeAssignment?->price_per_night ?? 0 }}" data-discount-type="{{ $stayDiscountType }}" data-discount-value="{{ $stayDiscountValue }}" data-extra="{{ $selectedBooking->extra_charges }}" data-vat-enabled="{{ $selectedBooking->vat_enabled ? 1 : 0 }}" data-deposited="{{ $financials['deposited'] }}">
                        <div class="mb-3 flex items-center justify-between"><h4 class="font-bold text-cyan-950"><i class="fas fa-calculator mr-2"></i>Live stay calculation preview</h4><span class="rounded-full bg-white px-2 py-1 text-xs font-semibold text-cyan-800">Not saved yet</span></div><div class="grid grid-cols-2 gap-2 text-sm sm:grid-cols-4"><div><p class="text-xs text-slate-500">Room rent</p><p id="stayPreviewRent" class="font-bold">—</p></div><div><p class="text-xs text-slate-500">Bill total</p><p id="stayPreviewTotal" class="font-bold">—</p></div><div><p class="text-xs text-slate-500">Deposited</p><p id="stayPreviewPaid" class="font-bold">—</p></div><div><p class="text-xs text-slate-500">Remaining</p><p id="stayPreviewDue" class="font-bold">—</p></div></div>
                    </div>

                    @if($selectedBooking->roomShifts->isNotEmpty())
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full min-w-[680px] text-left text-sm"><thead class="bg-cyan-50 text-xs uppercase text-cyan-900"><tr><th class="px-4 py-3">Retained room segment</th><th class="px-4 py-3">Shift / boundary date</th><th class="px-4 py-3">Previous room nightly rate</th></tr></thead><tbody class="divide-y divide-slate-100">
                                @foreach($selectedBooking->roomShifts->sortBy('id') as $shift)
                                    <tr><td class="px-4 py-3"><div class="font-semibold">Room {{ $shift->from_room_number }} → {{ $shift->to_room_number }}</div><div class="text-xs text-slate-500">History #{{ $shift->id }} · {{ $shift->billed_nights }} billed night(s), BDT {{ number_format((float)$shift->billed_amount, 2) }}</div></td><td class="px-4 py-3"><input type="date" name="shift_dates[{{ $shift->id }}]" value="{{ old('shift_dates.'.$shift->id, $shift->shift_date?->format('Y-m-d')) }}" required class="stay-shift-date rounded-lg border border-slate-300 px-3 py-2"></td><td class="px-4 py-3"><div class="flex items-center gap-2"><span class="text-xs text-slate-500">BDT</span><input type="number" min="0" step="0.01" name="shift_rates[{{ $shift->id }}]" value="{{ old('shift_rates.'.$shift->id, number_format((float)$shift->previous_rate, 2, '.', '')) }}" required class="stay-shift-rate w-36 rounded-lg border border-slate-300 px-3 py-2 text-right font-semibold"></div></td></tr>
                                @endforeach
                            </tbody></table>
                        </div>
                        @if($activeAssignment)
                            <label class="block max-w-sm text-sm font-semibold text-slate-700">Current room {{ $activeAssignment->room?->room_number }} nightly rate<input type="number" min="0" step="0.01" name="active_room_rate" value="{{ old('active_room_rate', number_format((float)$activeAssignment->price_per_night, 2, '.', '')) }}" class="stay-active-rate mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5"></label>
                        @endif
                        <p class="text-xs text-slate-500">Each corrected boundary becomes the next room segment’s start. The room sequence and original shift IDs stay unchanged; the previous-room bill is recalculated from corrected dates and rates.</p>
                    @else
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">This booking has no saved room-shift rows. Correcting dates updates all room assignments and recalculates the stay.</div>
                        @if($selectedBooking->bookingRooms->count() === 1)
                            <details class="rounded-xl border border-dashed border-cyan-300 bg-cyan-50/50 p-4">
                                <summary class="cursor-pointer font-semibold text-cyan-900">Add a missing previous-room segment (only if the shift really happened)</summary>
                                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                                    <label class="block text-sm font-semibold text-slate-700">Previous room<select name="missing_from_room_id" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5"><option value="">Select room</option>@foreach($roomOptions as $roomOption)@if((int)$roomOption->id !== (int)$activeAssignment?->room_id)<option value="{{ $roomOption->id }}" @selected((string)old('missing_from_room_id') === (string)$roomOption->id)>Room {{ $roomOption->room_number }}</option>@endif @endforeach</select></label>
                                    <label class="block text-sm font-semibold text-slate-700">Shift date<input type="date" name="missing_shift_date" value="{{ old('missing_shift_date') }}" class="stay-missing-date mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5"></label>
                                    <label class="block text-sm font-semibold text-slate-700">Previous room rent / night<input type="number" min="0" step="0.01" name="missing_previous_rate" value="{{ old('missing_previous_rate') }}" class="stay-missing-rate mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5"></label>
                                </div>
                                <label class="mt-3 flex items-center gap-2 text-sm font-semibold text-cyan-900"><input type="checkbox" id="addMissingShift" name="add_missing_shift" value="1" @checked(old('add_missing_shift')) class="rounded border-cyan-400">I verified this missing room shift and want to add it to the permanent history.</label>
                                <p class="mt-2 text-xs text-slate-600">The selected previous room will be linked to the currently assigned room. The system creates a new shift record with this superadmin, reason, dates, rates, and recalculated previous-room bill. It does not erase or overwrite existing history.</p>
                            </details>
                        @endif
                    @endif

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-xl border border-cyan-200 bg-cyan-50 p-4 text-sm text-cyan-950"><strong><i class="fas fa-calculator mr-2"></i>Automatic recalculation</strong><p class="mt-1">Room rent, VAT, grand total, due, payment status, and financial snapshots are recalculated from the saved room timeline.</p></div>
                        <label class="block text-sm font-semibold text-slate-700">Reason for date / shift correction <span class="text-rose-600">*</span><textarea name="reason" rows="3" minlength="8" maxlength="1000" required placeholder="Example: Actual stay was 11 Oct in room 301, then shifted to room 204 on 12 Oct." class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 font-normal">{{ old('reason') }}</textarea></label>
                    </div>
                    <label class="flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-900"><input type="checkbox" name="confirm_reconcile" value="1" required class="mt-1 rounded border-rose-300 text-rose-700"><span><strong>I checked the actual stay dates and room sequence.</strong> Recalculate the bill and save this correction in the audit history.</span></label>
                    <button type="submit" class="w-full rounded-xl bg-cyan-700 px-5 py-3 font-bold text-white shadow hover:bg-cyan-800"><i class="fas fa-clock-rotate-left mr-2"></i>Correct stay / shift history and recalculate</button>
                </form>
            @endif
        </section>
        </div>

        <script>
        (() => {
            const card = document.getElementById('stayPreview');
            if (!card) return;
            const checkIn = document.querySelector('.stay-checkin');
            const checkOut = document.querySelector('.stay-checkout');
            const dateDiff = (start, end) => {
                if (!start || !end) return 0;
                const a = Date.parse(start + 'T00:00:00Z');
                const b = Date.parse(end + 'T00:00:00Z');
                return Number.isFinite(a) && Number.isFinite(b) ? Math.max(0, (b - a) / 86400000) : 0;
            };
            const round2 = amount => Math.round((Number(amount) || 0) * 100) / 100;
            const money = amount => 'BDT ' + round2(amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            const update = () => {
                let rent = 0;
                if (card.dataset.hasShifts === '1') {
                    let start = checkIn.value;
                    document.querySelectorAll('.stay-shift-date').forEach((input, index) => {
                        rent += dateDiff(start, input.value) * (Number(document.querySelectorAll('.stay-shift-rate')[index]?.value) || 0);
                        start = input.value || start;
                    });
                    rent += dateDiff(start, checkOut.value) * (Number(document.querySelector('.stay-active-rate')?.value || card.dataset.activeRoomRate) || 0);
                } else if (document.getElementById('addMissingShift')?.checked) {
                    const shiftDate = document.querySelector('.stay-missing-date')?.value;
                    rent = dateDiff(checkIn.value, shiftDate) * (Number(document.querySelector('.stay-missing-rate')?.value) || 0)
                        + dateDiff(shiftDate, checkOut.value) * (Number(card.dataset.activeRoomRate) || 0);
                } else {
                    const rates = JSON.parse(card.dataset.unshiftedRates || '[]');
                    rent = rates.reduce((sum, rate) => sum + dateDiff(checkIn.value, checkOut.value) * (Number(rate) || 0), 0);
                }
                rent = round2(rent);
                const base = Math.max(0, rent);
                const type = card.dataset.discountType;
                const discountValue = Number(card.dataset.discountValue) || 0;
                const discount = type === 'percentage' ? Math.min(base, round2(base * discountValue / 100)) : (type === 'flat' ? Math.min(base, discountValue) : 0);
                const vat = Number(card.dataset.vatEnabled) ? round2(Math.max(0, base - discount) * 0.15) : 0;
                const total = round2(Math.max(0, base - discount) + (Number(card.dataset.extra) || 0) + vat);
                const deposited = Number(card.dataset.deposited) || 0;
                document.getElementById('stayPreviewRent').textContent = money(rent);
                document.getElementById('stayPreviewTotal').textContent = money(total);
                document.getElementById('stayPreviewPaid').textContent = money(deposited);
                document.getElementById('stayPreviewDue').textContent = money(Math.max(0, total - deposited));
            };
            document.querySelectorAll('.stay-checkin, .stay-checkout, .stay-shift-date, .stay-shift-rate, .stay-active-rate, .stay-missing-date, .stay-missing-rate, #addMissingShift').forEach(input => {
                input?.addEventListener('input', update);
                input?.addEventListener('change', update);
            });
            update();
        })();
        </script>

        @if($selectedBooking->financialSnapshots->isNotEmpty())
            <details class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <summary class="cursor-pointer list-none px-5 py-4 hover:bg-slate-50 [&::-webkit-details-marker]:hidden">
                    <span class="flex items-center justify-between gap-3">
                        <span><span class="block font-bold text-slate-800"><i class="fas fa-clock-rotate-left mr-2 text-indigo-600"></i>Recent bill history</span><span class="mt-1 block text-xs text-slate-500">Historical snapshots are retained when a correction is applied.</span></span>
                        <i class="fas fa-chevron-down text-xs text-slate-400"></i>
                    </span>
                </summary>
                <div class="border-t border-slate-100">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">Effective date</th><th class="px-4 py-3">Room rent</th><th class="px-4 py-3">Discount</th><th class="px-4 py-3">Bill total</th><th class="px-4 py-3">Reason / recorded by</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($selectedBooking->financialSnapshots->sortByDesc('id')->take(8) as $snapshot)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $snapshot->effective_date?->format('d M Y') }}</td>
                                    <td class="px-4 py-3">BDT {{ number_format((float)$snapshot->room_rent, 2) }}</td>
                                    <td class="px-4 py-3">BDT {{ number_format((float)$snapshot->discount, 2) }}</td>
                                    <td class="px-4 py-3 font-semibold">BDT {{ number_format((float)$snapshot->grand_total, 2) }}</td>
                                    <td class="px-4 py-3"><div>{{ $snapshot->reason ?: 'Earlier bill snapshot' }}</div><div class="mt-1 text-xs text-slate-500">{{ $snapshot->recordedBy?->name ?? 'System / legacy' }}</div></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                </div>
            </details>
        @endif

        <script>
        (() => {
            const tabs = [...document.querySelectorAll('.reconciliation-tab')];
            const panels = {
                'reconciliation-finance-tab': document.getElementById('reconciliation-finance-panel'),
                'reconciliation-stay-tab': document.getElementById('reconciliation-stay-panel'),
            };
            const activeStyles = {
                'reconciliation-finance-tab': ['bg-indigo-50', 'text-indigo-800', 'shadow-sm', 'ring-1', 'ring-indigo-100'],
                'reconciliation-stay-tab': ['bg-cyan-50', 'text-cyan-800', 'shadow-sm', 'ring-1', 'ring-cyan-100'],
            };
            const activate = selected => tabs.forEach(tab => {
                const active = tab === selected;
                tab.setAttribute('aria-selected', active ? 'true' : 'false');
                tab.tabIndex = active ? 0 : -1;
                tab.classList.remove(...activeStyles[tab.id], 'text-slate-600');
                tab.classList.add(...(active ? activeStyles[tab.id] : ['text-slate-600']));
                panels[tab.id].hidden = !active;
            });
            tabs.forEach((tab, index) => {
                tab.addEventListener('click', () => activate(tab));
                tab.addEventListener('keydown', event => {
                    if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
                    event.preventDefault();
                    const next = event.key === 'Home' ? 0 : event.key === 'End' ? tabs.length - 1 : (index + (event.key === 'ArrowRight' ? 1 : tabs.length - 1)) % tabs.length;
                    tabs[next].focus();
                    activate(tabs[next]);
                });
            });
            activate(tabs.find(tab => tab.getAttribute('aria-selected') === 'true') || tabs[0]);
        })();
        </script>

        <script>
        (() => {
            const preview = document.getElementById('reconcilePreview');
            const typeInput = document.getElementById('discountType');
            const valueInput = document.getElementById('discountValue');
            const label = document.getElementById('discountValueLabel');
            if (!preview || !typeInput || !valueInput) return;

            const money = value => 'BDT ' + (Number(value) || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            const update = () => {
                const base = Number(preview.dataset.roomRent) || 0;
                const extra = Number(preview.dataset.extra) || 0;
                const type = typeInput.value;
                const value = Math.round(Math.max(0, Number(valueInput.value) || 0) * 100) / 100;
                label.textContent = type === 'percentage' ? 'Discount percent' : (type === 'flat' ? 'Discount amount (BDT)' : 'Discount value (unused)');
                const rawDiscount = type === 'flat' ? Math.min(base, value) : (type === 'percentage' ? Math.min(base, base * Math.min(100, value) / 100) : 0);
                const discount = Math.round(rawDiscount * 100) / 100;
                const vat = Number(preview.dataset.vatEnabled) ? Math.round((base - discount) * 0.15 * 100) / 100 : 0;
                const total = Math.round((Math.max(0, base - discount) + extra + vat) * 100) / 100;
                const rows = [...document.querySelectorAll('.payment-amount')];
                const hasAdvanceRows = rows.some(row => row.dataset.paymentType === 'advance');
                let advance = hasAdvanceRows ? 0 : (Number(preview.dataset.currentAdvance) || 0);
                let paid = 0;
                let refunds = 0;
                rows.forEach(row => {
                    const amount = Math.round(Math.max(0, Number(row.value) || 0) * 100) / 100;
                    if (row.dataset.paymentType === 'advance') advance += amount;
                    else if (row.dataset.paymentType === 'refund') refunds += amount;
                    else if (row.dataset.paymentType === 'payment') paid += amount;
                });
                const deposited = Math.max(0, advance + paid - refunds);
                document.getElementById('previewDiscount').textContent = money(discount);
                document.getElementById('previewTotal').textContent = money(total);
                document.getElementById('previewDeposited').textContent = money(deposited);
                document.getElementById('previewRemaining').textContent = money(Math.max(0, total - deposited));
            };

            typeInput.addEventListener('change', update);
            valueInput.addEventListener('input', update);
            document.querySelectorAll('.payment-amount').forEach(input => input.addEventListener('input', update));
            update();
        })();
        </script>
    @endif
</div>
@endsection
