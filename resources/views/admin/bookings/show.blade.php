@extends('layouts.admin')

@section('content')
<div class="print:p-0">
 @php
 $nights = \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date));
 $nights = max(1, $nights);
 $allRooms = $booking->getAllRooms();
 $bookingRooms = $booking->bookingRooms;
 
 // Use model method for consistent base amount calculation
 $baseAmount = $booking->getCalculatedTotal();
 
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
 $totalDeposited = $booking->getTotalDeposited();
$remainingPayment = $booking->getCalculatedRemaining();

 // Date & status logic
 $today = \Carbon\Carbon::now()->startOfDay();
 $now = \Carbon\Carbon::now('Asia/Dhaka');
 $checkInDate = \Carbon\Carbon::parse($booking->check_in_date)->startOfDay();
 $checkOutDate = \Carbon\Carbon::parse($booking->check_out_date)->startOfDay();
 $isCheckInDayOrPast = $checkInDate->lte($today);
 $checkoutDateTime = $booking->getCheckOutDateTime();
 $isBeforeCheckoutTime = $checkoutDateTime ? $now->lt($checkoutDateTime) : false;
 $isExtendedStay = $booking->status === 'checked_out' && ($checkOutDate->gt($today) || ($checkOutDate->eq($today) && $isBeforeCheckoutTime));
 $canCheckIn = ($booking->status === 'confirmed' && $isCheckInDayOrPast) || $isExtendedStay;
 $canCancel = !in_array($booking->status, ['checked_out', 'cancelled']);
 $canRefund = $totalDeposited > 0 && $booking->status !== 'checked_out';
 $isEarlyDeparture = $booking->status === 'checked_in' && $checkOutDate->gt($today);
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
 @if($canRefund)
 <button onclick="openRefundModal()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition flex items-center gap-2">
 <i class="fas fa-undo"></i>
 <span>Process Refund</span>
 </button>
 @endif
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
 @if($canCheckIn)
 <button onclick="updateStatus({{ $booking->id }}, 'checked_in')" class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition flex items-center gap-2">
 <i class="fas fa-sign-in-alt"></i>
 <span>{{ $isExtendedStay ? 'Re-Check In' : 'Check In' }}</span>
 </button>
 @endif
 @if($booking->status === 'checked_in')
 <button onclick="updateStatus({{ $booking->id }}, 'checked_out')" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition flex items-center gap-2">
 <i class="fas fa-sign-out-alt"></i>
 <span>Check Out</span>
 </button>
 @endif
 <div class="relative">
 <select onchange="updateStatus({{ $booking->id }}, this.value)" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition cursor-pointer appearance-none pr-10">
 <option value="">Change Status</option>

 {{-- Pending can become confirmed or cancelled --}}
 @if(in_array($booking->status, ['pending', 'confirmed', 'checked_in']))
 <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
 @endif

 {{-- Confirmed can become pending, checked_in (on date), or cancelled --}}
 @if(in_array($booking->status, ['pending', 'confirmed', 'checked_in']))
 <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
 @endif

 {{-- Checked In shown when check-in date arrived AND current status is confirmed, checked_in, or extended checked_out --}}
 @if($isCheckInDayOrPast && in_array($booking->status, ['confirmed', 'checked_in']))
 <option value="checked_in" {{ $booking->status === 'checked_in' ? 'selected' : '' }}>Checked In</option>
 @elseif($isExtendedStay)
 <option value="checked_in">Re-Check In</option>
 @endif

 {{-- Checked Out shown when currently checked_in OR already checked_out --}}
 @if(in_array($booking->status, ['checked_in', 'checked_out']))
 <option value="checked_out" {{ $booking->status === 'checked_out' ? 'selected' : '' }}>Checked Out</option>
 @endif

 {{-- Cancelled shown for all except checked_out and already cancelled --}}
 @if($canCancel)
 <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
 @endif
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
 <div class="flex justify-between items-center mb-4">
 <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
 <i class="fas fa-user text-primary-600"></i>
 Guest Information
 </h2>
 <button onclick="openEditCustomerModal()" class="print:hidden bg-primary-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-primary-700 flex items-center gap-1">
 <i class="fas fa-edit"></i> Edit
 </button>
 </div>
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
 
 @php $calculatedPaymentStatus = $booking->getCalculatedPaymentStatus(); @endphp
 <!-- Payment Status -->
 <div class="border-2 rounded-lg p-4 text-center
 @if($calculatedPaymentStatus === 'paid') 
 border-primary-500 bg-green-50
 @elseif($calculatedPaymentStatus === 'partial') 
 border-yellow-500 bg-yellow-50
 @else 
 border-red-500 bg-red-50
 @endif">
 <i class="fas fa-money-bill-wave text-2xl mb-2
 @if($calculatedPaymentStatus === 'paid') 
 text-primary-600
 @elseif($calculatedPaymentStatus === 'partial') 
 text-yellow-600
 @else 
 text-red-600
 @endif"></i>
 <p class="text-xs font-semibold text-gray-600 mb-1">Payment Status</p>
 <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
 @if($calculatedPaymentStatus === 'paid') 
 bg-green-500 text-white
 @elseif($calculatedPaymentStatus === 'partial') 
 bg-yellow-500 text-gray-900
 @else 
 bg-red-500 text-white
 @endif">
 {{ strtoupper($calculatedPaymentStatus) }}
 </span>
 @if($remainingPayment > 0)
 <p class="text-xs text-gray-600 mt-1">Due: {{ number_format($remainingPayment, 2) }}</p>
 @endif
 </div>
 </div>
 
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
 <div class="md:col-span-2">
 <span class="font-semibold text-gray-600">Room(s):</span>
 @php $allRooms = $booking->getAllRooms(); $bookingRooms = $booking->bookingRooms; @endphp
 @if($allRooms->count() > 1)
 <div class="flex flex-wrap gap-2 mt-1">
 @foreach($allRooms as $room)
 @php
 $br = $bookingRooms->firstWhere('room_id', $room->id);
 $roomDates = '';
 if ($br && $br->check_in_date && $br->check_out_date) {
 $brIn = \Carbon\Carbon::parse($br->check_in_date)->format('d M');
 $brOut = \Carbon\Carbon::parse($br->check_out_date)->format('d M');
 $bIn = \Carbon\Carbon::parse($booking->check_in_date)->format('d M');
 $bOut = \Carbon\Carbon::parse($booking->check_out_date)->format('d M');
 if ($brIn !== $bIn || $brOut !== $bOut) {
 $roomDates = ' (' . $brIn . ' - ' . $brOut . ')';
 }
 }
 @endphp
 <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
 {{ $room->room_number }} - {{ $room->roomType->name ?? 'N/A' }}{{ $roomDates }}
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
 @if($booking->check_in_time) at {{ \Carbon\Carbon::parse($booking->check_in_time)->format('h:i A') }} @endif
 </p>
 </div>
 <div>
 <span class="font-semibold text-gray-600">Check-Out:</span>
 <p class="text-gray-900">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }} 
 @if($booking->check_out_time) at {{ \Carbon\Carbon::parse($booking->check_out_time)->format('h:i A') }} @endif
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
 <div>
 <span class="font-semibold text-gray-600">Updated By:</span>
 <p class="text-gray-900">{{ $booking->updatedBy->name ?? 'N/A' }}</p>
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
 @php
 $customerPhotos = $booking->getDocuments('customer_photo');
 $nidDocs = $booking->getDocuments('customer_nid_document');
 $passportDocs = $booking->getDocuments('passport_document');
 $visitingCards = $booking->getDocuments('visiting_card');
 $hasAnyDoc = !empty($customerPhotos) || !empty($nidDocs) || !empty($passportDocs) || !empty($visitingCards);
 @endphp
 @if($hasAnyDoc)
 <div class="bg-white rounded-xl shadow-lg p-6 print:hidden">
 <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
 <i class="fas fa-file text-primary-600"></i>
 Uploaded Documents
 </h2>
