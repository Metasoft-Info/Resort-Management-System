@extends('layouts.admin')

@section('content')
<div class="p-6">
 <div class="mb-8">
 <h1 class="text-3xl font-bold text-gray-800">🎪 Convention Booking Edit</h1>
 <p class="text-gray-600 mt-2">Booking Info, Addon, Discount & Payment Update</p>
 </div>

 <form action="{{ route('admin.convention-bookings.update', $conventionBooking) }}" method="POST" id="editForm" onsubmit="return disableEditSubmit(this)">
 @csrf
 @method('PUT')
 
 <!-- Customer & Event Details -->
 <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
 <h2 class="text-2xl font-bold mb-6 text-primary-600 flex items-center">
 <i class="fas fa-user mr-3"></i>Customer Event Description
 </h2>
 
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div>
 <label class="block text-gray-700 font-semibold mb-2">Event Date *</label>
 <input type="date" name="event_date" value="{{ $conventionBooking->event_date->format('Y-m-d') }}" required
 class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600">
 </div>
 <div>
 <label class="block text-gray-700 font-semibold mb-2">Time Slot *</label>
 <select name="time_slot" required
 class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600">
 <option value="morning" {{ $conventionBooking->time_slot == 'morning' ? 'selected' : '' }}>🌅 Morning (8AM - 2PM)</option>
 <option value="night" {{ $conventionBooking->time_slot == 'night' ? 'selected' : '' }}>🌙 Nights (6PM - 11PM)</option>
 <option value="full_day" {{ $conventionBooking->time_slot == 'full_day' ? 'selected' : '' }}>🌞 Full Day (8AM - 11PM)</option>
 </select>
 </div>
 <div>
 <label class="block text-gray-700 font-semibold mb-2">Convention Hall *</label>
 <select name="hall_id" id="hallId" required
 class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600"
 onchange="updateHallRent()">
 @foreach($halls as $hall)
 <option value="{{ $hall->id }}" data-price="{{ $hall->price_per_day }}" {{ $conventionBooking->hall_id == $hall->id ? 'selected' : '' }}>
 {{ $hall->name }} - {{ number_format($hall->price_per_day, 0) }}
 </option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-gray-700 font-semibold mb-2">Hall Rent (BDT) *</label>
 <input type="number" name="hall_rent" id="hallRent" value="{{ $conventionBooking->hall_rent }}" required step="0.01"
 class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600"
 onchange="calculateTotal()">
 </div>
 @if($relatedBookings->count() > 0)
 <div class="md:col-span-2">
 <label class="block text-gray-700 font-semibold mb-2">Other Halls Booked for This Event</label>
 <div class="flex flex-wrap gap-2 p-3 bg-violet-50 rounded-lg border border-violet-200">
 @foreach($relatedBookings as $related)
 <a href="{{ route('admin.convention-bookings.edit', $related) }}" class="px-3 py-1.5 bg-violet-600 text-white rounded-full text-sm font-semibold hover:bg-violet-700 transition">
 <i class="fas fa-building mr-1"></i>{{ $related->conventionHall->name ?? 'N/A' }}
 </a>
 @endforeach
 </div>
 <p class="text-xs text-violet-600 mt-1">Click to edit individual hall booking</p>
 </div>
 @endif
 @if($availableHalls->count() > 0)
 <div class="md:col-span-2">
 <label class="block text-gray-700 font-semibold mb-2">Add More Free Halls for This Event</label>
 <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3" id="additionalHallsGrid">
 @foreach($availableHalls as $hall)
 <label class="flex items-center gap-3 p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-primary-500 hover:bg-primary-50 transition additional-hall-card" data-hall-id="{{ $hall->id }}" data-price="{{ $hall->price_per_day }}">
 <input type="checkbox" name="additional_hall_ids[]" value="{{ $hall->id }}" class="w-5 h-5 text-primary-600 rounded" onchange="toggleAdditionalHall(this, {{ $hall->price_per_day }})">
 <div>
 <div class="font-semibold text-gray-800">{{ $hall->name }}</div>
 <div class="text-primary-600 font-bold text-sm">BDT {{ number_format($hall->price_per_day, 0) }}</div>
 </div>
 </label>
 @endforeach
 </div>
 <p class="text-xs text-gray-500 mt-1">Selected additional halls will be booked with same customer, date and slot</p>
 </div>
 @endif
 <div>
 <label class="block text-gray-700 font-semibold mb-2">Customer Name *</label>
 <input type="text" name="customer_name" value="{{ $conventionBooking->customer_name }}" required
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600">
 </div>
 <div>
 <label class="block text-gray-700 font-semibold mb-2">Phone Number *</label>
 <input type="tel" name="customer_phone" value="{{ $conventionBooking->customer_phone }}" required
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600">
 </div>
 <div>
 <label class="block text-gray-700 font-semibold mb-2">Email</label>
 <input type="email" name="customer_email" value="{{ $conventionBooking->customer_email }}"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600">
 </div>
 <div>
 <label class="block text-gray-700 font-semibold mb-2">WhatsApp</label>
 <input type="tel" name="customer_whatsapp" value="{{ $conventionBooking->customer_whatsapp }}"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600">
 </div>
 <div>
 <label class="block text-gray-700 font-semibold mb-2">NID</label>
 <input type="text" name="customer_nid" value="{{ $conventionBooking->customer_nid }}"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600">
 </div>
 <div>
 <label class="block text-gray-700 font-semibold mb-2">Organization Name</label>
 <input type="text" name="organization_name" value="{{ $conventionBooking->organization_name }}"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600">
 </div>
 <div>
 <label class="block text-gray-700 font-semibold mb-2">Event Type *</label>
 <select name="event_type" required
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600">
 <option value="conference" {{ $conventionBooking->event_type == 'conference' ? 'selected' : '' }}>Conference</option>
 <option value="wedding" {{ $conventionBooking->event_type == 'wedding' ? 'selected' : '' }}>Wedding</option>
 <option value="meeting" {{ $conventionBooking->event_type == 'meeting' ? 'selected' : '' }}>Meeting</option>
 <option value="seminar" {{ $conventionBooking->event_type == 'seminar' ? 'selected' : '' }}>Seminar</option>
 <option value="party" {{ $conventionBooking->event_type == 'party' ? 'selected' : '' }}>Party</option>
 <option value="other" {{ $conventionBooking->event_type == 'other' ? 'selected' : '' }}>Other</option>
 </select>
 </div>
 <div>
 <label class="block text-gray-700 font-semibold mb-2">Guest Count *</label>
 <input type="number" name="number_of_guests" id="numberOfGuests" value="{{ $conventionBooking->number_of_guests }}" min="1" required
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600"
 onchange="updateFoodCost()">
 </div>
 <div class="md:col-span-2">
 <label class="block text-gray-700 font-semibold mb-2">Address</label>
 <textarea name="customer_address" rows="2"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600">{{ $conventionBooking->customer_address }}</textarea>
 </div>
 </div>
 </div>

 <!-- Food Packages -->
 <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
 <h2 class="text-2xl font-bold mb-6 text-primary-600 flex items-center">
 <i class="fas fa-utensils mr-3"></i>Food Package
 </h2>
 <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
 <div class="border-2 border-gray-300 rounded-lg p-4 cursor-pointer hover:border-primary-500 transition {{ !$conventionBooking->food_package_id ? 'border-primary-600 bg-primary-50' : '' }}"
 onclick="selectFoodPackage(0, 'Custom', 0)">
 <input type="radio" name="food_package_id" value="" {{ !$conventionBooking->food_package_id ? 'checked' : '' }} class="mr-3">
 <span class="font-semibold">Custom Food</span>
 </div>
 @foreach($foodPackages as $package)
 <div class="border-2 border-gray-300 rounded-lg p-4 cursor-pointer hover:border-primary-500 transition {{ $conventionBooking->food_package_id == $package->id ? 'border-primary-600 bg-primary-50' : '' }}"
 onclick="selectFoodPackage({{ $package->id }}, '{{ $package->name }}', {{ $package->price_per_person }})">
 <input type="radio" name="food_package_id" value="{{ $package->id }}" {{ $conventionBooking->food_package_id == $package->id ? 'checked' : '' }} class="mr-3">
 <div>
 <div class="font-semibold">{{ $package->name }}</div>
 <div class="text-primary-600 font-bold">{{ number_format($package->price_per_person, 0) }}/person</div>
 </div>
 </div>
 @endforeach
 </div>
 <input type="hidden" name="food_cost" id="foodCost" value="{{ $conventionBooking->food_cost }}">
 </div>

 <!-- Addon Services -->
 <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
 <h2 class="text-2xl font-bold mb-6 text-primary-600 flex items-center">
 <i class="fas fa-plus-circle mr-3"></i>Addon Services
 </h2>
 <div class="flex flex-wrap gap-2 mb-4">
 <button type="button" class="px-4 py-2 rounded-lg bg-primary-600 text-white" onclick="filterAddons('all')">All</button>
 <button type="button" class="px-4 py-2 rounded-lg bg-gray-200" onclick="filterAddons('decoration')">Decoration</button>
 <button type="button" class="px-4 py-2 rounded-lg bg-gray-200" onclick="filterAddons('sound_system')">Sound</button>
 <button type="button" class="px-4 py-2 rounded-lg bg-gray-200" onclick="filterAddons('photography')">Photography</button>
 <button type="button" class="px-4 py-2 rounded-lg bg-gray-200" onclick="filterAddons('catering')">Catering</button>
 <button type="button" class="px-4 py-2 rounded-lg bg-gray-200" onclick="filterAddons('transport')">Transport</button>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
 @php
 $selectedAddons = $conventionBooking->selected_addons ?? [];
 $addonQuantities = $conventionBooking->addon_quantities ?? [];
 @endphp
 @foreach($addonServices as $addon)
 <div class="border-2 border-gray-200 rounded-lg p-4 addon-item" data-category="{{ $addon->category }}">
 <div class="flex items-start mb-2">
 <input type="checkbox" name="selected_addons[]" value="{{ $addon->id }}" 
 {{ in_array($addon->id, $selectedAddons) ? 'checked' : '' }}
 class="mr-3 w-5 h-5" data-price="{{ $addon->price }}" 
 onchange="toggleAddonQuantity({{ $addon->id }}, this.checked)">
 <div class="flex-1">
 <div class="font-semibold">{{ $addon->name }}</div>
 <div class="text-primary-600 font-bold">{{ number_format($addon->price, 0) }}</div>
 </div>
 </div>
 <div class="addon-quantity {{ in_array($addon->id, $selectedAddons) ? '' : 'hidden' }}" id="quantity-{{ $addon->id }}">
 <label class="text-xs">Amount:</label>
 <input type="number" name="addon_quantities[{{ $addon->id }}]" 
 value="{{ $addonQuantities[$addon->id] ?? 1 }}" min="1"
 class="w-20 px-2 py-1 border rounded text-sm" onchange="updateAddonsCost()">
 </div>
 </div>
 @endforeach
 </div>
 <input type="hidden" name="addons_cost" id="addonsCost" value="{{ $conventionBooking->addons_cost }}">
 </div>

 <!-- Discount & VAT -->
 <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
 <h2 class="text-2xl font-bold mb-6 text-primary-600 flex items-center">
 <i class="fas fa-calculator mr-3"></i>Discount VAT
 </h2>
 
 <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
 <div>
 <h3 class="text-lg font-bold mb-3">Discount (Discount)</h3>
 <div class="flex gap-4 mb-3">
 <label class="flex items-center">
 <input type="radio" name="discount_type" value="flat" {{ $conventionBooking->discount_type == 'flat' ? 'checked' : '' }} onclick="calculateTotal()" class="mr-2">
 <span>Flat</span>
 </label>
 <label class="flex items-center">
 <input type="radio" name="discount_type" value="percentage" {{ $conventionBooking->discount_type == 'percentage' ? 'checked' : '' }} onclick="calculateTotal()" class="mr-2">
 <span>Percentage</span>
 </label>
 </div>
 <input type="number" name="discount_value" id="discountValue" value="{{ $conventionBooking->discount_value }}" step="0.01"
 class="w-full px-4 py-3 border rounded-lg" onchange="calculateTotal()"
 placeholder="Discount Amount">
 <input type="hidden" name="discount" id="discountAmount" value="{{ $conventionBooking->discount }}">
 </div>

 <div>
 <h3 class="text-lg font-bold mb-3">VAT (VAT)</h3>
 <label class="flex items-center mb-3">
 <input type="hidden" name="vat_enabled" value="0">
 <input type="checkbox" name="vat_enabled" value="1" id="vatEnabled" {{ $conventionBooking->vat_enabled ? 'checked' : '' }} onchange="calculateTotal()" class="mr-2">
 <span>VAT Add</span>
 </label>
 <div id="vatSection" class="{{ $conventionBooking->vat_enabled ? '' : 'hidden' }}">
 <label class="block mb-2">VAT Percentage (%)</label>
 <input type="number" name="vat_percentage" id="vatPercentage" value="{{ $conventionBooking->vat_percentage }}" step="0.01"
 class="w-full px-4 py-3 border rounded-lg" onchange="calculateTotal()">
 </div>
 <input type="hidden" name="vat_amount" id="vatAmount" value="{{ $conventionBooking->vat_amount }}">
 </div>
 </div>
 </div>

 <!-- Summary & Payments -->
 <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
 <h2 class="text-2xl font-bold mb-6 text-primary-600 flex items-center">
 <i class="fas fa-money-bill-wave mr-3"></i>Payment 
 </h2>
 
 <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
 <div class="bg-gray-50 p-6 rounded-lg">
 <h3 class="text-xl font-bold mb-4">Cost Description</h3>
 <div class="space-y-3">
 <div class="flex justify-between">
 <span>Hall Rent:</span>
 <span class="font-semibold" id="displayHallRent">{{ number_format($conventionBooking->hall_rent, 2) }}</span>
 </div>
 <div class="flex justify-between">
 <span>Food Cost:</span>
 <span class="font-semibold" id="displayFoodCost">{{ number_format($conventionBooking->food_cost, 2) }}</span>
 </div>
 <div class="flex justify-between">
 <span>Addon Cost:</span>
 <span class="font-semibold" id="displayAddonsCost">{{ number_format($conventionBooking->addons_cost, 2) }}</span>
 </div>
 <div class="flex justify-between text-red-600">
 <span>Discount:</span>
 <span class="font-semibold" id="displayDiscount">-{{ number_format($conventionBooking->discount, 2) }}</span>
 </div>
 <div class="flex justify-between">
 <span>VAT:</span>
 <span class="font-semibold" id="displayVat">{{ number_format($conventionBooking->vat_amount, 2) }}</span>
 </div>
 <div class="border-t pt-3">
 <div class="flex justify-between text-lg font-bold text-primary-600">
 <span>Total Amount:</span>
 <span id="displayTotal">{{ number_format($conventionBooking->total_amount, 2) }}</span>
 </div>
 </div>
 </div>
 <input type="hidden" name="total_amount" id="totalAmount" value="{{ $conventionBooking->total_amount }}">
 </div>

 <div class="bg-primary-50 p-6 rounded-lg">
 <h3 class="text-xl font-bold mb-4">Payment Status</h3>
 <div class="space-y-3">
 <div class="flex justify-between">
 <span>Total Amount:</span>
 <span class="font-semibold">{{ number_format($conventionBooking->total_amount, 2) }}</span>
 </div>
 <div class="flex justify-between text-primary-600">
 <span>Paid:</span>
 <span class="font-semibold">{{ number_format($conventionBooking->advance_payment, 2) }}</span>
 </div>
 <div class="flex justify-between text-red-600 text-lg font-bold">
 <span>Remaining:</span>
 <span>{{ number_format($conventionBooking->remaining_payment, 2) }}</span>
 </div>
 <div class="mt-4">
 <span class="px-4 py-2 rounded-full text-sm font-bold
 @if($conventionBooking->payment_status == 'paid') bg-primary-100 text-primary-800
 @elseif($conventionBooking->payment_status == 'partial') bg-yellow-100 text-yellow-800
 @else bg-red-100 text-red-800
 @endif">
 {{ $conventionBooking->payment_status == 'paid' ? '✅ Complete Paid' : ($conventionBooking->payment_status == 'partial' ? '🟡 Partial Paid' : '❌ Paid') }}
 </span>
 </div>
 </div>
 </div>
 </div>
 </div>

 <!-- Notes -->
 <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
 <h2 class="text-2xl font-bold mb-4 text-gray-700 flex items-center">
 <i class="fas fa-sticky-note mr-3"></i>Note
 </h2>
 <textarea name="notes" rows="3"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600"
 placeholder="Additional Note Remarks ...">{{ $conventionBooking->notes }}</textarea>
 </div>

 <input type="hidden" name="payment_method" value="{{ $conventionBooking->payment_method }}">
 <input type="hidden" name="advance_payment" value="{{ $conventionBooking->advance_payment }}">

 <div class="flex gap-4">
 <a href="{{ route('admin.convention-bookings.index') }}" 
 class="flex-1 bg-gray-500 text-white px-8 py-4 rounded-lg hover:bg-gray-600 transition font-semibold text-lg text-center">
 <i class="fas fa-times mr-2"></i>Cancelled
 </a>
 <button type="submit" 
 class="flex-1 bg-gradient-to-r from-primary-600 to-primary-700 text-white px-8 py-4 rounded-lg hover:from-primary-700 hover:to-primary-800 transition font-semibold text-lg">
 <i class="fas fa-save mr-2"></i>Save Changes
 </button>
 </div>
 </form>
