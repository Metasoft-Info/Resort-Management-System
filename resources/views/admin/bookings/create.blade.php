@extends('layouts.admin')
@section('content')
<div class="p-6">
 <div class="mb-8"><h1 class="text-3xl font-bold text-gray-800">New Booking</h1></div>
 <div class="bg-white rounded-xl shadow-lg p-8">
 <form action="{{ route('admin.bookings.store') }}" method="POST">
 @csrf
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Room *</label>
 <select name="room_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 <option value="">Select Room</option>
 @foreach(\App\Models\Room::where('status', 'available')->get() as $room)
 <option value="{{ $room->id }}">{{ $room->room_number }} - {{ $room->roomType->name ?? 'N/A' }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Booking Purpose</label>
 <select name="booking_purpose" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 <option value="">{{ __('admin.booking.purpose_others') }}</option>
 <option value="company" {{ old('booking_purpose') == 'company' ? 'selected' : '' }}>{{ __('admin.booking.purpose_company') }}</option>
 <option value="family" {{ old('booking_purpose') == 'family' ? 'selected' : '' }}>{{ __('admin.booking.purpose_family') }}</option>
 <option value="wedding" {{ old('booking_purpose') == 'wedding' ? 'selected' : '' }}>{{ __('admin.booking.purpose_wedding') }}</option>
 <option value="single" {{ old('booking_purpose') == 'single' ? 'selected' : '' }}>{{ __('admin.booking.purpose_single') }}</option>
 <option value="others" {{ old('booking_purpose') == 'others' ? 'selected' : '' }}>{{ __('admin.booking.purpose_others') }}</option>
 </select>
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Check-In Date *</label>
 <input type="date" name="check_in_date" value="{{ old('check_in_date') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Check-Out Date *</label>
 <input type="date" name="check_out_date" value="{{ old('check_out_date') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Customer Name *</label>
 <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number *</label>
 <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
 <input type="email" name="customer_email" value="{{ old('customer_email') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">NID Number *</label>
 <input type="text" name="customer_nid" value="{{ old('customer_nid') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Number of Guests *</label>
 <input type="number" name="number_of_guests" value="{{ old('number_of_guests', 1) }}" min="1" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Total Amount () *</label>
 <input type="number" name="total_amount" value="{{ old('total_amount') }}" step="0.01" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Advance Payment () *</label>
 <input type="number" name="advance_payment" value="{{ old('advance_payment', 0) }}" step="0.01" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method *</label>
 <select name="payment_method" id="paymentMethod" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 <option value="cash">Cash</option>
 <option value="card">Card</option>
 <option value="mfs">Mobile Banking</option>
 <option value="bkash">bKash</option>
 </select>
 </div>
 <div id="bkashField" class="hidden">
 <label class="block text-sm font-semibold text-gray-700 mb-2">bKash Number</label>
 <input type="text" name="bkash_number" value="{{ old('bkash_number') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
 <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 <option value="pending">Pending</option>
 <option value="confirmed" selected>Confirmed</option>
 <option value="checked_in">Check-In</option>
 <option value="checked_out">Check-Out</option>
 <option value="cancelled">Cancelled</option>
 </select>
 </div>
 <div class="md:col-span-2">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
 <textarea name="customer_address" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('customer_address') }}</textarea>
 </div>
 <div class="md:col-span-2">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Special Request</label>
 <textarea name="notes" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('notes') }}</textarea>
 </div>
 </div>
 <div class="flex gap-4 mt-8">
 <button type="submit" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-8 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg"><i class="fas fa-save mr-2"></i>Save</button>
 <a href="{{ route('admin.bookings.index') }}" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition"><i class="fas fa-times mr-2"></i>Cancelled</a>
 </div>
 </form>
 </div>
</div>

<script>
document.getElementById('paymentMethod').addEventListener('change', function() {
    const bkashField = document.getElementById('bkashField');
    if (this.value === 'bkash') {
        bkashField.classList.remove('hidden');
    } else {
        bkashField.classList.add('hidden');
    }
});
</script>
@endsection
