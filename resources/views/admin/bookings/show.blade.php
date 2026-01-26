@extends('layouts.admin')

@section('content')
<div class="print:p-0">
    @php
        $nights = \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date));
        $baseAmount = $booking->total_amount;
        $discountAmount = 0;
        
        if($booking->discount_type === 'percentage' && $booking->discount_percentage > 0) {
            $discountAmount = ($baseAmount * $booking->discount_percentage) / 100;
        } elseif($booking->discount_type === 'flat' && $booking->discount_amount > 0) {
            $discountAmount = $booking->discount_amount;
        }
        
        $afterDiscount = $baseAmount - $discountAmount;
        $extraCharges = $booking->extra_charges ?? 0;
        $vatAmount = ($booking->vat_enabled && $booking->vat_amount) ? $booking->vat_amount : 0;
        $grandTotal = $afterDiscount + $extraCharges + $vatAmount;
    @endphp

    <!-- Action Buttons (Screen Only) -->
    <div class="print:hidden mb-6">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Booking #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</h1>
                <p class="text-gray-600 mt-1">Complete booking information and management</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="window.print()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                    <i class="fas fa-print"></i>
                    <span>Print Invoice</span>
                </button>
                <button onclick="openTimeModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fas fa-clock"></i>
                    <span>Edit Time</span>
                </button>
                <button onclick="openPaymentModal()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition flex items-center gap-2">
                    <i class="fas fa-money-bill"></i>
                    <span>Add Payment</span>
                </button>
                <button onclick="openRefundModal()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition flex items-center gap-2" @if($booking->advance_payment <= 0) disabled @endif>
                    <i class="fas fa-undo"></i>
                    <span>Process Refund</span>
                </button>
                <button onclick="openVatModal()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                    <i class="fas fa-percentage"></i>
                    <span>{{ $booking->vat_enabled ? 'Disable' : 'Enable' }} VAT</span>
                </button>
                <button onclick="openExtraChargesModal()" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i>
                    <span>Extra Charges</span>
                </button>
                <button onclick="openGuestModal()" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition flex items-center gap-2">
                    <i class="fas fa-user-plus"></i>
                    <span>Add Guest</span>
                </button>
                <div class="relative">
                    <select onchange="updateStatus({{ $booking->id }}, this.value)" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition cursor-pointer appearance-none pr-10">
                        <option value="">Change Status</option>
                        <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="checked_in" {{ $booking->status === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                        <option value="checked_out" {{ $booking->status === 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                        <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-white pointer-events-none"></i>
                </div>
                <a href="{{ route('admin.bookings.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 print:block">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Customer Information -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-blue-600"></i>
                    Guest Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-semibold text-gray-600">Name:</span>
                        <p class="text-gray-900">{{ $booking->customer_name }}</p>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600">NID:</span>
                        <p class="text-gray-900">{{ $booking->customer_nid }}</p>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600">Phone:</span>
                        <p class="text-gray-900">{{ $booking->customer_phone }}</p>
                    </div>
                    @if($booking->customer_whatsapp)
                    <div>
                        <span class="font-semibold text-gray-600">WhatsApp:</span>
                        <p class="text-gray-900">{{ $booking->customer_whatsapp }}</p>
                    </div>
                    @endif
                    <div class="md:col-span-2">
                        <span class="font-semibold text-gray-600">Email:</span>
                        <p class="text-gray-900">{{ $booking->customer_email }}</p>
                    </div>
                    @if($booking->passport_number)
                    <div class="md:col-span-2">
                        <span class="font-semibold text-gray-600">Passport:</span>
                        <p class="text-gray-900">{{ $booking->passport_number }}</p>
                    </div>
                    @endif
                    @if($booking->customer_address)
                    <div class="md:col-span-2">
                        <span class="font-semibold text-gray-600">Address:</span>
                        <p class="text-gray-900">{{ $booking->customer_address }}</p>
                    </div>
                    @endif
                </div>

                @if($booking->reference_name || $booking->reference_phone)
                <div class="mt-4 pt-4 border-t">
                    <h3 class="text-md font-bold text-gray-700 mb-3">Reference Person</h3>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        @if($booking->reference_name)
                        <div>
                            <span class="font-semibold text-gray-600">Name:</span>
                            <p class="text-gray-900">{{ $booking->reference_name }}</p>
                        </div>
                        @endif
                        @if($booking->reference_phone)
                        <div>
                            <span class="font-semibold text-gray-600">Phone:</span>
                            <p class="text-gray-900">{{ $booking->reference_phone }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Booking Details -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-bed text-green-600"></i>
                    Booking Details
                </h2>
                
                <!-- Status Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <!-- Check-in Status -->
                    <div class="border-2 rounded-lg p-4 text-center
                        @if($booking->status === 'checked_in' || $booking->status === 'checked_out') 
                            border-green-500 bg-green-50
                        @else 
                            border-gray-300 bg-gray-50
                        @endif">
                        <i class="fas fa-sign-in-alt text-2xl mb-2 
                            @if($booking->status === 'checked_in' || $booking->status === 'checked_out') 
                                text-green-600 
                            @else 
                                text-gray-400
                            @endif"></i>
                        <p class="text-xs font-semibold text-gray-600 mb-1">Check-In Status</p>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                            @if($booking->status === 'checked_in' || $booking->status === 'checked_out') 
                                bg-green-500 text-white
                            @else 
                                bg-gray-400 text-white
                            @endif">
                            @if($booking->status === 'checked_in' || $booking->status === 'checked_out')
                                CHECKED IN
                            @else
                                NOT CHECKED IN
                            @endif
                        </span>
                    </div>
                    
                    <!-- Check-out Status -->
                    <div class="border-2 rounded-lg p-4 text-center
                        @if($booking->status === 'checked_out') 
                            border-blue-500 bg-blue-50
                        @else 
                            border-gray-300 bg-gray-50
                        @endif">
                        <i class="fas fa-sign-out-alt text-2xl mb-2
                            @if($booking->status === 'checked_out') 
                                text-blue-600
                            @else 
                                text-gray-400
                            @endif"></i>
                        <p class="text-xs font-semibold text-gray-600 mb-1">Check-Out Status</p>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                            @if($booking->status === 'checked_out') 
                                bg-blue-500 text-white
                            @else 
                                bg-gray-400 text-white
                            @endif">
                            @if($booking->status === 'checked_out')
                                CHECKED OUT
                            @else
                                NOT CHECKED OUT
                            @endif
                        </span>
                    </div>
                    
                    <!-- Payment Status -->
                    <div class="border-2 rounded-lg p-4 text-center
                        @if($booking->payment_status === 'paid') 
                            border-green-500 bg-green-50
                        @elseif($booking->payment_status === 'partial') 
                            border-yellow-500 bg-yellow-50
                        @else 
                            border-red-500 bg-red-50
                        @endif">
                        <i class="fas fa-money-bill-wave text-2xl mb-2
                            @if($booking->payment_status === 'paid') 
                                text-green-600
                            @elseif($booking->payment_status === 'partial') 
                                text-yellow-600
                            @else 
                                text-red-600
                            @endif"></i>
                        <p class="text-xs font-semibold text-gray-600 mb-1">Payment Status</p>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                            @if($booking->payment_status === 'paid') 
                                bg-green-500 text-white
                            @elseif($booking->payment_status === 'partial') 
                                bg-yellow-500 text-gray-900
                            @else 
                                bg-red-500 text-white
                            @endif">
                            {{ strtoupper($booking->payment_status) }}
                        </span>
                        @if($booking->remaining_payment > 0)
                        <p class="text-xs text-gray-600 mt-1">Due: ৳{{ number_format($booking->remaining_payment, 2) }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-semibold text-gray-600">Room:</span>
                        <p class="text-gray-900">{{ $booking->room->room_number }} - {{ $booking->room->roomType->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600">Total Guests:</span>
                        <p class="text-gray-900">{{ $booking->number_of_guests }}</p>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600">Check-In:</span>
                        <p class="text-gray-900">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }} 
                            @if($booking->check_in_time) at {{ $booking->check_in_time }} @endif
                        </p>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600">Check-Out:</span>
                        <p class="text-gray-900">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }} 
                            @if($booking->check_out_time) at {{ $booking->check_out_time }} @endif
                        </p>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600">Total Nights:</span>
                        <p class="text-gray-900">{{ $nights }}</p>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600">AC Preference:</span>
                        <p class="text-gray-900">{{ strtoupper($booking->ac_preference ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600">Status:</span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            @if($booking->status === 'confirmed') bg-blue-100 text-blue-800
                            @elseif($booking->status === 'checked_in') bg-green-100 text-green-800
                            @elseif($booking->status === 'checked_out') bg-gray-100 text-gray-800
                            @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ strtoupper(str_replace('_', ' ', $booking->status)) }}
                        </span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600">Created By:</span>
                        <p class="text-gray-900">{{ $booking->createdBy->name ?? 'N/A' }}</p>
                    </div>
                    @if($booking->notes)
                    <div class="md:col-span-2">
                        <span class="font-semibold text-gray-600">Notes:</span>
                        <p class="text-gray-900">{{ $booking->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Additional Guests -->
            @if($booking->additionalGuests && $booking->additionalGuests->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6 print:break-inside-avoid">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-users text-purple-600"></i>
                    Additional Guest Members
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($booking->additionalGuests as $index => $guest)
                    <div class="bg-gray-50 p-3 rounded-lg text-sm">
                        <p class="font-semibold text-gray-800">{{ $index + 2 }}. {{ $guest->name }}</p>
                        <p class="text-gray-600">NID: {{ $guest->nid }}</p>
                        <p class="text-gray-600">Phone: {{ $guest->phone }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Documents -->
            @if($booking->customer_photo || $booking->customer_nid_document || $booking->passport_document || $booking->visiting_card)
            <div class="bg-white rounded-xl shadow-lg p-6 print:hidden">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-file text-indigo-600"></i>
                    Uploaded Documents
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @if($booking->customer_photo)
                    <div class="bg-blue-50 p-3 rounded-lg text-center hover:bg-blue-100 transition">
                        <a href="{{ Storage::url($booking->customer_photo) }}" target="_blank" class="block">
                            <img src="{{ Storage::url($booking->customer_photo) }}" alt="Customer Photo" class="w-full h-24 object-cover rounded mb-2 border">
                            <p class="text-xs text-gray-700 font-semibold">Customer Photo</p>
                        </a>
                    </div>
                    @endif
                    @if($booking->customer_nid_document)
                    <div class="bg-green-50 p-3 rounded-lg text-center hover:bg-green-100 transition">
                        <a href="{{ Storage::url($booking->customer_nid_document) }}" target="_blank" class="block">
                            @if(Str::endsWith(strtolower($booking->customer_nid_document), ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
                                <img src="{{ Storage::url($booking->customer_nid_document) }}" alt="NID Document" class="w-full h-24 object-cover rounded mb-2 border">
                            @else
                                <div class="w-full h-24 flex items-center justify-center bg-green-100 rounded mb-2">
                                    <i class="fas fa-file-pdf text-green-600 text-3xl"></i>
                                </div>
                            @endif
                            <p class="text-xs text-gray-700 font-semibold">NID Document</p>
                        </a>
                    </div>
                    @endif
                    @if($booking->passport_document)
                    <div class="bg-purple-50 p-3 rounded-lg text-center hover:bg-purple-100 transition">
                        <a href="{{ Storage::url($booking->passport_document) }}" target="_blank" class="block">
                            @if(Str::endsWith(strtolower($booking->passport_document), ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
                                <img src="{{ Storage::url($booking->passport_document) }}" alt="Passport" class="w-full h-24 object-cover rounded mb-2 border">
                            @else
                                <div class="w-full h-24 flex items-center justify-center bg-purple-100 rounded mb-2">
                                    <i class="fas fa-file-pdf text-purple-600 text-3xl"></i>
                                </div>
                            @endif
                            <p class="text-xs text-gray-700 font-semibold">Passport</p>
                        </a>
                    </div>
                    @endif
                    @if($booking->visiting_card)
                    <div class="bg-orange-50 p-3 rounded-lg text-center hover:bg-orange-100 transition">
                        <a href="{{ Storage::url($booking->visiting_card) }}" target="_blank" class="block">
                            @if(Str::endsWith(strtolower($booking->visiting_card), ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
                                <img src="{{ Storage::url($booking->visiting_card) }}" alt="Visiting Card" class="w-full h-24 object-cover rounded mb-2 border">
                            @else
                                <div class="w-full h-24 flex items-center justify-center bg-orange-100 rounded mb-2">
                                    <i class="fas fa-file-pdf text-orange-600 text-3xl"></i>
                                </div>
                            @endif
                            <p class="text-xs text-gray-700 font-semibold">Visiting Card</p>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Payment History -->
            @if($booking->payments && $booking->payments->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-history text-yellow-600"></i>
                    Payment History
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left p-2 font-semibold">Date</th>
                                <th class="text-left p-2 font-semibold">Type</th>
                                <th class="text-left p-2 font-semibold">Method</th>
                                <th class="text-right p-2 font-semibold">Amount</th>
                                <th class="text-left p-2 font-semibold">Recorded By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($booking->payments as $payment)
                            <tr>
                                <td class="p-2">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-2">
                                    <span class="px-2 py-1 rounded text-xs font-semibold
                                        @if($payment->type === 'advance') bg-blue-100 text-blue-800
                                        @elseif($payment->type === 'payment') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($payment->type) }}
                                    </span>
                                </td>
                                <td class="p-2 uppercase">{{ $payment->method }}</td>
                                <td class="p-2 text-right font-semibold
                                    @if($payment->type === 'refund') text-red-600 @else text-green-600 @endif">
                                    @if($payment->type === 'refund') - @endif৳{{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="p-2">{{ $payment->recordedBy->name ?? 'System' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Payment Summary -->
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-br from-blue-600 to-blue-800 text-white rounded-xl shadow-lg p-6 sticky top-6">
                <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <i class="fas fa-calculator"></i>
                    Payment Summary
                </h2>
                
                <div class="space-y-3 text-sm">
                    <!-- Base Amount -->
                    <div class="flex justify-between pb-2 border-b border-blue-400">
                        <span>Base Amount:</span>
                        <span class="font-semibold">৳{{ number_format($baseAmount, 2) }}</span>
                    </div>

                    <!-- VAT -->
                    @if($vatAmount > 0)
                    <div class="flex justify-between text-blue-200">
                        <span>VAT (15%):</span>
                        <span class="font-semibold">৳{{ number_format($vatAmount, 2) }}</span>
                    </div>
                    @endif

                    <!-- Discount -->
                    @if($discountAmount > 0)
                    <div class="flex justify-between text-red-300">
                        <span>Discount 
                            @if($booking->discount_type === 'percentage') 
                                ({{ $booking->discount_percentage }}%)
                            @endif:
                        </span>
                        <span class="font-semibold">- ৳{{ number_format($discountAmount, 2) }}</span>
                    </div>
                    @endif

                    <!-- Extra Charges -->
                    @if($extraCharges > 0)
                    <div class="flex justify-between text-orange-300">
                        <div>
                            <span>Extra Charges:</span>
                            @if($booking->extra_charges_description)
                            <p class="text-xs text-blue-200 italic">{{ $booking->extra_charges_description }}</p>
                            @endif
                        </div>
                        <span class="font-semibold">৳{{ number_format($extraCharges, 2) }}</span>
                    </div>
                    @endif

                    <!-- Grand Total -->
                    <div class="bg-white text-gray-900 rounded-lg p-3 my-3">
                        <div class="flex justify-between items-center">
                            <span class="font-bold">Grand Total:</span>
                            <span class="text-2xl font-bold text-green-600">৳{{ number_format($grandTotal, 2) }}</span>
                        </div>
                    </div>

                    <!-- Advance Payment -->
                    <div class="bg-blue-700 rounded-lg p-3">
                        <div class="flex justify-between">
                            <span>Advance Payment:</span>
                            <span class="font-bold">৳{{ number_format($booking->advance_payment, 2) }}</span>
                        </div>
                    </div>

                    <!-- Remaining -->
                    <div class="bg-yellow-500 text-gray-900 rounded-lg p-3">
                        <div class="flex justify-between">
                            <span class="font-bold">Remaining:</span>
                            <span class="font-bold">৳{{ number_format($booking->remaining_payment, 2) }}</span>
                        </div>
                    </div>

                    <!-- Payment Method & Status -->
                    <div class="pt-3 border-t border-blue-400 space-y-2">
                        <div class="flex justify-between items-center">
                            <span>Payment Method:</span>
                            <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-semibold uppercase">
                                {{ $booking->payment_method }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Payment Status:</span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if($booking->payment_status === 'paid') bg-green-500 text-white
                                @elseif($booking->payment_status === 'partial') bg-yellow-500 text-gray-900
                                @else bg-red-500 text-white
                                @endif">
                                {{ strtoupper($booking->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Invoice (Hidden on screen) -->
    @include('admin.bookings.invoice-template')
</div>

<!-- Modals -->
<!-- Refund Modal -->
<div id="refundModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Process Refund</h3>
            <button onclick="closeRefundModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="refundForm" onsubmit="submitRefund(event)">
            <div class="space-y-4">
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3">
                    <p class="text-sm text-yellow-700">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Available for refund: ৳{{ number_format($booking->advance_payment, 2) }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Refund Amount (৳)</label>
                    <input type="number" step="0.01" name="amount" required max="{{ $booking->advance_payment }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Refund Reason</label>
                    <textarea name="reason" rows="3" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500" placeholder="Enter reason for refund..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                        Process Refund
                    </button>
                    <button type="button" onclick="closeRefundModal()" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- VAT Modal -->
<div id="vatModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">{{ $booking->vat_enabled ? 'Disable' : 'Enable' }} VAT</h3>
            <button onclick="closeVatModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="space-y-4">
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                <p class="text-sm text-blue-700 mb-2">
                    <i class="fas fa-info-circle mr-2"></i>
                    Current Status: <strong>{{ $booking->vat_enabled ? 'VAT Enabled (15%)' : 'VAT Disabled' }}</strong>
                </p>
                @if($booking->vat_enabled)
                    <p class="text-sm text-blue-600">Current VAT Amount: ৳{{ number_format($vatAmount, 2) }}</p>
                @else
                    <p class="text-sm text-blue-600">Enabling VAT will add 15% to the base amount</p>
                @endif
            </div>
            <p class="text-sm text-gray-600">
                {{ $booking->vat_enabled ? 'Disabling VAT will remove the 15% tax from the grand total and recalculate the remaining payment.' : 'Enabling VAT will add 15% tax to the base amount and recalculate the grand total.' }}
            </p>
            <div class="flex gap-3">
                <button onclick="submitVatToggle()" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                    {{ $booking->vat_enabled ? 'Disable VAT' : 'Enable VAT' }}
                </button>
                <button onclick="closeVatModal()" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Time Modal -->
<div id="timeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Edit Check-In/Out Time</h3>
            <button onclick="closeTimeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="timeForm" onsubmit="submitTimeUpdate(event)">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check-In Time</label>
                    <input type="time" name="check_in_time" value="{{ $booking->check_in_time }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check-Out Time</label>
                    <input type="time" name="check_out_time" value="{{ $booking->check_out_time }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Update Time
                    </button>
                    <button type="button" onclick="closeTimeModal()" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Add Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Record Payment</h3>
            <button onclick="closePaymentModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="paymentForm" onsubmit="submitPayment(event)">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (৳)</label>
                    <input type="number" step="0.01" name="amount" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                    <p class="text-xs text-gray-500 mt-1">Remaining: ৳{{ number_format($booking->remaining_payment, 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
                    <select name="method" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="mfs">Mobile Banking (MFS)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Note (Optional)</label>
                    <textarea name="note" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                        Record Payment
                    </button>
                    <button type="button" onclick="closePaymentModal()" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Add Extra Charges Modal -->
<div id="extraChargesModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Add Extra Charges</h3>
            <button onclick="closeExtraChargesModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="extraChargesForm" onsubmit="submitExtraCharges(event)">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (৳)</label>
                    <input type="number" step="0.01" name="amount" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea name="description" required rows="3" placeholder="e.g., Room service, Mini bar, Laundry" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700">
                        Add Charges
                    </button>
                    <button type="button" onclick="closeExtraChargesModal()" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Add Guest Modal -->
<div id="guestModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Add Additional Guest</h3>
            <button onclick="closeGuestModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="guestForm" onsubmit="submitGuest(event)">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Guest Name</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">NID Number</label>
                    <input type="text" name="nid" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                    <input type="text" name="phone" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700">
                        Add Guest
                    </button>
                    <button type="button" onclick="closeGuestModal()" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Modal functions
function openTimeModal() {
    document.getElementById('timeModal').classList.remove('hidden');
    document.getElementById('timeModal').classList.add('flex');
}

function closeTimeModal() {
    document.getElementById('timeModal').classList.add('hidden');
    document.getElementById('timeModal').classList.remove('flex');
}

function openPaymentModal() {
    document.getElementById('paymentModal').classList.remove('hidden');
    document.getElementById('paymentModal').classList.add('flex');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.getElementById('paymentModal').classList.remove('flex');
}

function openExtraChargesModal() {
    document.getElementById('extraChargesModal').classList.remove('hidden');
    document.getElementById('extraChargesModal').classList.add('flex');
}

function closeExtraChargesModal() {
    document.getElementById('extraChargesModal').classList.add('hidden');
    document.getElementById('extraChargesModal').classList.remove('flex');
}

function openGuestModal() {
    document.getElementById('guestModal').classList.remove('hidden');
    document.getElementById('guestModal').classList.add('flex');
}

function closeGuestModal() {
    document.getElementById('guestModal').classList.add('hidden');
    document.getElementById('guestModal').classList.remove('flex');
}

function openRefundModal() {
    document.getElementById('refundModal').classList.remove('hidden');
    document.getElementById('refundModal').classList.add('flex');
}

function closeRefundModal() {
    document.getElementById('refundModal').classList.add('hidden');
    document.getElementById('refundModal').classList.remove('flex');
}

function openVatModal() {
    document.getElementById('vatModal').classList.remove('hidden');
    document.getElementById('vatModal').classList.add('flex');
}

function closeVatModal() {
    document.getElementById('vatModal').classList.add('hidden');
    document.getElementById('vatModal').classList.remove('flex');
}

// Update status
async function updateStatus(bookingId, status) {
    if (!status) return;
    
    const statusLabels = {
        'pending': 'পেন্ডিং',
        'confirmed': 'কনফার্মড',
        'checked_in': 'চেক-ইন',
        'checked_out': 'চেক-আউট',
        'cancelled': 'ক্যান্সেলড'
    };
    const statusLabel = statusLabels[status] || status.replace('_', ' ').toUpperCase();
    
    showConfirmModal(`আপনি কি স্ট্যাটাস "${statusLabel}" তে পরিবর্তন করতে চান?`, async function() {
        try {
            const response = await fetch(`/admin/bookings/${bookingId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status })
            });

            if (response.ok) {
                showGlobalModal('success', 'স্ট্যাটাস আপডেট হয়েছে!');
                setTimeout(() => location.reload(), 1500);
            } else {
                showGlobalModal('error', 'স্ট্যাটাস আপডেট করতে সমস্যা হয়েছে!');
            }
        } catch (error) {
            console.error('Error:', error);
            showGlobalModal('error', 'স্ট্যাটাস আপডেট করতে সমস্যা হয়েছে!');
        }
    });
}

// Submit time update
async function submitTimeUpdate(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch(`/admin/bookings/{{ $booking->id }}/update-time`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                check_in_time: formData.get('check_in_time'),
                check_out_time: formData.get('check_out_time')
            })
        });

        if (response.ok) {
            showGlobalModal('success', 'সময় আপডেট হয়েছে!');
            setTimeout(() => location.reload(), 1500);
        } else {
            showGlobalModal('error', 'সময় আপডেট করতে সমস্যা হয়েছে!');
        }
    } catch (error) {
        console.error('Error:', error);
        showGlobalModal('error', 'সময় আপডেট করতে সমস্যা হয়েছে!');
    }
}

// Submit payment
async function submitPayment(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch(`/admin/bookings/{{ $booking->id }}/add-payment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                amount: parseFloat(formData.get('amount')),
                method: formData.get('method'),
                note: formData.get('note')
            })
        });

        if (response.ok) {
            showGlobalModal('success', 'পেমেন্ট রেকর্ড হয়েছে!');
            setTimeout(() => location.reload(), 1500);
        } else {
            const data = await response.json();
            showGlobalModal('error', data.message || 'পেমেন্ট রেকর্ড করতে সমস্যা হয়েছে!');
        }
    } catch (error) {
        console.error('Error:', error);
        showGlobalModal('error', 'পেমেন্ট রেকর্ড করতে সমস্যা হয়েছে!');
    }
}

// Submit extra charges
async function submitExtraCharges(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch(`/admin/bookings/{{ $booking->id }}/add-extra-charges`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                amount: parseFloat(formData.get('amount')),
                description: formData.get('description')
            })
        });

        if (response.ok) {
            showGlobalModal('success', 'অতিরিক্ত চার্জ যোগ হয়েছে!');
            setTimeout(() => location.reload(), 1500);
        } else {
            showGlobalModal('error', 'অতিরিক্ত চার্জ যোগ করতে সমস্যা হয়েছে!');
        }
    } catch (error) {
        console.error('Error:', error);
        showGlobalModal('error', 'অতিরিক্ত চার্জ যোগ করতে সমস্যা হয়েছে!');
    }
}

// Submit guest
async function submitGuest(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch(`/admin/bookings/{{ $booking->id }}/add-guest`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                name: formData.get('name'),
                nid: formData.get('nid'),
                phone: formData.get('phone')
            })
        });

        if (response.ok) {
            showGlobalModal('success', 'অতিথি যোগ হয়েছে!');
            setTimeout(() => location.reload(), 1500);
        } else {
            showGlobalModal('error', 'অতিথি যোগ করতে সমস্যা হয়েছে!');
        }
    } catch (error) {
        console.error('Error:', error);
        showGlobalModal('error', 'অতিথি যোগ করতে সমস্যা হয়েছে!');
    }
}

// Submit refund
async function submitRefund(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const amount = parseFloat(formData.get('amount'));
    const maxRefund = {{ $booking->advance_payment }};
    
    if (amount > maxRefund) {
        showGlobalModal('error', `রিফান্ড পরিমাণ ৳${maxRefund.toFixed(2)} এর বেশি হতে পারবে না!`);
        return;
    }
    
    showConfirmModal(`আপনি কি ৳${amount.toFixed(2)} রিফান্ড করতে চান?`, async function() {
        try {
            const response = await fetch(`/admin/bookings/{{ $booking->id }}/process-refund`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    amount: amount,
                    reason: formData.get('reason')
                })
            });

            if (response.ok) {
                showGlobalModal('success', 'রিফান্ড সফল হয়েছে!');
                setTimeout(() => location.reload(), 1500);
            } else {
                const data = await response.json();
                showGlobalModal('error', data.message || 'রিফান্ড করতে সমস্যা হয়েছে!');
            }
        } catch (error) {
            console.error('Error:', error);
            showGlobalModal('error', 'রিফান্ড করতে সমস্যা হয়েছে!');
        }
    });
}

// Submit VAT toggle
async function submitVatToggle() {
    const currentStatus = {{ $booking->vat_enabled ? 'true' : 'false' }};
    const action = currentStatus ? 'বন্ধ' : 'চালু';
    
    showConfirmModal(`আপনি কি ভ্যাট ${action} করতে চান?`, async function() {
        try {
            const response = await fetch(`/admin/bookings/{{ $booking->id }}/update-vat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    vat_enabled: !currentStatus
                })
            });

            if (response.ok) {
                showGlobalModal('success', `ভ্যাট ${action} হয়েছে!`);
                setTimeout(() => location.reload(), 1500);
            } else {
                showGlobalModal('error', 'ভ্যাট আপডেট করতে সমস্যা হয়েছে!');
            }
        } catch (error) {
            console.error('Error:', error);
            showGlobalModal('error', 'ভ্যাট আপডেট করতে সমস্যা হয়েছে!');
        }
    });
}
</script>

<style>
@media print {
    .print\\:hidden {
        display: none !important;
    }
    .print\\:block {
        display: block !important;
    }
}
</style>
@endsection
