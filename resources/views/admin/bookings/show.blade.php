@extends('layouts.admin')

@section('content')
<div class="print:p-0">
 @php
 $nights = \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date));
 $nights = max(1, $nights);
 $allRooms = $booking->getAllRooms();
 $bookingRooms = $booking->bookingRooms;
 
 // Calculate base amount from actual rooms (not stored total_amount)
 $baseAmount = 0;
 foreach($allRooms as $room) {
 $bookingRoom = $bookingRooms->where('room_id', $room->id)->first();
 $roomPrice = $bookingRoom ? $bookingRoom->price_per_night : ($room->roomType->price_per_night ?? $room->price_per_night ?? 0);
 $baseAmount += $roomPrice * $nights;
 }
 
 // If no rooms found, fallback to stored total_amount
 if ($baseAmount == 0) {
 $baseAmount = $booking->total_amount;
 }
 
 $discountAmount = 0;
 
 if($booking->discount_type === 'percentage' && $booking->discount_percentage > 0) {
 $discountAmount = ($baseAmount * $booking->discount_percentage) / 100;
 } elseif($booking->discount_type === 'flat' && $booking->discount_amount > 0) {
 $discountAmount = $booking->discount_amount;
 }
 
 $afterDiscount = $baseAmount - $discountAmount;
 $extraCharges = $booking->extra_charges ?? 0;
 $vatAmount = $booking->vat_enabled ? ($afterDiscount * 0.15) : 0;
 $grandTotal = $afterDiscount + $extraCharges + $vatAmount;
 $remainingPayment = $grandTotal - $booking->advance_payment;
 @endphp

 <!-- Action Buttons (Screen Only) -->
 <div class="print:hidden mb-6">
 <div class="flex flex-wrap justify-between items-center gap-3">
 <div>
 <h1 class="text-3xl font-bold text-gray-800">Booking #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</h1>
 <p class="text-gray-600 mt-1">Complete booking information and management</p>
 </div>
 <div class="flex flex-wrap gap-2">
 <button onclick="printReservationLetter()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
 <i class="fas fa-file-alt"></i>
 <span>Reservation Letter</span>
 </button>
 <button onclick="printInvoice()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
 <i class="fas fa-print"></i>
 <span>Print Invoice</span>
 </button>
 <button onclick="openTimeModal()" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition flex items-center gap-2">
 <i class="fas fa-clock"></i>
 <span>Edit Time</span>
 </button>
 <button onclick="openPaymentModal()" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition flex items-center gap-2">
 <i class="fas fa-money-bill"></i>
 <span>Add Payment</span>
 </button>
 <button onclick="openRefundModal()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition flex items-center gap-2" @if($booking->advance_payment <= 0) disabled @endif>
 <i class="fas fa-undo"></i>
 <span>Process Refund</span>
 </button>
 <button onclick="openVatModal()" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition flex items-center gap-2">
 <i class="fas fa-percentage"></i>
 <span>{{ $booking->vat_enabled ? 'Disable' : 'Enable' }} VAT</span>
 </button>
 <button onclick="openExtraChargesModal()" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition flex items-center gap-2">
 <i class="fas fa-plus-circle"></i>
 <span>Extra Charges</span>
 </button>
 <button onclick="openGuestModal()" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition flex items-center gap-2">
 <i class="fas fa-user-plus"></i>
 <span>Add Guest</span>
 </button>
 <a href="{{ route('admin.premium-booking.index') }}?booking_id={{ $booking->id }}&phone={{ $booking->customer_phone }}&name={{ urlencode($booking->customer_name) }}&nid={{ $booking->customer_nid }}&address={{ urlencode($booking->customer_address ?? '') }}&company={{ urlencode($booking->company_name ?? '') }}&checkin={{ $booking->check_in_date }}&checkout={{ $booking->check_out_date }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition flex items-center gap-2">
 <i class="fas fa-plus-circle"></i>
 <span>Add Room</span>
 </a>
 <a href="{{ route('admin.customers.show', urlencode($booking->customer_phone)) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
 <i class="fas fa-user"></i>
 <span>Customer Profile</span>
 </a>
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
 <i class="fas fa-user text-primary-600"></i>
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
 <i class="fas fa-bed text-primary-600"></i>
 Booking Details
 </h2>
 
 <!-- Status Overview Cards -->
 <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
 <!-- Check-in Status -->
 <div class="border-2 rounded-lg p-4 text-center
 @if($booking->status === 'checked_in' || $booking->status === 'checked_out') 
 border-primary-500 bg-green-50
 @else 
 border-gray-300 bg-gray-50
 @endif">
 <i class="fas fa-sign-in-alt text-2xl mb-2 
 @if($booking->status === 'checked_in' || $booking->status === 'checked_out') 
 text-primary-600 
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
 border-primary-500 bg-primary-50
 @else 
 border-gray-300 bg-gray-50
 @endif">
 <i class="fas fa-sign-out-alt text-2xl mb-2
 @if($booking->status === 'checked_out') 
 text-primary-600
 @else 
 text-gray-400
 @endif"></i>
 <p class="text-xs font-semibold text-gray-600 mb-1">Check-Out Status</p>
 <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
 @if($booking->status === 'checked_out') 
 bg-primary-500 text-white
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
 border-primary-500 bg-green-50
 @elseif($booking->payment_status === 'partial') 
 border-yellow-500 bg-yellow-50
 @else 
 border-red-500 bg-red-50
 @endif">
 <i class="fas fa-money-bill-wave text-2xl mb-2
 @if($booking->payment_status === 'paid') 
 text-primary-600
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
 @if($remainingPayment > 0)
 <p class="text-xs text-gray-600 mt-1">Due: {{ number_format($remainingPayment, 2) }}</p>
 @endif
 </div>
 </div>
 
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
 <div class="md:col-span-2">
 <span class="font-semibold text-gray-600">Room(s):</span>
 @php $allRooms = $booking->getAllRooms(); @endphp
 @if($allRooms->count() > 1)
 <div class="flex flex-wrap gap-2 mt-1">
 @foreach($allRooms as $room)
 <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
 {{ $room->room_number }} - {{ $room->roomType->name ?? 'N/A' }}
 </span>
 @endforeach
 </div>
 @elseif($allRooms->count() == 1)
 <p class="text-gray-900">{{ $allRooms->first()->room_number }} - {{ $allRooms->first()->roomType->name ?? 'N/A' }}</p>
 @else
 <p class="text-gray-500">No room assigned</p>
 @endif
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
 @if($booking->status === 'confirmed') bg-primary-100 text-primary-800
 @elseif($booking->status === 'checked_in') bg-primary-100 text-primary-800
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
 <i class="fas fa-users text-primary-600"></i>
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
 <i class="fas fa-file text-primary-600"></i>
 Uploaded Documents
 </h2>
 <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
 @if($booking->customer_photo)
 <div class="bg-primary-50 p-3 rounded-lg text-center hover:bg-primary-100 transition">
 <a href="{{ Storage::url($booking->customer_photo) }}" target="_blank" class="block">
 <img src="{{ Storage::url($booking->customer_photo) }}" alt="Customer Photo" class="w-full h-24 object-cover rounded mb-2 border">
 <p class="text-xs text-gray-700 font-semibold">Customer Photo</p>
 </a>
 </div>
 @endif
 @if($booking->customer_nid_document)
 <div class="bg-green-50 p-3 rounded-lg text-center hover:bg-primary-100 transition">
 <a href="{{ Storage::url($booking->customer_nid_document) }}" target="_blank" class="block">
 @if(Str::endsWith(strtolower($booking->customer_nid_document), ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
 <img src="{{ Storage::url($booking->customer_nid_document) }}" alt="NID Document" class="w-full h-24 object-cover rounded mb-2 border">
 @else
 <div class="w-full h-24 flex items-center justify-center bg-primary-100 rounded mb-2">
 <i class="fas fa-file-pdf text-primary-600 text-3xl"></i>
 </div>
 @endif
 <p class="text-xs text-gray-700 font-semibold">NID Document</p>
 </a>
 </div>
 @endif
 @if($booking->passport_document)
 <div class="bg-primary-50 p-3 rounded-lg text-center hover:bg-primary-100 transition">
 <a href="{{ Storage::url($booking->passport_document) }}" target="_blank" class="block">
 @if(Str::endsWith(strtolower($booking->passport_document), ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
 <img src="{{ Storage::url($booking->passport_document) }}" alt="Passport" class="w-full h-24 object-cover rounded mb-2 border">
 @else
 <div class="w-full h-24 flex items-center justify-center bg-primary-100 rounded mb-2">
 <i class="fas fa-file-pdf text-primary-600 text-3xl"></i>
 </div>
 @endif
 <p class="text-xs text-gray-700 font-semibold">Passport</p>
 </a>
 </div>
 @endif
 @if($booking->visiting_card)
 <div class="bg-primary-50 p-3 rounded-lg text-center hover:bg-primary-100 transition">
 <a href="{{ Storage::url($booking->visiting_card) }}" target="_blank" class="block">
 @if(Str::endsWith(strtolower($booking->visiting_card), ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
 <img src="{{ Storage::url($booking->visiting_card) }}" alt="Visiting Card" class="w-full h-24 object-cover rounded mb-2 border">
 @else
 <div class="w-full h-24 flex items-center justify-center bg-primary-100 rounded mb-2">
 <i class="fas fa-file-pdf text-primary-600 text-3xl"></i>
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
 @if($payment->type === 'advance') bg-primary-100 text-primary-800
 @elseif($payment->type === 'payment') bg-primary-100 text-primary-800
 @else bg-red-100 text-red-800
 @endif">
 {{ ucfirst($payment->type) }}
 </span>
 </td>
 <td class="p-2 uppercase">{{ $payment->method }}</td>
 <td class="p-2 text-right font-semibold
 @if($payment->type === 'refund') text-red-600 @else text-primary-600 @endif">
 @if($payment->type === 'refund') - @endif{{ number_format($payment->amount, 2) }}
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
 <div class="bg-gradient-to-br from-primary-600 to-primary-800 text-white rounded-xl shadow-lg p-6 sticky top-6">
 <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
 <i class="fas fa-calculator"></i>
 Payment Summary
 </h2>
 
 <div class="space-y-3 text-sm">
 <!-- Base Amount -->
 <div class="flex justify-between pb-2 border-b border-blue-400">
 <span>Base Amount:</span>
 <span class="font-semibold">{{ number_format($baseAmount, 2) }}</span>
 </div>

 <!-- VAT -->
 @if($vatAmount > 0)
 <div class="flex justify-between text-primary-200">
 <span>VAT (15%):</span>
 <span class="font-semibold">{{ number_format($vatAmount, 2) }}</span>
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
 <span class="font-semibold">- {{ number_format($discountAmount, 2) }}</span>
 </div>
 @endif

 <!-- Extra Charges -->
 @if($extraCharges > 0)
 <div class="bg-primary-600 rounded-lg p-3 my-2">
 <div class="flex justify-between items-center">
 <span class="font-semibold">Extra Charges:</span>
 <span class="font-bold text-lg">+ {{ number_format($extraCharges, 2) }}</span>
 </div>
 @if($booking->extra_charges_description)
 <div class="mt-2 pt-2 border-t border-primary-400">
 <p class="text-xs text-primary-100"><i class="fas fa-list mr-1"></i> {{ $booking->extra_charges_description }}</p>
 </div>
 @endif
 </div>
 @else
 <div class="text-center text-primary-200 text-sm py-2">
 <i class="fas fa-info-circle mr-1"></i> No extra charges
 </div>
 @endif

 <!-- Grand Total -->
 <div class="bg-white text-gray-900 rounded-lg p-3 my-3">
 <div class="flex justify-between items-center">
 <span class="font-bold">Grand Total:</span>
 <span class="text-2xl font-bold text-primary-600">{{ number_format($grandTotal, 2) }}</span>
 </div>
 </div>

 <!-- Advance Payment -->
 <div class="bg-primary-700 rounded-lg p-3">
 <div class="flex justify-between">
 <span>Advance Payment:</span>
 <span class="font-bold">{{ number_format($booking->advance_payment, 2) }}</span>
 </div>
 </div>

 <!-- Remaining -->
 <div class="bg-yellow-500 text-gray-900 rounded-lg p-3">
 <div class="flex justify-between">
 <span class="font-bold">Remaining:</span>
 <span class="font-bold">{{ number_format($remainingPayment, 2) }}</span>
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
 
 <!-- Print Reservation Letter (Hidden on screen) -->
 @include('admin.bookings.reservation-letter-template')
</div>

<!-- Modals -->
<!-- Refund Modal -->
<div id="refundModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
 <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md my-8 mx-4 max-h-[90vh] overflow-y-auto">
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
 Available for refund: {{ number_format($booking->advance_payment, 2) }}
 </p>
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Refund Amount ()</label>
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
<div id="vatModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
 <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md my-8 mx-4 max-h-[90vh] overflow-y-auto">
 <div class="flex justify-between items-center mb-4">
 <h3 class="text-xl font-bold text-gray-800">{{ $booking->vat_enabled ? 'Disable' : 'Enable' }} VAT</h3>
 <button onclick="closeVatModal()" class="text-gray-500 hover:text-gray-700">
 <i class="fas fa-times text-xl"></i>
 </button>
 </div>
 <div class="space-y-4">
 <div class="bg-primary-50 border-l-4 border-primary-500 p-4">
 <p class="text-sm text-primary-700 mb-2">
 <i class="fas fa-info-circle mr-2"></i>
 Current Status: <strong>{{ $booking->vat_enabled ? 'VAT Enabled (15%)' : 'VAT Disabled' }}</strong>
 </p>
 @if($booking->vat_enabled)
 <p class="text-sm text-primary-600">Current VAT Amount: {{ number_format($vatAmount, 2) }}</p>
 @else
 <p class="text-sm text-primary-600">Enabling VAT will add 15% to the base amount</p>
 @endif
 </div>
 <p class="text-sm text-gray-600">
 {{ $booking->vat_enabled ? 'Disabling VAT will remove the 15% tax from the grand total and recalculate the remaining payment.' : 'Enabling VAT will add 15% tax to the base amount and recalculate the grand total.' }}
 </p>
 <div class="flex gap-3">
 <button onclick="submitVatToggle()" class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">
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
<div id="timeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
 <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md my-8 mx-4 max-h-[90vh] overflow-y-auto">
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
 <input type="time" name="check_in_time" value="{{ $booking->check_in_time }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Check-Out Time</label>
 <input type="time" name="check_out_time" value="{{ $booking->check_out_time }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div class="flex gap-3">
 <button type="submit" class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">
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
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
 <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-lg my-8 mx-4 max-h-[90vh] overflow-y-auto">
 <div class="flex justify-between items-center mb-4">
 <h3 class="text-xl font-bold text-gray-800">Record Payment</h3>
 <button onclick="closePaymentModal()" class="text-gray-500 hover:text-gray-700">
 <i class="fas fa-times text-xl"></i>
 </button>
 </div>
 <form id="paymentForm" onsubmit="submitPayment(event)">
 <div class="space-y-4">
 <!-- Remaining Balance Display -->
 <div class="bg-yellow-100 border border-yellow-400 rounded-lg p-3">
 <div class="flex justify-between items-center">
 <span class="font-semibold text-gray-700">Remaining:</span>
 <span class="text-xl font-bold text-yellow-700" id="currentRemaining">{{ number_format($remainingPayment, 2) }}</span>
 </div>
 <div id="afterPaymentCalc" class="hidden mt-2 pt-2 border-t border-yellow-300">
 <div class="flex justify-between text-sm">
 <span>Remaining after payment/discount:</span>
 <span class="font-bold text-primary-600" id="afterPaymentRemaining">0.00</span>
 </div>
 </div>
 <div id="paymentError" class="hidden mt-2 text-red-600 text-sm font-semibold"></div>
 </div>

 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Amount () <span class="text-xs text-gray-400">(Enter 0 for discount)</span></label>
 <input type="number" step="0.01" name="amount" id="payment_amount" value="{{ number_format($remainingPayment, 2, '.', '') }}" min="0" max="{{ $remainingPayment }}" oninput="calculatePaymentPreview()" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
 <select name="method" id="payment_modal_method" required onchange="togglePaymentModalFields()" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
 <option value="cash">Cash</option>
 <option value="bkash">bKash</option>
 <option value="card">Card</option>
 </select>
 </div>
 <div id="payment_modal_bkash" class="hidden">
 <label class="block text-sm font-semibold text-gray-700 mb-2">bKash Number</label>
 <input type="text" name="bkash_number" placeholder="01XXXXXXXXX" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div id="payment_modal_bank" class="hidden">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Bank Name</label>
 <select name="bank_name" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
 <option value="">Select Bank</option>
 <option value="Pubali Bank">Pubali Bank</option>
 <option value="City Bank">City Bank</option>
 <option value="Dutch Bangla Bank">Dutch Bangla Bank</option>
 </select>
 </div>
 <div class="border-t pt-4">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Discount</label>
 <select name="discount_type" id="payment_discount_type" onchange="toggleDiscountFields()" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
 <option value="none">No Discount</option>
 <option value="flat">Fixed Amount</option>
 <option value="percentage">Percentage (%)</option>
 </select>
 </div>
 <div id="discount_flat_div" class="hidden">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Amount ()</label>
 <input type="number" step="0.01" name="discount_amount" id="discount_amount_input" oninput="calculatePaymentPreview()" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
 </div>
 <div id="discount_percentage_div" class="hidden">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Percentage (%)</label>
 <input type="number" step="0.01" name="discount_percentage" id="discount_percentage_input" min="0" max="100" oninput="calculatePaymentPreview()" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
 </div>
 <div id="discount_reference_div" class="hidden">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Reference (approved by?)</label>
 <input type="text" name="discount_reference" placeholder="Manager name or code" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Notes (Optional)</label>
 <textarea name="note" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500"></textarea>
 </div>
 <div class="flex gap-3">
 <button type="submit" id="submitPaymentBtn" class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">
 <i class="fas fa-save mr-2"></i>Payment Save
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
<div id="extraChargesModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
 <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-lg my-8 mx-4">
 <div class="flex justify-between items-center mb-4">
 <h3 class="text-xl font-bold text-gray-800"><i class="fas fa-plus-circle text-primary-600 mr-2"></i>Extra Charges Add</h3>
 <button onclick="closeExtraChargesModal()" class="text-gray-500 hover:text-gray-700">
 <i class="fas fa-times text-xl"></i>
 </button>
 </div>
 
 <!-- Current Extra Charges Display -->
 @if($booking->extra_charges > 0)
 <div class="bg-primary-100 border border-primary-300 rounded-lg p-3 mb-4">
 <div class="flex justify-between items-center">
 <span class="font-semibold text-gray-700">Current Extra Charges:</span>
 <span class="font-bold text-primary-600">{{ number_format($booking->extra_charges, 2) }}</span>
 </div>
 @if($booking->extra_charges_description)
 <p class="text-xs text-gray-600 mt-1">{{ $booking->extra_charges_description }}</p>
 @endif
 </div>
 @endif
 
 <!-- Categories List -->
 <div id="extraChargeCategoriesContainer" class="mb-4">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Service Select:</label>
 <div id="extraChargeCategoriesList" class="grid grid-cols-1 gap-2 max-h-48 overflow-y-auto border rounded-lg p-2">
 <div class="text-center text-gray-500 py-4">
 <i class="fas fa-spinner fa-spin"></i> Loading...
 </div>
 </div>
 </div>

 <!-- Selected Items -->
 <div id="selectedExtraCharges" class="mb-4 hidden">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Selected Items:</label>
 <div id="selectedChargesList" class="space-y-2"></div>
 <div class="mt-3 p-3 bg-primary-100 rounded-lg">
 <div class="flex justify-between items-center">
 <span class="font-semibold text-gray-700">Total Charges:</span>
 <span id="totalExtraChargeAmount" class="text-xl font-bold text-primary-600">0.00</span>
 </div>
 </div>
 </div>

 <!-- Manual Entry Option -->
 <div class="border-t pt-4 mt-4">
 <button type="button" onclick="toggleManualEntry()" class="text-sm text-primary-600 hover:text-primary-800">
 <i class="fas fa-edit mr-1"></i> Enter charge manually
 </button>
 <div id="manualEntryDiv" class="hidden mt-3 space-y-3">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-1">Amount ()</label>
 <input type="number" step="0.01" id="manualAmount" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500" placeholder="500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
 <input type="text" id="manualDescription" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500" placeholder="Laundry Service">
 </div>
 <button type="button" onclick="addManualCharge()" class="w-full bg-primary-600 text-white px-3 py-2 rounded-lg hover:bg-primary-700">
 <i class="fas fa-plus mr-1"></i> Add
 </button>
 </div>
 </div>

 <!-- Submit Button -->
 <div class="flex gap-3 mt-4">
 <button type="button" onclick="submitAllExtraCharges()" id="submitExtraChargesBtn" disabled class="flex-1 bg-primary-600 text-white px-4 py-3 rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed">
 <i class="fas fa-save mr-2"></i>Save
 </button>
 <button type="button" onclick="closeExtraChargesModal()" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
 Cancel
 </button>
 </div>
 </div>
</div>

<!-- Add Guest Modal -->
<div id="guestModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
 <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md my-8 mx-4 max-h-[90vh] overflow-y-auto">
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
 <input type="text" name="name" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">NID Number</label>
 <input type="text" name="nid" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
 <input type="text" name="phone" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div class="flex gap-3">
 <button type="submit" class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">
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
// Print functions
function printInvoice() {
 document.body.classList.remove('print-reservation');
 document.body.classList.add('print-invoice');
 document.getElementById('invoice-print-area').style.display = 'block';
 document.getElementById('reservation-print-area').style.display = 'none';
 window.print();
 setTimeout(() => {
 document.getElementById('invoice-print-area').style.display = '';
 document.getElementById('reservation-print-area').style.display = '';
 document.body.classList.remove('print-invoice');
 }, 500);
}

function printReservationLetter() {
 document.body.classList.remove('print-invoice');
 document.body.classList.add('print-reservation');
 document.getElementById('reservation-print-area').style.display = 'block';
 document.getElementById('invoice-print-area').style.display = 'none';
 window.print();
 setTimeout(() => {
 document.getElementById('invoice-print-area').style.display = '';
 document.getElementById('reservation-print-area').style.display = '';
 document.body.classList.remove('print-reservation');
 }, 500);
}

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
 document.getElementById('payment_amount').value = remainingBalance.toFixed(2);
 calculatePaymentPreview();
}

function closePaymentModal() {
 document.getElementById('paymentModal').classList.add('hidden');
 document.getElementById('paymentModal').classList.remove('flex');
}

function togglePaymentModalFields() {
 const method = document.getElementById('payment_modal_method').value;
 document.getElementById('payment_modal_bkash').classList.add('hidden');
 document.getElementById('payment_modal_bank').classList.add('hidden');
 
 if (method === 'bkash') {
 document.getElementById('payment_modal_bkash').classList.remove('hidden');
 } else if (method === 'card') {
 document.getElementById('payment_modal_bank').classList.remove('hidden');
 }
}

function toggleDiscountFields() {
 const type = document.getElementById('payment_discount_type').value;
 document.getElementById('discount_flat_div').classList.add('hidden');
 document.getElementById('discount_percentage_div').classList.add('hidden');
 document.getElementById('discount_reference_div').classList.add('hidden');
 
 if (type === 'flat') {
 document.getElementById('discount_flat_div').classList.remove('hidden');
 document.getElementById('discount_reference_div').classList.remove('hidden');
 } else if (type === 'percentage') {
 document.getElementById('discount_percentage_div').classList.remove('hidden');
 document.getElementById('discount_reference_div').classList.remove('hidden');
 }
 calculatePaymentPreview();
}

// Calculate real-time payment preview
const remainingBalance = {{ $remainingPayment }};

function calculatePaymentPreview() {
 const paymentAmount = parseFloat(document.getElementById('payment_amount').value) || 0;
 const discountType = document.getElementById('payment_discount_type').value;
 
 let discountAmount = 0;
 if (discountType === 'flat') {
 discountAmount = parseFloat(document.getElementById('discount_amount_input').value) || 0;
 } else if (discountType === 'percentage') {
 const percentage = parseFloat(document.getElementById('discount_percentage_input').value) || 0;
 discountAmount = (remainingBalance * percentage) / 100;
 }
 
 const totalDeduction = paymentAmount + discountAmount;
 const afterPayment = remainingBalance - totalDeduction;
 
 const calcDiv = document.getElementById('afterPaymentCalc');
 const remainingSpan = document.getElementById('afterPaymentRemaining');
 const errorDiv = document.getElementById('paymentError');
 const submitBtn = document.getElementById('submitPaymentBtn');
 
 if (totalDeduction > 0) {
 calcDiv.classList.remove('hidden');
 
 if (totalDeduction > remainingBalance) {
 // Error - exceeds remaining
 remainingSpan.textContent = '' + afterPayment.toFixed(2);
 remainingSpan.classList.remove('text-primary-600');
 remainingSpan.classList.add('text-red-600');
 errorDiv.classList.remove('hidden');
 errorDiv.textContent = 'Total payment and discount cannot exceed remaining amount!';
 submitBtn.disabled = true;
 submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
 } else {
 // Valid
 remainingSpan.textContent = '' + afterPayment.toFixed(2);
 remainingSpan.classList.add('text-primary-600');
 remainingSpan.classList.remove('text-red-600');
 errorDiv.classList.add('hidden');
 submitBtn.disabled = false;
 submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
 }
 } else {
 calcDiv.classList.add('hidden');
 errorDiv.classList.add('hidden');
 submitBtn.disabled = false;
 submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
 }
}

// Extra Charges Variables
let extraChargeCategories = [];
let selectedCharges = [];

function openExtraChargesModal() {
 document.getElementById('extraChargesModal').classList.remove('hidden');
 document.getElementById('extraChargesModal').classList.add('flex');
 selectedCharges = [];
 updateSelectedChargesUI();
 loadExtraChargeCategories();
}

function closeExtraChargesModal() {
 document.getElementById('extraChargesModal').classList.add('hidden');
 document.getElementById('extraChargesModal').classList.remove('flex');
 selectedCharges = [];
}

async function loadExtraChargeCategories() {
 try {
 const response = await fetch('/admin/api/extra-charge-categories');
 extraChargeCategories = await response.json();
 renderCategoriesList();
 } catch (error) {
 console.error('Error loading categories:', error);
 document.getElementById('extraChargeCategoriesList').innerHTML = `
 <div class="text-center text-red-500 py-4">
 <i class="fas fa-exclamation-circle"></i> Failed to load categories
 </div>`;
 }
}

function renderCategoriesList() {
 const container = document.getElementById('extraChargeCategoriesList');
 
 if (extraChargeCategories.length === 0) {
 container.innerHTML = `
 <div class="text-center text-gray-500 py-4">
 <i class="fas fa-info-circle"></i> No categories available. 
 <a href="/admin/extra-charge-categories/create" class="text-primary-600 hover:underline">Add New</a>
 </div>`;
 return;
 }
 
 container.innerHTML = extraChargeCategories.map(cat => `
 <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg hover:bg-primary-50 border">
 <div class="flex-1">
 <span class="font-semibold text-gray-800">${cat.name}</span>
 <span class="text-sm text-gray-500 ml-2">${parseFloat(cat.price).toFixed(2)}${cat.unit ? '/' + cat.unit : ''}</span>
 </div>
 <div class="flex items-center gap-2">
 <input type="number" min="1" value="1" id="qty_${cat.id}" 
 class="w-16 px-2 py-1 border rounded text-center focus:ring-2 focus:ring-orange-500" 
 placeholder="Quantity">
 <button type="button" onclick="addCategoryCharge(${cat.id})" 
 class="bg-primary-500 text-white px-3 py-1 rounded hover:bg-primary-600">
 <i class="fas fa-plus"></i>
 </button>
 </div>
 </div>
 `).join('');
}

function addCategoryCharge(categoryId) {
 const category = extraChargeCategories.find(c => c.id === categoryId);
 if (!category) return;
 
 const qtyInput = document.getElementById(`qty_${categoryId}`);
 const quantity = parseInt(qtyInput.value) || 1;
 const amount = parseFloat(category.price) * quantity;
 
 // Check if already exists, update quantity
 const existingIndex = selectedCharges.findIndex(c => c.categoryId === categoryId);
 if (existingIndex >= 0) {
 selectedCharges[existingIndex].quantity += quantity;
 selectedCharges[existingIndex].amount = parseFloat(category.price) * selectedCharges[existingIndex].quantity;
 } else {
 selectedCharges.push({
 categoryId: categoryId,
 name: category.name,
 price: parseFloat(category.price),
 unit: category.unit,
 quantity: quantity,
 amount: amount,
 description: `${category.name} × ${quantity}`
 });
 }
 
 qtyInput.value = 1; // Reset quantity
 updateSelectedChargesUI();
}

function removeSelectedCharge(index) {
 selectedCharges.splice(index, 1);
 updateSelectedChargesUI();
}

function updateSelectedChargesUI() {
 const container = document.getElementById('selectedChargesList');
 const totalSpan = document.getElementById('totalExtraChargeAmount');
 const selectedDiv = document.getElementById('selectedExtraCharges');
 const submitBtn = document.getElementById('submitExtraChargesBtn');
 
 if (selectedCharges.length === 0) {
 selectedDiv.classList.add('hidden');
 submitBtn.disabled = true;
 return;
 }
 
 selectedDiv.classList.remove('hidden');
 submitBtn.disabled = false;
 
 let totalAmount = 0;
 container.innerHTML = selectedCharges.map((charge, index) => {
 totalAmount += charge.amount;
 return `
 <div class="flex items-center justify-between p-2 bg-green-50 rounded border border-green-200">
 <div>
 <span class="font-medium">${charge.name}</span>
 <span class="text-sm text-gray-500 ml-1">× ${charge.quantity}</span>
 </div>
 <div class="flex items-center gap-2">
 <span class="font-bold text-primary-600">${charge.amount.toFixed(2)}</span>
 <button type="button" onclick="removeSelectedCharge(${index})" class="text-red-500 hover:text-red-700">
 <i class="fas fa-times"></i>
 </button>
 </div>
 </div>
 `;
 }).join('');
 
 totalSpan.textContent = '' + totalAmount.toFixed(2);
}

function toggleManualEntry() {
 const div = document.getElementById('manualEntryDiv');
 div.classList.toggle('hidden');
}

function addManualCharge() {
 const amount = parseFloat(document.getElementById('manualAmount').value);
 const description = document.getElementById('manualDescription').value.trim();
 
 if (!amount || amount <= 0) {
 alert('Enter amount');
 return;
 }
 if (!description) {
 alert('Enter Description');
 return;
 }
 
 selectedCharges.push({
 categoryId: null,
 name: description,
 price: amount,
 unit: null,
 quantity: 1,
 amount: amount,
 description: description
 });
 
 document.getElementById('manualAmount').value = '';
 document.getElementById('manualDescription').value = '';
 updateSelectedChargesUI();
}

async function submitAllExtraCharges() {
 if (selectedCharges.length === 0) return;
 
 let totalAmount = 0;
 let descriptions = [];
 
 selectedCharges.forEach(charge => {
 totalAmount += charge.amount;
 if (charge.quantity > 1) {
 descriptions.push(`${charge.name} × ${charge.quantity} = ${charge.amount.toFixed(2)}`);
 } else {
 descriptions.push(`${charge.name} = ${charge.amount.toFixed(2)}`);
 }
 });
 
 try {
 const response = await fetch(`/admin/bookings/{{ $booking->id }}/add-extra-charges`, {
 method: 'POST',
 headers: {
 'Content-Type': 'application/json',
 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
 },
 body: JSON.stringify({
 amount: totalAmount,
 description: descriptions.join('; ')
 })
 });

 if (response.ok) {
 showGlobalModal('success', 'Extra charges added!');
 setTimeout(() => location.reload(), 1500);
 } else {
 showGlobalModal('error', 'Failed to add extra charges!');
 }
 } catch (error) {
 console.error('Error:', error);
 showGlobalModal('error', 'Failed to add extra charges!');
 }
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
 'pending': 'Pending',
 'confirmed': 'Confirmed',
 'checked_in': 'Check-In',
 'checked_out': 'Check-Out',
 'cancelled': 'Cancelled'
 };
 const statusLabel = statusLabels[status] || status.replace('_', ' ').toUpperCase();
 
 showConfirmModal(`Do you want to change status to "${statusLabel}" change?`, async function() {
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
 showGlobalModal('success', 'Status updated!');
 setTimeout(() => location.reload(), 1500);
 } else {
 showGlobalModal('error', 'Failed to update status!');
 }
 } catch (error) {
 console.error('Error:', error);
 showGlobalModal('error', 'Failed to update status!');
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
 showGlobalModal('success', 'Time updated!');
 setTimeout(() => location.reload(), 1500);
 } else {
 showGlobalModal('error', 'Time update failed!');
 }
 } catch (error) {
 console.error('Error:', error);
 showGlobalModal('error', 'Time update failed!');
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
 bkash_number: formData.get('bkash_number') || '',
 bank_name: formData.get('bank_name') || '',
 discount_type: formData.get('discount_type'),
 discount_amount: parseFloat(formData.get('discount_amount')) || 0,
 discount_percentage: parseFloat(formData.get('discount_percentage')) || 0,
 discount_reference: formData.get('discount_reference') || '',
 note: formData.get('note')
 })
 });

 if (response.ok) {
 showGlobalModal('success', 'Payment recorded!');
 setTimeout(() => location.reload(), 1500);
 } else {
 const data = await response.json();
 showGlobalModal('error', data.message || 'Failed to record payment!');
 }
 } catch (error) {
 console.error('Error:', error);
 showGlobalModal('error', 'Failed to record payment!');
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
 showGlobalModal('success', 'Guest added!');
 setTimeout(() => location.reload(), 1500);
 } else {
 showGlobalModal('error', 'Failed to add guest!');
 }
 } catch (error) {
 console.error('Error:', error);
 showGlobalModal('error', 'Failed to add guest!');
 }
}