</div>

<script>
let selectedFoodPrice = {{ $conventionBooking->food_package_id && $conventionBooking->foodPackage ? $conventionBooking->foodPackage->price_per_person : 0 }};

function updateHallRent() {
 const select = document.getElementById('hallId');
 const selectedOption = select.options[select.selectedIndex];
 const price = parseFloat(selectedOption.dataset.price) || 0;
 document.getElementById('hallRent').value = price;
 document.getElementById('displayHallRent').textContent = 'BDT ' + price.toFixed(2);
 calculateTotal();
}

function toggleAdditionalHall(checkbox, price) {
 const card = checkbox.closest('.additional-hall-card');
 if (checkbox.checked) {
 card.classList.add('border-primary-500', 'bg-primary-50');
 card.classList.remove('border-gray-300');
 } else {
 card.classList.remove('border-primary-500', 'bg-primary-50');
 card.classList.add('border-gray-300');
 }
}

function disableEditSubmit(form) {
 const submitBtn = form.querySelector('button[type="submit"]');
 if (submitBtn) {
 submitBtn.disabled = true;
 submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
 }
 return true;
}

function selectFoodPackage(id, name, pricePerPerson) {
 selectedFoodPrice = pricePerPerson;
 document.querySelectorAll('[name="food_package_id"]').forEach(r => r.checked = false);
 if (id > 0) {
 document.querySelector(`[name="food_package_id"][value="${id}"]`).checked = true;
 } else {
 document.querySelector(`[name="food_package_id"][value=""]`).checked = true;
 }
 updateFoodCost();
}

