@extends('layouts.admin')
@php
$canEditDates = \Carbon\Carbon::parse($booking->check_in_date)->startOfDay()->lte(\Carbon\Carbon::now()->startOfDay());
@endphp
@section('content')
<div class="p-6">
 <div class="mb-8"><h1 class="text-3xl font-bold text-gray-800">Booking Edit #{{ $booking->id }}</h1></div>

 @if(session('success'))
 <div class="bg-primary-100 border border-primary-400 text-primary-700 px-4 py-3 rounded-lg mb-6">{{ session('success') }}</div>
 @endif
 @if(session('error'))
 <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">{{ session('error') }}</div>
 @endif

 <div class="bg-white rounded-xl shadow-lg p-8">
 <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
 @csrf @method('PUT')

 <!-- Room & Dates -->
 <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fas fa-bed mr-2 text-primary-600"></i>Room & Dates</h3>
 @if(!$canEditDates)
 <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-2 rounded-lg mb-4 text-sm">
 <i class="fas fa-info-circle mr-1"></i>Check-In / Check-Out date auto-activate hobe booking date asar por. Ager date edit kora jabe na.
 </div>
 @endif
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Room *</label>
 <select name="room_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 @foreach(\App\Models\Room::with('roomType')->get() as $room)
 <option value="{{ $room->id }}" {{ $booking->room_id == $room->id ? 'selected' : '' }}>{{ $room->room_number }} - {{ $room->roomType->name ?? 'N/A' }} ({{ number_format($room->roomType->price_per_night ?? 0) }})</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Check-In Date *</label>
 <input type="date" name="check_in_date" value="{{ old('check_in_date', $booking->check_in_date ? $booking->check_in_date->format('Y-m-d') : '') }}" required {{ $canEditDates ? '' : 'readonly' }} class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 {{ $canEditDates ? '' : 'bg-gray-100 text-gray-500 cursor-not-allowed' }}">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Check-Out Date *</label>
 <input type="date" name="check_out_date" value="{{ old('check_out_date', $booking->check_out_date ? $booking->check_out_date->format('Y-m-d') : '') }}" required {{ $canEditDates ? '' : 'readonly' }} class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 {{ $canEditDates ? '' : 'bg-gray-100 text-gray-500 cursor-not-allowed' }}">
 </div>
 </div>

 <!-- Customer Info -->
 <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fas fa-user mr-2 text-primary-600"></i>Customer Info</h3>
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Customer Name *</label>
 <input type="text" name="customer_name" value="{{ old('customer_name', $booking->customer_name) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number *</label>
 <input type="tel" name="customer_phone" value="{{ old('customer_phone', $booking->customer_phone) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
 <input type="email" name="customer_email" value="{{ old('customer_email', $booking->customer_email) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">NID Number</label>
 <input type="text" name="customer_nid" value="{{ old('customer_nid', $booking->customer_nid) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Company</label>
 <input type="text" name="company_name" value="{{ old('company_name', $booking->company_name) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Booking Purpose</label>
 <select name="booking_purpose" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 <option value="">{{ __('admin.booking.purpose_others') }}</option>
 <option value="company" {{ old('booking_purpose', $booking->booking_purpose) == 'company' ? 'selected' : '' }}>{{ __('admin.booking.purpose_company') }}</option>
 <option value="family" {{ old('booking_purpose', $booking->booking_purpose) == 'family' ? 'selected' : '' }}>{{ __('admin.booking.purpose_family') }}</option>
 <option value="wedding" {{ old('booking_purpose', $booking->booking_purpose) == 'wedding' ? 'selected' : '' }}>{{ __('admin.booking.purpose_wedding') }}</option>
 <option value="single" {{ old('booking_purpose', $booking->booking_purpose) == 'single' ? 'selected' : '' }}>{{ __('admin.booking.purpose_single') }}</option>
 <option value="others" {{ old('booking_purpose', $booking->booking_purpose) == 'others' ? 'selected' : '' }}>{{ __('admin.booking.purpose_others') }}</option>
 </select>
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Number of Guests</label>
 <input type="number" name="number_of_guests" value="{{ old('number_of_guests', $booking->number_of_guests) }}" min="1" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div class="md:col-span-2">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
 <textarea name="customer_address" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('customer_address', $booking->customer_address) }}</textarea>
 </div>
 </div>

 <!-- Reference Info -->
 <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fas fa-user-tag mr-2 text-primary-600"></i>Reference Info</h3>
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Reference Name</label>
 <input type="text" name="reference_name" value="{{ old('reference_name', $booking->reference_name) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Reference Phone</label>
 <input type="tel" name="reference_phone" value="{{ old('reference_phone', $booking->reference_phone) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 </div>

 <!-- Payment Info -->
 <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fas fa-money-bill mr-2 text-primary-600"></i>Payment Info</h3>
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Total Amount ()</label>
 <input type="number" name="total_amount" value="{{ old('total_amount', $booking->total_amount) }}" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Advance Paid ()</label>
 <input type="number" name="advance_payment" value="{{ old('advance_payment', $booking->advance_payment) }}" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Due ()</label>
 <input type="number" name="remaining_payment" value="{{ old('remaining_payment', $booking->remaining_payment) }}" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 bg-gray-100" readonly>
 </div>
 </div>

 <!-- Status -->
 <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fas fa-cog mr-2 text-primary-600"></i>Status</h3>
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Booking Status *</label>
 <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
 <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
 <option value="checked_in" {{ $booking->status == 'checked_in' ? 'selected' : '' }}>Check-In</option>
 <option value="checked_out" {{ $booking->status == 'checked_out' ? 'selected' : '' }}>Check-Out</option>
 <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
 </select>
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Status</label>
 <select name="payment_status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 <option value="pending" {{ $booking->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
 <option value="partial" {{ $booking->payment_status == 'partial' ? 'selected' : '' }}>Partial</option>
 <option value="paid" {{ $booking->payment_status == 'paid' ? 'selected' : '' }}>Fully Paid</option>
 </select>
 </div>
 <div class="md:col-span-2">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
 <textarea name="notes" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('notes', $booking->notes) }}</textarea>
 </div>
 </div>

 <div class="flex gap-4 mt-8">
 <button type="submit" class="bg-primary-600 text-white px-8 py-3 rounded-lg hover:bg-primary-700 transition shadow-lg"><i class="fas fa-save mr-2"></i>Update</button>
 <a href="{{ route('admin.bookings.show', $booking) }}" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition"><i class="fas fa-eye mr-2"></i>Details</a>
 <a href="{{ route('admin.bookings.index') }}" class="bg-gray-400 text-white px-8 py-3 rounded-lg hover:bg-gray-500 transition"><i class="fas fa-list mr-2"></i>List</a>
 </div>
 </form>
 </div>
</div>

<script>
document.querySelector('input[name="total_amount"]').addEventListener('input', updateRemaining);
document.querySelector('input[name="advance_payment"]').addEventListener('input', updateRemaining);

function updateRemaining() {
 const total = parseFloat(document.querySelector('input[name="total_amount"]').value) || 0;
 const advance = parseFloat(document.querySelector('input[name="advance_payment"]').value) || 0;
 document.querySelector('input[name="remaining_payment"]').value = (total - advance).toFixed(2);
}
</script>
@endsection
