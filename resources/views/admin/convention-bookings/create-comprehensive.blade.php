@extends('layouts.admin')
@section('content')
<div class="p-6 max-w-7xl mx-auto">
 <div class="mb-8">
 <h1 class="text-3xl font-bold text-gray-800">New Convention Booking</h1>
 <p class="text-gray-600 mt-2">Complete Booking Information, with Addon Services & Food Package</p>
 </div>

 <form action="{{ route('admin.convention-bookings.store') }}" method="POST" id="bookingForm">
 @csrf
 
 <!-- Customer Information -->
 <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
 <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
 <i class="fas fa-user text-primary-600 mr-3"></i>
 Customer Information
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
 <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
 <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
 <i class="fas fa-calendar-alt text-primary-600 mr-3"></i>
 Event Information
 </h2>
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Convention Hall *</label>
 <select name="hall_id" id="hall_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" onchange="updateHallRent()">
 <option value="">Hall Select</option>
 @foreach($halls as $hall)
 <option value="{{ $hall->id }}" data-price="{{ $hall->price_per_day }}">{{ $hall->name }} (Capacity: {{ $hall->capacity }})</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Event Date *</label>
 <input type="date" name="event_date" id="event_date" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
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
 <input type="number" name="number_of_guests" id="number_of_guests" value="1" min="1" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" onchange="updateFoodCost()">
 </div>
 <div class="md:col-span-3">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Event Description</label>
 <textarea name="event_description" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
 </div>
 </div>
 </div>

 <!-- Food Package Selection -->
 <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
 <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
 <i class="fas fa-utensils text-primary-600 mr-3"></i>
 Food Package
 </h2>
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
 <div class="border-2 border-gray-300 rounded-lg p-4 cursor-pointer hover:border-primary-500 transition" onclick="selectFoodPackage(0, '')">
 <div class="flex items-center">
 <input type="radio" name="selected_food_package_id" value="" class="mr-3">
 <div>
 <div class="font-semibold">Custom / Own</div>
 <div class="text-sm text-gray-600">Own food arrangement</div>
 </div>
 </div>
 </div>
 @foreach($foodPackages as $package)
 <div class="border-2 border-gray-300 rounded-lg p-4 cursor-pointer hover:border-primary-500 transition" onclick="selectFoodPackage({{ $package->id }}, '{{ $package->name }}', {{ $package->price_per_person }})">
 <div class="flex items-center mb-2">
 <input type="radio" name="selected_food_package_id" value="{{ $package->id }}" class="mr-3">
 <div class="flex-1">
 <div class="font-semibold text-gray-800">{{ $package->name }}</div>
 <div class="text-primary-600 font-bold">{{ number_format($package->price_per_person, 0) }}/person</div>
 </div>
 </div>
 @if($package->items)
 <div class="text-xs text-gray-600 mt-2">
 @foreach(json_decode($package->items, true) as $item)
 <div class="flex items-start">
 <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
 <span>{{ $item }}</span>
 </div>
 @endforeach
 </div>
 @endif
 </div>
 @endforeach
 </div>
 </div>

 <!-- Addon Services Selection -->
 <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
 <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
 <i class="fas fa-plus-circle text-primary-600 mr-3"></i>
 Addon Services
 </h2>
 
 <!-- Category Filter -->
 <div class="flex flex-wrap gap-2 mb-6">
 <button type="button" class="category-btn px-4 py-2 rounded-lg font-semibold transition bg-primary-600 text-white" onclick="filterAddons('all')">All</button>
 <button type="button" class="category-btn px-4 py-2 rounded-lg font-semibold transition bg-gray-200 text-gray-700 hover:bg-primary-100" onclick="filterAddons('decoration')"><i class="fas fa-paint-brush mr-2"></i>Decoration</button>
 <button type="button" class="category-btn px-4 py-2 rounded-lg font-semibold transition bg-gray-200 text-gray-700 hover:bg-primary-100" onclick="filterAddons('sound_system')"><i class="fas fa-volume-up mr-2"></i>Sound System</button>
 <button type="button" class="category-btn px-4 py-2 rounded-lg font-semibold transition bg-gray-200 text-gray-700 hover:bg-primary-100" onclick="filterAddons('photography')"><i class="fas fa-camera mr-2"></i>Photography</button>
 <button type="button" class="category-btn px-4 py-2 rounded-lg font-semibold transition bg-gray-200 text-gray-700 hover:bg-primary-100" onclick="filterAddons('catering')"><i class="fas fa-utensils mr-2"></i>Catering</button>
 <button type="button" class="category-btn px-4 py-2 rounded-lg font-semibold transition bg-gray-200 text-gray-700 hover:bg-primary-100" onclick="filterAddons('transport')"><i class="fas fa-car mr-2"></i>Transport</button>
 <button type="button" class="category-btn px-4 py-2 rounded-lg font-semibold transition bg-gray-200 text-gray-700 hover:bg-primary-100" onclick="filterAddons('other')"><i class="fas fa-ellipsis-h mr-2"></i>Other</button>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="addonServices">
 @foreach($addonServices as $addon)
 <div class="border-2 border-gray-200 rounded-lg p-4 addon-item" data-category="{{ $addon->category }}">
 <div class="flex items-start justify-between mb-2">
 <div class="flex items-center flex-1">
 <input type="checkbox" name="selected_addons[]" value="{{ $addon->id }}" class="mr-3 w-5 h-5" data-price="{{ $addon->price }}" onchange="toggleAddonQuantity({{ $addon->id }}, this.checked)">
 <div class="flex-1">
 <div class="font-semibold text-gray-800">{{ $addon->name }}</div>
 <div class="text-primary-600 font-bold">{{ number_format($addon->price, 0) }}</div>
 @if($addon->unit)
 <div class="text-xs text-gray-500">Per {{ $addon->unit }}</div>
 @endif
 </div>
 </div>
 </div>
 @if($addon->description)
 <div class="text-xs text-gray-600 mb-2">{{ Str::limit($addon->description, 80) }}</div>
 @endif
 <div class="addon-quantity hidden" id="quantity-{{ $addon->id }}">
 <label class="text-xs text-gray-700 font-semibold">Amount:</label>
 <input type="number" name="addon_quantities[{{ $addon->id }}]" value="1" min="1" class="w-20 px-2 py-1 border border-gray-300 rounded text-sm" onchange="updateAddonsCost()">
 </div>
 </div>
 @endforeach
 </div>
 </div>

 <!-- Pricing & Payment -->
 <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
 <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
 <i class="fas fa-calculator text-primary-600 mr-3"></i>
 Price Calculation & Payment
 </h2>
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Hall Rent () *</label>
 <input type="number" name="hall_rent" id="hall_rent" value="0" step="0.01" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" onchange="calculateTotal()">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Food Cost ()</label>
 <input type="number" name="food_cost" id="food_cost" value="0" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" onchange="calculateTotal()">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Addon Cost ()</label>
 <input type="number" name="addons_cost" id="addons_cost" value="0" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" readonly>
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Discount ()</label>
 <input type="number" name="discount" id="discount" value="0" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" onchange="calculateTotal()">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">VAT Percentage (%)</label>
 <input type="number" name="vat_percentage" id="vat_percentage" value="0" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" onchange="calculateTotal()">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">VAT Amount ()</label>
 <input type="number" name="vat_amount" id="vat_amount" value="0" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" readonly>
 </div>
 <div class="md:col-span-2 bg-gradient-to-r from-indigo-50 to-primary-50 p-4 rounded-lg border-2 border-indigo-300">
 <div class="flex justify-between items-center">
 <span class="text-lg font-bold text-gray-800">Total Amount:</span>
 <span class="text-2xl font-bold text-primary-600" id="total_display">0.00</span>
 </div>
 <input type="hidden" name="total_amount" id="total_amount" value="0">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Advance Payment () *</label>
 <input type="number" name="advance_payment" id="advance_payment" value="0" step="0.01" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" onchange="calculateRemaining()">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method *</label>
 <select name="payment_method" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
 <option value="cash">Cash</option>
 <option value="card">Card</option>
 <option value="mfs">Mobile Banking</option>
 </select>
 </div>
 <div class="md:col-span-2 bg-yellow-50 p-4 rounded-lg border border-yellow-300">
 <div class="flex justify-between items-center">
 <span class="font-semibold text-gray-700">Remaining Payment:</span>
 <span class="text-xl font-bold text-yellow-600" id="remaining_display">0.00</span>
 </div>
 </div>
 </div>
 </div>

 <!-- Additional Information -->
 <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
 <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
 <i class="fas fa-info-circle text-gray-600 mr-3"></i>
 Additional Information
 </h2>
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

 <!-- Submit Buttons -->
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