function updateFoodCost() {
 const guests = parseInt(document.getElementById('numberOfGuests').value) || 0;
 const foodCost = guests * selectedFoodPrice;
 document.getElementById('foodCost').value = foodCost;
 document.getElementById('displayFoodCost').textContent = 'BDT ' + foodCost.toFixed(2);
 calculateTotal();
}

function toggleAddonQuantity(addonId, isChecked) {
 const quantityDiv = document.getElementById(`quantity-${addonId}`);
 if (isChecked) {
 quantityDiv.classList.remove('hidden');
 } else {
 quantityDiv.classList.add('hidden');
 }
 updateAddonsCost();
}

function updateAddonsCost() {
 let total = 0;
 document.querySelectorAll('[name="selected_addons[]"]:checked').forEach(checkbox => {
 const price = parseFloat(checkbox.dataset.price) || 0;
 const addonId = checkbox.value;
 const quantityInput = document.querySelector(`[name="addon_quantities[${addonId}]"]`);
 const quantity = quantityInput ? parseInt(quantityInput.value) || 1 : 1;
 total += price * quantity;
 });
 document.getElementById('addonsCost').value = total;
 document.getElementById('displayAddonsCost').textContent = 'BDT ' + total.toFixed(2);
 calculateTotal();
}

function filterAddons(category) {
 const addons = document.querySelectorAll('.addon-item');
 addons.forEach(addon => {
 if (category === 'all' || addon.dataset.category === category) {
 addon.classList.remove('hidden');
 } else {
 addon.classList.add('hidden');
 }
 });
}