// Submit refund
async function submitRefund(e) {
 e.preventDefault();
 const formData = new FormData(e.target);
 const amount = parseFloat(formData.get('amount'));
 const maxRefund = {{ $booking->advance_payment }};
 
 if (amount > maxRefund) {
 showGlobalModal('error', `Refund amount ${maxRefund.toFixed(2)} cannot exceed!`);
 return;
 }
 
 showConfirmModal(`Do you want to refund ${amount.toFixed(2)} refund this?`, async function() {
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
 showGlobalModal('success', 'Refund successful!');
 setTimeout(() => location.reload(), 1500);
 } else {
 const data = await response.json();
 showGlobalModal('error', data.message || 'Failed to process refund!');
 }
 } catch (error) {
 console.error('Error:', error);
 showGlobalModal('error', 'Failed to process refund!');
 }
 });
}

// Submit VAT toggle
async function submitVatToggle() {
 const currentStatus = {{ $booking->vat_enabled ? 'true' : 'false' }};
 const action = currentStatus ? 'Disable' : 'Enable';
 
 showConfirmModal(`Do you want to VAT ${action}proceed?`, async function() {
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
 showGlobalModal('success', `VAT ${action} successful!`);
 setTimeout(() => location.reload(), 1500);
 } else {
 showGlobalModal('error', 'Failed to update VAT!');
 }
 } catch (error) {
 console.error('Error:', error);
 showGlobalModal('error', 'Failed to update VAT!');
 }
 });
}

// Auto-print invoice if ?print=invoice parameter is present
document.addEventListener('DOMContentLoaded', function() {
 const urlParams = new URLSearchParams(window.location.search);
 if (urlParams.get('print') === 'invoice') {
 setTimeout(function() {
 printInvoice();
 }, 500);
 }
});
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