<script>
let selectedFoodPackagePrice = 0;

function selectFoodPackage(id, name, pricePerPerson = 0) {
 selectedFoodPackagePrice = pricePerPerson;
 document.querySelectorAll('[name="selected_food_package_id"]').forEach(radio => radio.checked = false);
 document.querySelector(`[name="selected_food_package_id"][value="${id}"]`).checked = true;
 updateFoodCost();
}

function updateFoodCost() {
 const guests = parseInt(document.getElementById('number_of_guests').value) || 0;
 const foodCost = guests * selectedFoodPackagePrice;
 document.getElementById('food_cost').value = foodCost.toFixed(2);
 calculateTotal();
}

function updateHallRent() {
 const hallSelect = document.getElementById('hall_id');
 const timeSlot = document.getElementById('time_slot').value;
 const selectedOption = hallSelect.options[hallSelect.selectedIndex];
 
 if (selectedOption && selectedOption.dataset.price) {
 let price = parseFloat(selectedOption.dataset.price);
 
 // Apply time slot pricing
 if (timeSlot === 'morning' || timeSlot === 'afternoon' || timeSlot === 'evening') {
 price = price * 0.4; // 40% for half day slots
 }
 
 document.getElementById('hall_rent').value = price.toFixed(2);
 calculateTotal();
 }
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
 document.getElementById('addons_cost').value = total.toFixed(2);
 calculateTotal();
}

