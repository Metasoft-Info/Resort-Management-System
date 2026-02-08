<!-- Print Invoice Template for Convention Booking -->
<div id="convention-invoice-print-area" class="hidden print:block">
    <div class="bg-white p-4 text-xs">
        <!-- Header -->
        <div class="text-center border-b-2 border-primary-800 pb-4 mb-6">
            @if($resortInfo && $resortInfo->header_logo)
                <img src="{{ asset('storage/' . $resortInfo->header_logo) }}" alt="{{ $resortInfo->resort_name ?? 'Resort' }}" class="h-16 mx-auto mb-2">
            @else
                <h1 class="text-4xl font-bold text-primary-800 mb-2">{{ $resortInfo->resort_name ?? 'Tufan Resort' }}</h1>
            @endif
            <p class="text-gray-600 text-sm">🏛️ Convention Hall & Event Center</p>
            <p class="text-gray-500 text-sm mt-2">
                @if($resortInfo)
                    Phone: {{ $resortInfo->phone ?? 'N/A' }} | Email: {{ $resortInfo->email ?? 'N/A' }}
                    @if($resortInfo->address)<br>{{ $resortInfo->address }}@endif
                @else
                    Phone: +880 1234 567890 | Email: info@tufanresort.com
                @endif
            </p>
        </div>

        <!-- Invoice Info -->
        <div class="flex justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">CONVENTION INVOICE</h2>
                <p class="text-sm text-gray-600">Invoice #: CONV-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</p>
                <p class="text-sm text-gray-600">Date: {{ $booking->created_at->format('d/m/Y') }}</p>
            </div>
            <div class="text-right">
                <div class="inline-block px-4 py-2 rounded-lg font-bold text-sm
                    @if($booking->status === 'confirmed') bg-primary-100 text-primary-800
                    @elseif($booking->status === 'completed') bg-primary-100 text-primary-800
                    @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                    @else bg-yellow-100 text-yellow-800
                    @endif">
                    {{ strtoupper($booking->status) }}
                </div>
            </div>
        </div>

        <!-- Customer Details -->
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div class="border border-gray-300 rounded p-4">
                <h3 class="font-bold text-gray-800 mb-3 text-sm border-b pb-2">Customer Information</h3>
                <div class="space-y-2 text-sm">
                    <p><span class="font-semibold">Name:</span> {{ $booking->customer_name }}</p>
                    @if($booking->organization_name)
                    <p><span class="font-semibold">Organization:</span> {{ $booking->organization_name }}</p>
                    @endif
                    <p><span class="font-semibold">Phone:</span> {{ $booking->customer_phone }}</p>
                    @if($booking->customer_whatsapp)
                    <p><span class="font-semibold">WhatsApp:</span> {{ $booking->customer_whatsapp }}</p>
                    @endif
                    @if($booking->customer_email)
                    <p><span class="font-semibold">Email:</span> {{ $booking->customer_email }}</p>
                    @endif
                    @if($booking->customer_nid)
                    <p><span class="font-semibold">NID:</span> {{ $booking->customer_nid }}</p>
                    @endif
                    @if($booking->customer_address)
                    <p><span class="font-semibold">Address:</span> {{ $booking->customer_address }}</p>
                    @endif
                </div>
            </div>

            <div class="border border-gray-300 rounded p-4">
                <h3 class="font-bold text-gray-800 mb-3 text-sm border-b pb-2">Event Details</h3>
                <div class="space-y-2 text-sm">
                    <p><span class="font-semibold">Hall:</span> {{ $booking->conventionHall->name }}</p>
                    <p><span class="font-semibold">Event Date:</span> {{ \Carbon\Carbon::parse($booking->event_date)->format('d/m/Y') }}</p>
                    <p><span class="font-semibold">Time Slot:</span> 
                        @if($booking->time_slot == 'morning') Morning (8AM - 2PM)
                        @elseif($booking->time_slot == 'night') Night (6PM - 11PM)
                        @else Full Day (8AM - 11PM)
                        @endif
                    </p>
                    <p><span class="font-semibold">Event Type:</span> {{ $booking->event_type }}</p>
                    <p><span class="font-semibold">Number of Guests:</span> {{ $booking->number_of_guests }}</p>
                </div>
            </div>
        </div>

        <!-- Food Package & Addons -->
        @if($booking->foodPackage || $booking->selected_addons)
        <div class="border border-gray-300 rounded p-4 mb-6">
            <h3 class="font-bold text-gray-800 mb-3 text-sm border-b pb-2">Food & Addon Services</h3>
            
            @if($booking->foodPackage)
            <div class="mb-3">
                <p class="font-semibold">Food Package: {{ $booking->foodPackage->name }}</p>
                <p class="text-gray-600">Cost: ৳{{ number_format($booking->food_cost, 2) }}</p>
            </div>
            @endif

            @if($booking->selected_addons)
            <div>
                <p class="font-semibold mb-2">Addon Services:</p>
                @php
                    $addons = is_array($booking->selected_addons) ? $booking->selected_addons : json_decode($booking->selected_addons, true);
                    $quantities = is_array($booking->addon_quantities) ? $booking->addon_quantities : json_decode($booking->addon_quantities, true);
                @endphp
                @if($addons)
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-1">Service</th>
                                <th class="text-center py-1">Qty</th>
                                <th class="text-right py-1">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($addons as $addonId)
                                @php
                                    $addon = \App\Models\AddonService::find($addonId);
                                    $qty = $quantities[$addonId] ?? 1;
                                @endphp
                                @if($addon)
                                <tr class="border-b border-gray-100">
                                    <td class="py-1">{{ $addon->name }}</td>
                                    <td class="text-center py-1">{{ $qty }}</td>
                                    <td class="text-right py-1">৳{{ number_format($addon->price * $qty, 2) }}</td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            @endif
        </div>
        @endif

        <!-- Billing Details -->
        <div class="border border-gray-300 rounded p-4 mb-6">
            <h3 class="font-bold text-gray-800 mb-3 text-sm border-b pb-2">Billing Summary</h3>
            <table class="w-full text-sm">
                <tbody>
                    <tr class="border-b border-gray-100">
                        <td class="py-2">Hall Rent</td>
                        <td class="text-right py-2 font-semibold">৳{{ number_format($booking->hall_rent, 2) }}</td>
                    </tr>
                    @if($booking->food_cost > 0)
                    <tr class="border-b border-gray-100">
                        <td class="py-2">Food Cost</td>
                        <td class="text-right py-2 font-semibold">৳{{ number_format($booking->food_cost, 2) }}</td>
                    </tr>
                    @endif
                    @if($booking->addons_cost > 0)
                    <tr class="border-b border-gray-100">
                        <td class="py-2">Addon Services</td>
                        <td class="text-right py-2 font-semibold">৳{{ number_format($booking->addons_cost, 2) }}</td>
                    </tr>
                    @endif
                    @if($booking->discount > 0)
                    <tr class="border-b border-gray-100 text-red-600">
                        <td class="py-2">Discount</td>
                        <td class="text-right py-2 font-semibold">-৳{{ number_format($booking->discount, 2) }}</td>
                    </tr>
                    @endif
                    @if($booking->vat_amount > 0)
                    <tr class="border-b border-gray-100">
                        <td class="py-2">VAT ({{ $booking->vat_percentage }}%)</td>
                        <td class="text-right py-2 font-semibold">৳{{ number_format($booking->vat_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="bg-primary-50">
                        <td class="py-3 font-bold text-lg">Total Amount</td>
                        <td class="text-right py-3 font-bold text-lg text-primary-700">৳{{ number_format($booking->total_amount, 2) }}</td>
                    </tr>
                    <tr class="bg-green-50">
                        <td class="py-2 font-semibold text-primary-700">Advance Payment</td>
                        <td class="text-right py-2 font-semibold text-primary-700">৳{{ number_format($booking->advance_payment, 2) }}</td>
                    </tr>
                    <tr class="bg-red-50">
                        <td class="py-2 font-bold text-red-700">Remaining Payment</td>
                        <td class="text-right py-2 font-bold text-red-700">৳{{ number_format($booking->remaining_payment, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Payment History -->
        @if($booking->payments && $booking->payments->count() > 0)
        <div class="border border-gray-300 rounded p-4 mb-6">
            <h3 class="font-bold text-gray-800 mb-3 text-sm border-b pb-2">Payment History</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-1">Date</th>
                        <th class="text-left py-1">Method</th>
                        <th class="text-right py-1">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($booking->payments as $payment)
                    <tr class="border-b border-gray-100">
                        <td class="py-1">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
                        <td class="py-1">{{ ucfirst($payment->payment_method) }}</td>
                        <td class="text-right py-1 font-semibold">৳{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Signature -->
        <div class="mt-6">
            <div class="text-right">
                <div class="inline-block border-t border-gray-400 pt-2 px-8">
                    <p class="text-sm font-semibold">Authorized Signature</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4 pt-3 border-t border-gray-300">
            <p class="text-sm text-gray-700 font-medium">Thank you for choosing {{ $resortInfo->resort_name ?? 'our resort' }}!</p>
            <p class="text-xs text-gray-500 mt-2">{{ $resortInfo->footer_text ?? 'We look forward to serving you again.' }}</p>
        </div>

        <!-- Developer Credit -->
        <div class="text-center mt-2 pt-2 border-t border-gray-200">
            <p class="text-xs text-gray-400">Developed By <span class="font-semibold">Mir Javed Jeetu</span> | Contact: <span class="font-semibold">01811480222</span></p>
        </div>
    </div>
</div>

<style>
@media print {
    @page {
        size: A4;
        margin: 10mm;
    }
    body {
        font-size: 10px !important;
    }
    body * {
        visibility: hidden;
    }
    #convention-invoice-print-area, #convention-invoice-print-area * {
        visibility: visible;
    }
    #convention-invoice-print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        font-size: 10px !important;
    }
    #convention-invoice-print-area .p-4 {
        padding: 8px !important;
    }
    #convention-invoice-print-area .mb-6 {
        margin-bottom: 8px !important;
    }
    #convention-invoice-print-area .mb-4 {
        margin-bottom: 6px !important;
    }
    #convention-invoice-print-area .p-2 {
        padding: 4px !important;
    }
    #convention-invoice-print-area h1 {
        font-size: 24px !important;
    }
    #convention-invoice-print-area h2 {
        font-size: 16px !important;
    }
    #convention-invoice-print-area h3 {
        font-size: 12px !important;
    }
    .print\:block {
        display: block !important;
    }
    .print\:hidden {
        display: none !important;
    }
}
</style>
