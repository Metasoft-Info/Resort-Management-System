@extends('layouts.admin')

@section('title', 'Convention Financial Reconciliation')

@section('content')
<div class="mx-auto max-w-7xl space-y-6 pb-10">
    <div class="rounded-2xl bg-gradient-to-r from-slate-950 via-violet-950 to-purple-800 p-6 text-white shadow-lg">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[.2em] text-purple-200">Super Admin only</p>
                <h1 class="mt-1 text-2xl font-bold md:text-3xl">Convention Reconciliation</h1>
                <p class="mt-2 max-w-3xl text-sm text-purple-100">Correct an event discount or an existing receipt. Group deposits are redistributed across its hall bills and every change is audited.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.booking-reconciliation.index') }}" class="rounded-xl border border-white/25 bg-white/10 px-4 py-3 text-sm font-semibold hover:bg-white/20"><i class="fas fa-bed mr-2"></i>Resort / room timeline</a>
                <span class="rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm"><i class="fas fa-shield-halved mr-2"></i>Reason + confirmation required</span>
            </div>
        </div>
    </div>

    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800"><i class="fas fa-circle-check mr-2"></i>{{ session('success') }}</div>@endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800"><p class="font-semibold"><i class="fas fa-triangle-exclamation mr-2"></i>Please review the correction.</p><ul class="mt-2 list-inside list-disc text-sm">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.booking-reconciliation.convention') }}" class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1"><i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i><input name="q" value="{{ $search }}" placeholder="Convention booking ID, guest, mobile, organization, or hall" class="w-full rounded-xl border border-slate-300 py-3 pl-11 pr-4 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-100"></div>
            <button class="rounded-xl bg-purple-700 px-6 py-3 font-semibold text-white hover:bg-purple-800"><i class="fas fa-search mr-2"></i>Find convention booking</button>
        </form>
    </section>

    @if($search !== '')
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4"><h2 class="font-bold text-slate-800">Convention search results</h2></div>
            @if($bookings->count())
                <div class="overflow-x-auto"><table class="w-full min-w-[800px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Booking</th><th class="px-5 py-3">Customer / organization</th><th class="px-5 py-3">Hall</th><th class="px-5 py-3">Event date / slot</th><th class="px-5 py-3">Status</th><th class="px-5 py-3"></th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                    @foreach($bookings as $row)
                        <tr class="hover:bg-purple-50/40"><td class="px-5 py-4 font-bold text-purple-800">#{{ str_pad($row->id, 5, '0', STR_PAD_LEFT) }}</td><td class="px-5 py-4"><div class="font-semibold text-slate-800">{{ $row->customer_name }}</div><div class="text-xs text-slate-500">{{ $row->customer_phone }} · {{ $row->organization_name ?: 'No organization' }}</div></td><td class="px-5 py-4">{{ $row->conventionHall?->name ?? '—' }}</td><td class="whitespace-nowrap px-5 py-4">{{ $row->event_date?->format('d M Y') }} · {{ ucfirst(str_replace('_', ' ', $row->time_slot)) }}</td><td class="px-5 py-4">{{ ucfirst($row->status) }}</td><td class="px-5 py-4 text-right"><a class="inline-flex items-center rounded-lg bg-purple-700 px-3 py-2 text-xs font-bold text-white hover:bg-purple-800" href="{{ route('admin.booking-reconciliation.convention', ['q' => $search, 'convention_booking_id' => $row->id]) }}">Review <i class="fas fa-arrow-right ml-2"></i></a></td></tr>
                    @endforeach
                    </tbody>
                </table></div>
                <div class="border-t border-slate-100 px-5 py-4">{{ $bookings->links() }}</div>
            @else
                <p class="px-5 py-8 text-center text-slate-500">No matching convention bookings found.</p>
            @endif
        </section>
    @endif

    @if($selectedBooking)
        @php
            $selectedFinancials = $groupFinancials->get($selectedBooking->id);
            $groupBill = (float) $groupFinancials->sum('total_amount');
            $groupDeposit = (float) $groupBookings->sum('advance_payment');
            $groupDue = max(0, $groupBill - $groupDeposit);
            $discountType = in_array($selectedBooking->discount_type, ['flat', 'percentage'], true) ? $selectedBooking->discount_type : 'none';
            $discountValue = $discountType === 'percentage' ? $selectedBooking->discount_value : ($discountType === 'flat' ? $selectedBooking->discount : 0);
            $paymentTotal = (float) $groupPayments->sum('amount');
        @endphp
        <section class="rounded-2xl border border-purple-100 bg-white p-5 shadow-sm md:p-6">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 pb-5">
                <div><p class="text-xs font-bold uppercase tracking-wider text-purple-700">Selected convention event</p><h2 class="mt-1 text-2xl font-bold text-slate-900">#{{ str_pad($selectedBooking->id, 5, '0', STR_PAD_LEFT) }} · {{ $selectedBooking->customer_name }}</h2><p class="mt-1 text-sm text-slate-500">{{ $selectedBooking->customer_phone }} · {{ $selectedBooking->organization_name ?: 'No organization' }} · {{ $selectedBooking->event_date?->format('d M Y') }} · {{ ucfirst(str_replace('_', ' ', $selectedBooking->time_slot)) }}</p></div>
                <a href="{{ route('admin.convention-bookings.show', $selectedBooking) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"><i class="fas fa-arrow-up-right-from-square mr-2"></i>Open booking</a>
            </div>

            <div class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-4">
                @foreach([['Event bill', $groupBill, 'indigo'], ['Discount on selected hall', $selectedFinancials['discount'], 'amber'], ['Group deposited', $groupDeposit, 'emerald'], ['Group remaining', $groupDue, 'rose']] as [$label, $value, $tone])
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><p class="text-xs text-slate-500">{{ $label }}</p><p class="mt-1 text-base font-extrabold text-slate-800">BDT {{ number_format((float)$value, 2) }}</p></div>
                @endforeach
            </div>

            <div class="mt-6 grid gap-6 xl:grid-cols-[.9fr_1.1fr]">
                <div class="space-y-5">
                    <div><h3 class="mb-3 font-bold text-slate-800"><i class="fas fa-building mr-2 text-purple-600"></i>Hall bills in this event group</h3><div class="overflow-x-auto rounded-xl border border-slate-200"><table class="w-full min-w-[480px] text-left text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-3 py-3">Hall / booking</th><th class="px-3 py-3">Bill</th><th class="px-3 py-3">Deposit</th><th class="px-3 py-3">Due</th></tr></thead><tbody class="divide-y divide-slate-100">
                        @foreach($groupBookings as $groupBooking)
                            @php $groupBillLine = $groupFinancials->get($groupBooking->id); @endphp
                            <tr class="{{ $groupBooking->id === $selectedBooking->id ? 'bg-purple-50' : '' }}"><td class="px-3 py-3"><div class="font-semibold">{{ $groupBooking->conventionHall?->name ?? 'Hall' }}</div><div class="text-xs text-slate-500">#{{ str_pad($groupBooking->id, 5, '0', STR_PAD_LEFT) }}{{ $groupBooking->id === $selectedBooking->id ? ' · selected' : '' }}</div></td><td class="px-3 py-3">BDT {{ number_format($groupBillLine['total_amount'], 2) }}</td><td class="px-3 py-3">BDT {{ number_format($groupBillLine['advance_payment'], 2) }}</td><td class="px-3 py-3 font-semibold">BDT {{ number_format($groupBillLine['remaining_payment'], 2) }}</td></tr>
                        @endforeach
                    </tbody></table></div></div>

                    <div><h3 class="mb-3 font-bold text-slate-800"><i class="fas fa-receipt mr-2 text-purple-600"></i>Group payment ledger</h3>
                        @if($groupPayments->isNotEmpty())
                            <div class="overflow-x-auto rounded-xl border border-slate-200"><table class="w-full min-w-[560px] text-left text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-3 py-3">Date / receipt</th><th class="px-3 py-3">Hall record</th><th class="px-3 py-3">Method</th><th class="px-3 py-3">Received by</th><th class="px-3 py-3">Amount</th></tr></thead><tbody class="divide-y divide-slate-100">
                                @foreach($groupPayments as $payment)
                                    <tr><td class="px-3 py-3">{{ $payment->payment_date?->format('d M Y') ?: $payment->created_at?->format('d M Y') }}<div class="text-xs text-slate-500">Receipt #{{ $payment->id }}</div></td><td class="px-3 py-3">{{ $groupBookings->firstWhere('id', $payment->convention_booking_id)?->conventionHall?->name ?? 'Hall' }}</td><td class="px-3 py-3 uppercase">{{ $payment->payment_method ?? $payment->method ?? '—' }}</td><td class="px-3 py-3">{{ $payment->receivedBy?->name ?? '—' }}</td><td class="px-3 py-3 font-semibold">BDT {{ number_format((float)$payment->amount, 2) }}</td></tr>
                                @endforeach
                            </tbody></table></div>
                        @else
                            <div class="rounded-xl border border-dashed border-slate-300 p-4 text-sm text-slate-500">No payment ledger rows exist. This tool will not create a receipt or invent a payment method.</div>
                        @endif
                    </div>

                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900"><p class="font-bold"><i class="fas fa-circle-info mr-2"></i>How group reconciliation works</p><p class="mt-1">This event's halls share one payment pool. Any corrected receipt total is applied to the group's deposited total, then redistributed by the existing hall allocations (capped at each hall bill). The selected hall's discount is corrected separately. Receipt IDs, dates, methods, and receiver history remain intact.</p></div>
                </div>

                <form method="POST" action="{{ route('admin.booking-reconciliation.convention.update', $selectedBooking) }}" id="conventionReconcileForm" class="space-y-5 rounded-2xl border border-slate-200 bg-slate-50 p-4 md:p-5">
                    @csrf
                    <div><h3 class="font-bold text-slate-900"><i class="fas fa-sliders mr-2 text-purple-600"></i>Correct selected hall bill and group receipts</h3><p class="mt-1 text-xs text-slate-500">Discount affects this hall booking. Receipt edits change the shared event-group deposit and hall allocation.</p></div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="block text-sm font-semibold text-slate-700">Discount type<select name="discount_type" id="conventionDiscountType" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5"><option value="none" @selected(old('discount_type', $discountType) === 'none')>No discount</option><option value="flat" @selected(old('discount_type', $discountType) === 'flat')>Fixed amount (BDT)</option><option value="percentage" @selected(old('discount_type', $discountType) === 'percentage')>Percentage (%)</option></select></label>
                        <label class="block text-sm font-semibold text-slate-700"><span id="conventionDiscountLabel">Discount value</span><input type="number" min="0" step="0.01" max="99999999" name="discount_value" id="conventionDiscountValue" value="{{ old('discount_value', $discountValue) }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5"></label>
                    </div>
                    @if($groupPayments->isNotEmpty())
                        <div><h4 class="mb-2 text-sm font-bold text-slate-700">Correct existing group receipt amounts</h4><div class="space-y-2">
                            @foreach($groupPayments as $payment)
                                <label class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white p-3 text-sm"><span><span class="font-semibold">{{ $groupBookings->firstWhere('id', $payment->convention_booking_id)?->conventionHall?->name ?? 'Hall' }}</span><span class="ml-2 text-xs text-slate-500">{{ $payment->payment_date?->format('d M Y') ?: $payment->created_at?->format('d M Y') }} · {{ strtoupper($payment->payment_method ?? $payment->method ?? '') }} · #{{ $payment->id }}</span></span><span class="flex items-center gap-2"><span class="text-xs text-slate-500">BDT</span><input type="number" min="0" step="1" max="99999999" name="payment_amounts[{{ $payment->id }}]" value="{{ old('payment_amounts.'.$payment->id, number_format((float)$payment->amount, 0, '.', '')) }}" class="convention-payment-amount w-36 rounded-lg border border-slate-300 px-3 py-2 text-right font-semibold"></span></label>
                            @endforeach
                        </div></div>
                    @endif
                    <input type="hidden" name="effective_date" value="{{ $selectedBooking->event_date?->format('Y-m-d') }}">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-xl border border-slate-200 bg-white p-3 text-sm"><p class="font-semibold text-slate-700">Correction date</p><p class="mt-1">{{ $selectedBooking->event_date?->format('d M Y') }} event date</p><p class="mt-1 text-xs text-slate-500">Stored in audit history; convention records do not currently have dated bill snapshots.</p></div>
                        <label class="block text-sm font-semibold text-slate-700">Reason for correction <span class="text-rose-600">*</span><textarea name="reason" rows="3" minlength="8" maxlength="1000" required placeholder="Example: Verified receipt is BDT 750 cash, not BDT 1,500." class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 font-normal">{{ old('reason') }}</textarea></label>
                    </div>
                    <div id="conventionPreview" class="rounded-xl border border-purple-200 bg-white p-4" data-subtotal="{{ $selectedFinancials['subtotal'] }}" data-current-discount="{{ $selectedFinancials['discount'] }}" data-current-selected-total="{{ $selectedFinancials['total_amount'] }}" data-vat-enabled="{{ $selectedFinancials['vat_enabled'] ? 1 : 0 }}" data-vat-percent="{{ $selectedFinancials['vat_percentage'] }}" data-group-bill="{{ $groupBill }}" data-group-deposit="{{ $groupDeposit }}" data-old-payment-total="{{ $paymentTotal }}">
                        <div class="mb-3 flex items-center justify-between"><h4 class="font-bold text-purple-950">Live group calculation preview</h4><span class="rounded-full bg-purple-50 px-2 py-1 text-xs font-semibold text-purple-700">Not saved yet</span></div><div class="grid grid-cols-2 gap-2 text-sm sm:grid-cols-4"><div><p class="text-xs text-slate-500">Selected discount</p><p id="conventionPreviewDiscount" class="font-bold">—</p></div><div><p class="text-xs text-slate-500">Group bill</p><p id="conventionPreviewBill" class="font-bold">—</p></div><div><p class="text-xs text-slate-500">Group deposited</p><p id="conventionPreviewDeposit" class="font-bold">—</p></div><div><p class="text-xs text-slate-500">Group remaining</p><p id="conventionPreviewDue" class="font-bold">—</p></div></div>
                    </div>
                    <label class="flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-900"><input type="checkbox" name="confirm_reconcile" value="1" required class="mt-1 rounded border-rose-300 text-rose-700"><span><strong>I verified the correction against the actual receipt/event records.</strong> Apply the discount, recalculate group payment allocation, and retain the audit history.</span></label>
                    <button type="submit" class="w-full rounded-xl bg-purple-700 px-5 py-3 font-bold text-white shadow hover:bg-purple-800"><i class="fas fa-check-double mr-2"></i>Reconcile event and save audit</button>
                </form>
            </div>
        </section>

        <script>
        (() => {
            const card = document.getElementById('conventionPreview');
            const type = document.getElementById('conventionDiscountType');
            const value = document.getElementById('conventionDiscountValue');
            const label = document.getElementById('conventionDiscountLabel');
            if (!card || !type || !value) return;
            const money = amount => 'BDT ' + Math.round(Number(amount) || 0).toLocaleString();
            const update = () => {
                const subtotal = Number(card.dataset.subtotal) || 0;
                const raw = Math.max(0, Number(value.value) || 0);
                const discount = type.value === 'percentage' ? Math.min(subtotal, Math.round(subtotal * Math.min(100, raw) / 100)) : (type.value === 'flat' ? Math.min(subtotal, Math.round(raw)) : 0);
                const selectedBill = Math.max(0, subtotal - discount) + (Number(card.dataset.vatEnabled) ? Math.round(Math.max(0, subtotal - discount) * (Number(card.dataset.vatPercent) || 0) / 100) : 0);
                const groupBill = Math.max(0, (Number(card.dataset.groupBill) || 0) - (Number(card.dataset.currentSelectedTotal) || 0) + selectedBill);
                let newPayments = 0;
                document.querySelectorAll('.convention-payment-amount').forEach(input => newPayments += Math.max(0, Math.round(Number(input.value) || 0)));
                const deposit = Math.max(0, (Number(card.dataset.groupDeposit) || 0) + newPayments - (Number(card.dataset.oldPaymentTotal) || 0));
                document.getElementById('conventionPreviewDiscount').textContent = money(discount);
                document.getElementById('conventionPreviewBill').textContent = money(groupBill);
                document.getElementById('conventionPreviewDeposit').textContent = money(deposit);
                document.getElementById('conventionPreviewDue').textContent = money(Math.max(0, groupBill - deposit));
                label.textContent = type.value === 'percentage' ? 'Discount percent' : (type.value === 'flat' ? 'Discount amount (BDT)' : 'Discount value (unused)');
            };
            type.addEventListener('change', update); value.addEventListener('input', update);
            document.querySelectorAll('.convention-payment-amount').forEach(input => input.addEventListener('input', update));
            update();
        })();
        </script>
    @endif
</div>
@endsection