function calculateTotal() {
 const hallRent = parseFloat(document.getElementById('hall_rent').value) || 0;
 const foodCost = parseFloat(document.getElementById('food_cost').value) || 0;
 const addonsCost = parseFloat(document.getElementById('addons_cost').value) || 0;
 const discount = parseFloat(document.getElementById('discount').value) || 0;
 const vatPercentage = parseFloat(document.getElementById('vat_percentage').value) || 0;
 
 const subtotal = hallRent + foodCost + addonsCost - discount;
 const vatAmount = (subtotal * vatPercentage) / 100;
 const total = subtotal + vatAmount;
 
 document.getElementById('vat_amount').value = vatAmount.toFixed(2);
 document.getElementById('total_amount').value = total.toFixed(2);
 document.getElementById('total_display').textContent = `${total.toFixed(2)}`;
 
 calculateRemaining();
}

function calculateRemaining() {
 const total = parseFloat(document.getElementById('total_amount').value) || 0;
 const advance = parseFloat(document.getElementById('advance_payment').value) || 0;
 const remaining = Math.max(0, total - advance);
 document.getElementById('remaining_display').textContent = `${remaining.toFixed(2)}`;
}

function filterAddons(category) {
 const addons = document.querySelectorAll('.addon-item');
 document.querySelectorAll('.category-btn').forEach(btn => {
 btn.classList.remove('bg-primary-600', 'text-white');
 btn.classList.add('bg-gray-200', 'text-gray-700');
 });
 event.target.classList.remove('bg-gray-200', 'text-gray-700');
 event.target.classList.add('bg-primary-600', 'text-white');
 
 addons.forEach(addon => {
 if (category === 'all' || addon.dataset.category === category) {
 addon.classList.remove('hidden');
 } else {
 addon.classList.add('hidden');
 }
 });
}

// Initialize
calculateTotal();
</script>
@endsection