@if(!empty($customerPhotos))
<div class="mb-4">
<h3 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-1"><i class="fas fa-camera text-primary-600"></i> Customer Photos</h3>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3">
@foreach($customerPhotos as $idx => $doc)
<div class="bg-primary-50 p-2 rounded-lg text-center hover:bg-primary-100 transition">
<a href="{{ Storage::url($doc) }}" target="_blank" class="block">
<img src="{{ Storage::url($doc) }}" alt="Customer Photo" class="w-full h-24 object-cover rounded mb-1 border">
<p class="text-xs text-gray-700 font-semibold">Photo {{ $idx + 1 }}</p>
</a>
</div>
@endforeach
</div>
</div>
@endif

@if(!empty($nidDocs))
<div class="mb-4">
<h3 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-1"><i class="fas fa-id-card text-green-600"></i> NID Documents</h3>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3">
@foreach($nidDocs as $idx => $doc)
<div class="bg-green-50 p-2 rounded-lg text-center hover:bg-green-100 transition">
<a href="{{ Storage::url($doc) }}" target="_blank" class="block">
@if(Str::endsWith(strtolower($doc), ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
<img src="{{ Storage::url($doc) }}" alt="NID Document" class="w-full h-24 object-cover rounded mb-1 border">
@else
<div class="w-full h-24 flex items-center justify-center bg-green-100 rounded mb-1">
<i class="fas fa-file-pdf text-green-600 text-3xl"></i>
</div>
@endif
<p class="text-xs text-gray-700 font-semibold">NID {{ $idx + 1 }}</p>
</a>
</div>
@endforeach
</div>
</div>
@endif

@if(!empty($passportDocs))
<div class="mb-4">
<h3 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-1"><i class="fas fa-passport text-blue-600"></i> Passport Documents</h3>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3">
@foreach($passportDocs as $idx => $doc)
<div class="bg-blue-50 p-2 rounded-lg text-center hover:bg-blue-100 transition">
<a href="{{ Storage::url($doc) }}" target="_blank" class="block">
@if(Str::endsWith(strtolower($doc), ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
<img src="{{ Storage::url($doc) }}" alt="Passport" class="w-full h-24 object-cover rounded mb-1 border">
@else
<div class="w-full h-24 flex items-center justify-center bg-blue-100 rounded mb-1">
<i class="fas fa-file-pdf text-blue-600 text-3xl"></i>
</div>
@endif
<p class="text-xs text-gray-700 font-semibold">Passport {{ $idx + 1 }}</p>
</a>
</div>
@endforeach
</div>
</div>
@endif

@if(!empty($visitingCards))
<div class="mb-2">
<h3 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-1"><i class="fas fa-address-card text-purple-600"></i> Visiting Cards</h3>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3">
@foreach($visitingCards as $idx => $doc)
<div class="bg-purple-50 p-2 rounded-lg text-center hover:bg-purple-100 transition">
<a href="{{ Storage::url($doc) }}" target="_blank" class="block">
@if(Str::endsWith(strtolower($doc), ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
<img src="{{ Storage::url($doc) }}" alt="Visiting Card" class="w-full h-24 object-cover rounded mb-1 border">
@else
<div class="w-full h-24 flex items-center justify-center bg-purple-100 rounded mb-1">
<i class="fas fa-file-pdf text-purple-600 text-3xl"></i>
</div>
@endif
<p class="text-xs text-gray-700 font-semibold">Card {{ $idx + 1 }}</p>
</a>
</div>
@endforeach
</div>
</div>
@endif
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
 <td class="p-2">{{ $payment->created_at->format('d/m/Y h:i A') }}</td>
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
 @php
 $extraData = $booking->extra_charges_data ?? [];
 @endphp
 @if(!empty($extraData) && is_array($extraData))
 <div class="mt-2 pt-2 border-t border-primary-400">
 <p class="text-xs text-primary-100 font-semibold mb-1"><i class="fas fa-list mr-1"></i> Details:</p>
 @foreach($extraData as $item)
 <div class="text-xs text-primary-100 flex justify-between">
 <span>{{ $item['name'] ?? 'Unknown' }} {{ ($item['quantity'] ?? 1) > 1 ? '× ' . ($item['quantity'] ?? 1) . ' @ ' . number_format($item['price'] ?? 0) : '' }}</span>
 <span>{{ number_format($item['amount'] ?? 0, 2) }}</span>
 </div>
 @endforeach
 </div>
 @elseif($booking->extra_charges_description)
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
 @if($calculatedPaymentStatus === 'paid') bg-green-500 text-white
 @elseif($calculatedPaymentStatus === 'partial') bg-yellow-500 text-gray-900
 @else bg-red-500 text-white
 @endif">
 {{ strtoupper($calculatedPaymentStatus) }}
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
 Available for refund: {{ number_format($totalDeposited, 2) }}
 </p>
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Refund Amount ()</label>
 <input type="number" step="0.01" name="amount" required max="{{ $totalDeposited }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500">
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
 <h3 class="text-xl font-bold text-gray-800">Edit Check-In/Out Date & Time</h3>
 <button onclick="closeTimeModal()" class="text-gray-500 hover:text-gray-700">
 <i class="fas fa-times text-xl"></i>
 </button>
 </div>
 <form id="timeForm" onsubmit="submitTimeUpdate(event)">
 <div class="space-y-4">
 <div class="grid grid-cols-2 gap-3">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Check-In Date</label>
 <input type="date" name="check_in_date" value="{{ $booking->check_in_date?->format('Y-m-d') }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Check-In Time</label>
 <input type="time" name="check_in_time" value="{{ $booking->check_in_time }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 </div>
 <div class="grid grid-cols-2 gap-3">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Check-Out Date</label>
 <input type="date" name="check_out_date" value="{{ $booking->check_out_date?->format('Y-m-d') }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Check-Out Time</label>
 <input type="time" name="check_out_time" value="{{ $booking->check_out_time }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
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

<!-- Edit Customer Modal -->
<div id="editCustomerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
 <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-2xl my-8 mx-4 max-h-[90vh] overflow-y-auto">
 <div class="flex justify-between items-center mb-4">
 <h3 class="text-xl font-bold text-gray-800">Edit Customer Information</h3>
 <button onclick="closeEditCustomerModal()" class="text-gray-500 hover:text-gray-700">
 <i class="fas fa-times text-xl"></i>
 </button>
 </div>
 <form id="editCustomerForm" onsubmit="submitEditCustomer(event)" enctype="multipart/form-data">
 @csrf
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
 <input type="text" name="customer_name" value="{{ $booking->customer_name }}" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 text-sm">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
 <input type="text" name="customer_phone" value="{{ $booking->customer_phone }}" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 text-sm">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-1">NID</label>
 <input type="text" name="customer_nid" value="{{ $booking->customer_nid }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 text-sm">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
 <input type="email" name="customer_email" value="{{ $booking->customer_email }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 text-sm">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-1">WhatsApp</label>
 <input type="text" name="customer_whatsapp" value="{{ $booking->customer_whatsapp }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 text-sm">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-1">Passport No</label>
 <input type="text" name="passport_number" value="{{ $booking->passport_number }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 text-sm">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-1">Company</label>
 <input type="text" name="company_name" value="{{ $booking->company_name }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 text-sm">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-1">Reference Name</label>
 <input type="text" name="reference_name" value="{{ $booking->reference_name }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 text-sm">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-1">Reference Phone</label>
 <input type="text" name="reference_phone" value="{{ $booking->reference_phone }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 text-sm">
 </div>
 <div class="md:col-span-2">
 <label class="block text-sm font-semibold text-gray-700 mb-1">Address</label>
 <textarea name="customer_address" rows="2" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 text-sm">{{ $booking->customer_address }}</textarea>
 </div>
 </div>

 <!-- Documents -->
 <div class="mt-4 border-t pt-4">
 <h4 class="text-sm font-bold text-gray-700 mb-3">Documents (optional — check to remove, select new to add more)</h4>
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
 @php
 $editPhotos = $booking->getDocuments('customer_photo');
 $editNids = $booking->getDocuments('customer_nid_document');
 $editPassports = $booking->getDocuments('passport_document');
 $editCards = $booking->getDocuments('visiting_card');
 @endphp
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-1">Photo</label>
 @if(!empty($editPhotos))
 <div class="flex flex-wrap gap-2 mb-2">
 @foreach($editPhotos as $doc)
 <div class="relative inline-block">
 <a href="{{ asset('storage/'.$doc) }}" target="_blank" class="block">
 <img src="{{ asset('storage/'.$doc) }}" class="w-16 h-16 object-cover rounded border">
 </a>
 <label class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center cursor-pointer" title="Remove">
 <input type="checkbox" name="remove_customer_photo[]" value="{{ $doc }}" class="hidden">
 <i class="fas fa-times text-[8px]"></i>
 </label>
 </div>
 @endforeach
 </div>
 @endif
 <input type="file" name="customer_photo[]" accept="image/*" multiple class="w-full text-sm border rounded-lg px-2 py-1.5">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-1">NID Document</label>
 @if(!empty($editNids))
 <div class="flex flex-wrap gap-2 mb-2">
 @foreach($editNids as $doc)
 <div class="relative inline-block">
 <a href="{{ asset('storage/'.$doc) }}" target="_blank" class="block">
 @if(Str::endsWith(strtolower($doc), ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
 <img src="{{ asset('storage/'.$doc) }}" class="w-16 h-16 object-cover rounded border">
 @else
 <div class="w-16 h-16 flex items-center justify-center bg-gray-100 rounded border"><i class="fas fa-file-pdf text-red-500 text-xl"></i></div>
 @endif
 </a>
 <label class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center cursor-pointer" title="Remove">
 <input type="checkbox" name="remove_customer_nid_document[]" value="{{ $doc }}" class="hidden">
 <i class="fas fa-times text-[8px]"></i>
 </label>
 </div>
 @endforeach
 </div>
 @endif
 <input type="file" name="customer_nid_document[]" accept="image/*,.pdf" multiple class="w-full text-sm border rounded-lg px-2 py-1.5">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-1">Passport Document</label>
 @if(!empty($editPassports))
 <div class="flex flex-wrap gap-2 mb-2">
 @foreach($editPassports as $doc)
 <div class="relative inline-block">
 <a href="{{ asset('storage/'.$doc) }}" target="_blank" class="block">
 @if(Str::endsWith(strtolower($doc), ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
 <img src="{{ asset('storage/'.$doc) }}" class="w-16 h-16 object-cover rounded border">
 @else
 <div class="w-16 h-16 flex items-center justify-center bg-gray-100 rounded border"><i class="fas fa-file-pdf text-red-500 text-xl"></i></div>
 @endif
 </a>
 <label class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center cursor-pointer" title="Remove">
 <input type="checkbox" name="remove_passport_document[]" value="{{ $doc }}" class="hidden">
 <i class="fas fa-times text-[8px]"></i>
 </label>
 </div>
 @endforeach
 </div>
 @endif
 <input type="file" name="passport_document[]" accept="image/*,.pdf" multiple class="w-full text-sm border rounded-lg px-2 py-1.5">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-1">Visiting Card</label>
 @if(!empty($editCards))
 <div class="flex flex-wrap gap-2 mb-2">
 @foreach($editCards as $doc)
 <div class="relative inline-block">
 <a href="{{ asset('storage/'.$doc) }}" target="_blank" class="block">
 @if(Str::endsWith(strtolower($doc), ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
 <img src="{{ asset('storage/'.$doc) }}" class="w-16 h-16 object-cover rounded border">
 @else
 <div class="w-16 h-16 flex items-center justify-center bg-gray-100 rounded border"><i class="fas fa-file-pdf text-red-500 text-xl"></i></div>
 @endif
 </a>
 <label class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center cursor-pointer" title="Remove">
 <input type="checkbox" name="remove_visiting_card[]" value="{{ $doc }}" class="hidden">
 <i class="fas fa-times text-[8px]"></i>
 </label>
 </div>
 @endforeach
 </div>
 @endif
 <input type="file" name="visiting_card[]" accept="image/*,.pdf" multiple class="w-full text-sm border rounded-lg px-2 py-1.5">
 </div>
 </div>
 </div>

 <div class="flex gap-3 mt-5">
 <button type="submit" id="editCustomerSubmitBtn" class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 font-semibold">
 <i class="fas fa-save mr-1"></i> Save Changes
 </button>
 <button type="button" onclick="closeEditCustomerModal()" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
 Cancel
 </button>
 </div>
 </form>

 <!-- Upload Progress Overlay -->
 <div id="uploadProgressOverlay" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
 <div class="bg-white rounded-xl shadow-2xl p-6 w-80 text-center">
 <div class="mb-3">
 <i class="fas fa-cloud-upload-alt text-4xl text-primary-600 animate-bounce"></i>
 </div>
 <h3 class="text-lg font-bold text-gray-800 mb-1">Uploading Documents...</h3>
 <p id="uploadPercentText" class="text-2xl font-bold text-primary-600 mb-3">0%</p>
 <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
 <div id="uploadProgressBar" class="bg-primary-600 h-4 rounded-full transition-all duration-200" style="width: 0%"></div>
 </div>
 <p class="text-xs text-gray-500 mt-2">Please do not close this window</p>
 </div>
 </div>
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
 <option value="mfs">bKash / MFS</option>
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
 <select name="discount_type" id="payment_discount_type" onchange="toggleDiscountFields(true)" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
 <option value="none">No Discount</option>
 <option value="flat">Fixed Amount</option>
 <option value="percentage">Percentage (%)</option>
 </select>
 </div>
 <div id="discount_flat_div" class="hidden">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Amount ()</label>
 <input type="number" step="0.01" name="discount_amount" id="discount_amount_input" oninput="calculatePaymentPreview(true)" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
 </div>
 <div id="discount_percentage_div" class="hidden">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Percentage (%)</label>
 <input type="number" step="0.01" name="discount_percentage" id="discount_percentage_input" min="0" max="100" oninput="calculatePaymentPreview(true)" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
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

 // Reset to clean defaults for better UX each time modal opens
 document.getElementById('payment_modal_method').value = 'cash';
 togglePaymentModalFields();

 document.getElementById('payment_discount_type').value = 'none';
 document.getElementById('discount_amount_input').value = '';
 document.getElementById('discount_percentage_input').value = '';
 toggleDiscountFields(true); // force auto-calc on modal open

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
 
 if (method === 'mfs') {
 document.getElementById('payment_modal_bkash').classList.remove('hidden');
 } else if (method === 'card') {
 document.getElementById('payment_modal_bank').classList.remove('hidden');
 }
}

function toggleDiscountFields(forceAutoUpdateAmount = false) {
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

 // When discount type changes, auto-adjust payment amount for user friendliness
 autoAdjustPaymentAmountFromDiscount();
 calculatePaymentPreview(forceAutoUpdateAmount);
}

// Calculate real-time payment preview
const remainingBalance = {{ $remainingPayment }};

function getDiscountAmount() {
 const discountType = document.getElementById('payment_discount_type').value;

 if (discountType === 'flat') {
 return parseFloat(document.getElementById('discount_amount_input').value) || 0;
 }

 if (discountType === 'percentage') {
 const percentage = parseFloat(document.getElementById('discount_percentage_input').value) || 0;
 const safePercentage = Math.max(0, Math.min(100, percentage));
 return (remainingBalance * safePercentage) / 100;
 }

 return 0;
}

function autoAdjustPaymentAmountFromDiscount() {
 const paymentInput = document.getElementById('payment_amount');
 const discountAmount = getDiscountAmount();

 // Auto-fill payment so (payment + discount) targets full remaining amount
 const suggestedPayment = Math.max(0, remainingBalance - discountAmount);
 paymentInput.value = suggestedPayment.toFixed(2);
}

function calculatePaymentPreview(forceAutoUpdateAmount = false) {
 if (forceAutoUpdateAmount) {
 autoAdjustPaymentAmountFromDiscount();
 }

 const paymentAmount = parseFloat(document.getElementById('payment_amount').value) || 0;
 const discountAmount = getDiscountAmount();
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
 errorDiv.textContent = 'Total payment + discount cannot exceed remaining amount.';
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

// Pre-load existing extra charges from booking
const existingExtraChargesData = @json($booking->extra_charges_data ?? []);

function openExtraChargesModal() {
 document.getElementById('extraChargesModal').classList.remove('hidden');
 document.getElementById('extraChargesModal').classList.add('flex');
 selectedCharges = [];

 // Populate from existing data if available
 if (existingExtraChargesData && existingExtraChargesData.length > 0) {
  existingExtraChargesData.forEach(item => {
   selectedCharges.push({
    categoryId: item.category_id || null,
    name: item.name || 'Unknown',
    price: parseFloat(item.price) || 0,
    unit: item.unit || null,
    quantity: parseInt(item.quantity) || 1,
    amount: parseFloat(item.amount) || 0,
    description: item.name || ''
   });
  });
 }

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
 let items = [];

 selectedCharges.forEach(charge => {
 totalAmount += charge.amount;
 if (charge.quantity > 1) {
 descriptions.push(`${charge.name} × ${charge.quantity} = ${charge.amount.toFixed(2)}`);
 } else {
 descriptions.push(`${charge.name} = ${charge.amount.toFixed(2)}`);
 }
 items.push({
 category_id: charge.categoryId,
 name: charge.name,
 price: charge.price,
 quantity: charge.quantity,
 amount: charge.amount
 });
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
 description: descriptions.join('; '),
 items: items
 })
 });

 if (response.ok) {
 showGlobalModal('success', 'Extra charges added!');
 setTimeout(() => location.reload(), 1500);
 } else {
 const errorText = await response.text();
 showGlobalModal('error', 'Failed: ' + (errorText.substring(0, 200) || 'Server error'));
 }
 } catch (error) {
 console.error('Error:', error);
 showGlobalModal('error', 'Failed: ' + error.message);
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

 let confirmMsg = `Do you want to change status to "${statusLabel}"?`;
 if (status === 'checked_in') {
  confirmMsg = 'Check in this guest now? The room will be marked as occupied.';
 } else if (status === 'checked_out') {
  confirmMsg = 'Check out this guest now? The room will be freed up.';
 } else if (status === 'cancelled') {
  confirmMsg = 'Cancel this booking? This action cannot be undone.';
 }

 showConfirmModal(confirmMsg, async function() {
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
 showGlobalModal('success', `Status updated to ${statusLabel}!`);
 setTimeout(() => location.reload(), 1200);
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
 check_in_date: formData.get('check_in_date'),
 check_out_date: formData.get('check_out_date'),
 check_in_time: formData.get('check_in_time'),
 check_out_time: formData.get('check_out_time')
 })
 });

 if (response.ok) {
 showGlobalModal('success', 'Date/Time updated!');
 setTimeout(() => location.reload(), 1500);
 } else {
 const data = await response.json();
 if (data.conflicts && data.conflicts.length > 0) {
 let html = '<div class="text-center mb-4"><i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-2"></i>';
 html += '<p class="text-lg font-bold text-gray-800">' + (data.message || 'Room is already booked for this date.') + '</p></div>';
 html += '<div class="space-y-3 max-h-60 overflow-y-auto">';
 data.conflicts.forEach(function(c) {
 const statusColors = {confirmed: 'bg-blue-100 text-blue-700', checked_in: 'bg-green-100 text-green-700', pending: 'bg-yellow-100 text-yellow-700'};
 const statusColor = statusColors[c.status] || 'bg-gray-100 text-gray-700';
 html += '<div class="border rounded-lg p-3 bg-gray-50">';
 html += '<div class="flex justify-between items-start mb-1">';
 html += '<span class="font-semibold text-gray-800">#' + c.id + ' - ' + c.customer_name + '</span>';
 html += '<span class="px-2 py-0.5 rounded-full text-xs font-semibold ' + statusColor + '">' + c.status.replace('_', ' ').toUpperCase() + '</span>';
 html += '</div>';
 html += '<div class="text-sm text-gray-600"><i class="fas fa-bed mr-1"></i>Room: ' + c.rooms + '</div>';
 html += '<div class="text-sm text-gray-600"><i class="fas fa-calendar mr-1"></i>' + c.check_in + ' → ' + c.check_out + '</div>';
 html += '<div class="text-sm text-gray-600"><i class="fas fa-phone mr-1"></i>' + c.customer_phone + '</div>';
 html += '</div>';
 });
 html += '</div>';
 html += '<p class="text-center text-sm text-gray-500 mt-3">Please select a different date.</p>';
 showGlobalModalHtml(html);
 } else {
 showGlobalModal('error', data.message || 'Date/Time update failed!');
 }
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
 const maxRefund = {{ $totalDeposited }};
 
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

// Edit Customer modal
function openEditCustomerModal() {
 document.getElementById('editCustomerModal').classList.remove('hidden');
 document.getElementById('editCustomerModal').classList.add('flex');
}
function closeEditCustomerModal() {
 document.getElementById('editCustomerModal').classList.add('hidden');
 document.getElementById('editCustomerModal').classList.remove('flex');
}

// Document remove checkbox visual feedback
document.querySelectorAll('[name^="remove_"]').forEach(cb => {
    cb.addEventListener('change', function() {
        const thumb = this.closest('.relative');
        if (this.checked) {
            thumb.style.opacity = '0.4';
            thumb.querySelector('img, .w-16')?.classList.add('grayscale');
        } else {
            thumb.style.opacity = '1';
            thumb.querySelector('img, .w-16')?.classList.remove('grayscale');
        }
    });
});

function submitEditCustomer(e) {
 e.preventDefault();
 const form = document.getElementById('editCustomerForm');
 const formData = new FormData(form);
 const overlay = document.getElementById('uploadProgressOverlay');
 const progressBar = document.getElementById('uploadProgressBar');
 const percentText = document.getElementById('uploadPercentText');
 const submitBtn = document.getElementById('editCustomerSubmitBtn');

 // Show overlay and disable button
 overlay.classList.remove('hidden');
 overlay.classList.add('flex');
 submitBtn.disabled = true;
 submitBtn.classList.add('opacity-50', 'cursor-not-allowed');

 const xhr = new XMLHttpRequest();

 xhr.upload.addEventListener('progress', function(event) {
 if (event.lengthComputable) {
 const percent = Math.round((event.loaded / event.total) * 100);
 progressBar.style.width = percent + '%';
 percentText.textContent = percent + '%';
 }
 });

 xhr.addEventListener('load', function() {
 overlay.classList.add('hidden');
 overlay.classList.remove('flex');
 submitBtn.disabled = false;
 submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');

 if (xhr.status >= 200 && xhr.status < 300) {
 showGlobalModal('success', 'Customer information updated!');
 setTimeout(() => location.reload(), 1500);
 } else {
 let msg = 'Failed to update customer!';
 try {
 const data = JSON.parse(xhr.responseText);
 msg = data.message || msg;
 } catch (e) {}
 showGlobalModal('error', msg);
 }
 });

 xhr.addEventListener('error', function() {
 overlay.classList.add('hidden');
 overlay.classList.remove('flex');
 submitBtn.disabled = false;
 submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
 showGlobalModal('error', 'Failed to update customer!');
 });

 xhr.open('POST', `/admin/bookings/{{ $booking->id }}/update-customer`);
 xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
 xhr.send(formData);
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