function calculateTotal() {
 const hallRent = parseFloat(document.getElementById('hallRent').value) || 0;
 const foodCost = parseFloat(document.getElementById('foodCost').value) || 0;
 const addonsCost = parseFloat(document.getElementById('addonsCost').value) || 0;
 
 const subtotal = hallRent + foodCost + addonsCost;
 
 // Discount
 const discountType = document.querySelector('[name="discount_type"]:checked').value;
 const discountValue = parseFloat(document.getElementById('discountValue').value) || 0;
 let discount = 0;
 if (discountType === 'percentage') {
 discount = (subtotal * discountValue) / 100;
 } else {
 discount = discountValue;
 }
 document.getElementById('discountAmount').value = discount;
 document.getElementById('displayDiscount').textContent = '-BDT ' + discount.toFixed(2);
 
 const afterDiscount = subtotal - discount;
 
 // VAT
 const vatEnabled = document.getElementById('vatEnabled').checked;
 document.getElementById('vatSection').classList.toggle('hidden', !vatEnabled);
 let vatAmount = 0;
 if (vatEnabled) {
 const vatPercentage = parseFloat(document.getElementById('vatPercentage').value) || 0;
 vatAmount = (afterDiscount * vatPercentage) / 100;
 }
 document.getElementById('vatAmount').value = vatAmount;
 document.getElementById('displayVat').textContent = 'BDT ' + vatAmount.toFixed(2);
 
 // Total
 const total = afterDiscount + vatAmount;
 document.getElementById('totalAmount').value = total;
 document.getElementById('displayTotal').textContent = 'BDT ' + total.toFixed(2);
 
 // Update display values
 document.getElementById('displayHallRent').textContent = 'BDT ' + hallRent.toFixed(2);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
 updateAddonsCost();
 calculateTotal();
});
</script>
@endsection
