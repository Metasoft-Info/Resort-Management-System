@extends('layouts.admin')
@section('content')
<div class="p-6">
 <div class="mb-8"><h1 class="text-3xl font-bold text-gray-800">New Convention Booking</h1></div>
 <div class="bg-white rounded-xl shadow-lg p-8">
 <form action="{{ route('admin.convention-bookings.store') }}" method="POST">
 @csrf
 
 <!-- Customer Information -->
 <div class="mb-6">
 <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
 <i class="fas fa-user text-primary-600 mr-3"></i>Customer Information
 </h2>
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Customer Name *</label>
 <input type="text" name="customer_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number *</label>
 <input type="tel" name="customer_phone" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">WhatsApp</label>
 <input type="tel" name="customer_whatsapp" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">NID Number</label>
 <input type="text" name="customer_nid" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
 <input type="email" name="customer_email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Organization Name</label>
 <input type="text" name="organization_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div class="md:col-span-3">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
 <textarea name="customer_address" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"></textarea>
 </div>
 </div>
 </div>

 <!-- Event Information -->
 <div class="mb-6">
 <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
 <i class="fas fa-calendar-alt text-primary-600 mr-3"></i>Event Information
 </h2>
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Convention Hall *</label>
 <select name="hall_id" id="hall_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" onchange="updateHallRent()">
 <option value="">Hall Select</option>
 @foreach($halls as $hall)
 <option value="{{ $hall->id }}" data-price="{{ $hall->price_per_day }}">{{ $hall->name }} (Capacity: {{ $hall->max_capacity }})</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Event Date *</label>
 <input type="date" name="event_date" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Time Slot *</label>
 <select name="time_slot" id="time_slot" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" onchange="updateHallRent()">
 <option value="">Time Select</option>
 <option value="morning">Morning (8AM - 2PM)</option>
 <option value="night">Nights (6PM - 11PM)</option>
 <option value="full_day">Full Day (8AM - 11PM)</option>
 </select>
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Event Type *</label>
 <input type="text" name="event_type" required placeholder="Wedding, Conference, Seminar" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Guest Count *</label>
 <input type="number" name="number_of_guests" value="1" min="1" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
 </div>
 <div class="md:col-span-3">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Event Description</label>
 <textarea name="event_description" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
 </div>
 </div>
 </div>

 <!-- Pricing -->
 <div class="mb-6">
 <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
 <i class="fas fa-calculator text-primary-600 mr-3"></i>Price Information
 </h2>
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Hall Rent (BDT) *</label>
 <input type="number" name="hall_rent" id="hall_rent" value="0" step="0.01" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" onchange="calculateTotal()">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Discount (BDT)</label>
 <input type="number" name="discount" id="discount" value="0" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" onchange="calculateTotal()">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Advance Payment (BDT)</label>
 <input type="number" name="advance_payment" id="advance_payment" value="0" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" onchange="calculateTotal()">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method *</label>
 <select name="payment_method" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
 <option value="cash">Cash</option>
 <option value="card">Card</option>
 <option value="mfs">Mobile Banking</option>
 </select>
 </div>
 </div>
 </div>

 <!-- Status & Notes -->
 <div class="mb-6">
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
 <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500">
 <option value="pending">Pending</option>
 <option value="confirmed" selected>Confirmed</option>
 <option value="completed">Completed</option>
 <option value="cancelled">Cancelled</option>
 </select>
 </div>
 <div class="md:col-span-2">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Additional Note</label>
 <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500"></textarea>
 </div>
 </div>
 </div>

 <input type="hidden" name="food_cost" id="food_cost" value="0">
 <input type="hidden" name="addons_cost" id="addons_cost" value="0">
 <input type="hidden" name="vat_enabled" value="0">
 <input type="hidden" name="vat_percentage" id="vat_percentage" value="0">
 <input type="hidden" name="vat_amount" id="vat_amount" value="0">
 <input type="hidden" name="total_amount" id="total_amount" value="0">

 <!-- Live Summary -->
 <div class="mb-6">
 <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
 <i class="fas fa-receipt text-primary-600 mr-3"></i>Booking Summary
 </h2>
 <div class="bg-gray-50 rounded-xl p-6 max-w-md">
 <div class="space-y-3">
 <div class="flex justify-between">
 <span>Hall Rent:</span>
 <span class="font-semibold" id="display_hall_rent">0</span>
 </div>
 <div class="flex justify-between text-red-600">
 <span>Discount:</span>
 <span class="font-semibold" id="display_discount">0</span>
 </div>
 <div class="border-t pt-3">
 <div class="flex justify-between text-lg font-bold text-primary-600">
 <span>Total Amount:</span>
 <span id="display_total">0</span>
 </div>
 </div>
 <div class="flex justify-between">
 <span>Advance Payment:</span>
 <span class="font-semibold" id="display_advance">0</span>
 </div>
 <div class="flex justify-between text-red-600 font-bold">
 <span>Remaining:</span>
 <span id="display_remaining">0</span>
 </div>
 </div>
 </div>
 </div>

 <div class="flex gap-4">
 <button type="submit" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-8 py-4 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg font-semibold text-lg">
 <i class="fas fa-save mr-2"></i>Booking Save
 </button>
 <a href="{{ route('admin.convention-bookings.index') }}" class="bg-gray-500 text-white px-8 py-4 rounded-lg hover:bg-gray-600 transition font-semibold text-lg">
 <i class="fas fa-times mr-2"></i>Cancelled
 </a>
 </div>
 </form>
 </div>
</div>

<script>
function updateHallRent() {
 const hallSelect = document.getElementById('hall_id');
 const selectedOption = hallSelect.options[hallSelect.selectedIndex];
 
 if (selectedOption && selectedOption.dataset.price) {
 let price = parseFloat(selectedOption.dataset.price);
 document.getElementById('hall_rent').value = price.toFixed(2);
 calculateTotal();
 }
}

function calculateTotal() {
 const hallRent = parseFloat(document.getElementById('hall_rent').value) || 0;
 const discount = parseFloat(document.getElementById('discount').value) || 0;
 const advance = parseFloat(document.getElementById('advance_payment').value) || 0;
 const total = Math.max(0, hallRent - discount);
 const remaining = Math.max(0, total - advance);
 
 document.getElementById('total_amount').value = total.toFixed(2);
 document.getElementById('display_hall_rent').textContent = 'BDT ' + hallRent.toFixed(2);
 document.getElementById('display_discount').textContent = '-BDT ' + discount.toFixed(2);
 document.getElementById('display_advance').textContent = 'BDT ' + advance.toFixed(2);
 document.getElementById('display_total').textContent = 'BDT ' + total.toFixed(2);
 document.getElementById('display_remaining').textContent = 'BDT ' + remaining.toFixed(2);
}

document.addEventListener('DOMContentLoaded', function() {
 calculateTotal();
 document.getElementById('advance_payment').addEventListener('input', calculateTotal);
});
</script>
@endsection
