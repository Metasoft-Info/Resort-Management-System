@extends('layouts.admin')

@section('title', 'Convention Hall Booking')
@section('header', 'Convention Hall Booking')

@section('content')
<div class="p-2 lg:p-6">
 <!-- Premium Header -->
 <div class="bg-gradient-to-r from-violet-600 via-purple-600 to-violet-700 rounded-2xl shadow-xl p-6 mb-8">
 <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
 <div class="text-white">
 <h1 class="text-2xl lg:text-3xl font-bold flex items-center">
 <i class="fas fa-building-columns mr-3 text-violet-200"></i>
 Convention Hall Booking
 </h1>
 <p class="text-violet-100 mt-2 text-sm lg:text-base">Complete Convention Hall Booking - Food Package with Addon Services</p>
 </div>
 <a href="{{ route('admin.convention-bookings.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-white/20 text-white border border-white/30 rounded-xl hover:bg-white/30 transition font-semibold">
 <i class="fas fa-list mr-2"></i>
 All Bookings View
 </a>
 </div>
 </div>

 <!-- Progress Steps -->
 <div class="flex justify-between mb-8 max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-4">
 <div class="flex items-center flex-1">
 <div class="flex flex-col items-center flex-1">
 <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg bg-violet-600 text-white shadow-lg" id="step1-circle">1</div>
 <span class="text-sm mt-2 font-semibold text-gray-700">Hall Event</span>
 </div>
 </div>
 <div class="flex items-center flex-1">
 <div class="flex flex-col items-center flex-1">
 <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg bg-gray-200 text-gray-500" id="step2-circle">2</div>
 <span class="text-sm mt-2 font-semibold text-gray-500">Food & Service</span>
 </div>
 </div>
 <div class="flex items-center flex-1">
 <div class="flex flex-col items-center flex-1">
 <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg bg-gray-200 text-gray-500" id="step3-circle">3</div>
 <span class="text-sm mt-2 font-semibold text-gray-500">Payment</span>
 </div>
 </div>
 </div>

 <form action="{{ route('admin.convention-bookings.store') }}" method="POST" id="bookingForm" class="max-w-6xl mx-auto" enctype="multipart/form-data" novalidate onsubmit="return validateForm()">
 @csrf
 
 <!-- Step 1: Hall & Event Details -->
 <div id="step1" class="bg-white rounded-2xl shadow-xl p-6 lg:p-8">
 <h2 class="text-xl lg:text-2xl font-bold mb-6 text-violet-600 flex items-center">
 <i class="fas fa-building mr-3"></i>Hall & Event Details
 </h2>
 
 <!-- Date & Time First -->
 <div class="bg-violet-50 border-2 border-violet-200 rounded-xl p-4 lg:p-6 mb-6">
 <h3 class="text-lg font-bold text-violet-800 mb-4 flex items-center">
 <i class="fas fa-calendar mr-2"></i>Select Date & Time first
 </h3>
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
 <div>
 <label class="block text-gray-700 font-semibold mb-2">Event Date *</label>
 <input type="date" id="eventDate" name="event_date" required min="{{ date('Y-m-d') }}"
 class="w-full px-4 py-3 border-2 border-violet-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500"
 onchange="checkAvailability()">
 </div>
 <div>
 <label class="block text-gray-700 font-semibold mb-2">Time Slot *</label>
 <select id="timeSlot" name="time_slot" required
 class="w-full px-4 py-3 border-2 border-violet-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500"
 onchange="checkAvailability()">
 <option value="">Time Select</option>
 <option value="morning">🌅 Morning (8AM - 2PM)</option>
 <option value="night">🌙 Nights (6PM - 11PM)</option>
 <option value="full_day">🌞 Full Day (8AM - 11PM)</option>
 </select>
 </div>
 </div>
 </div>

 <!-- Available Halls -->
 <div id="hallsContainer" class="hidden mb-6">
 <label class="block text-gray-700 font-semibold mb-4">
 Available Convention Hall *
 <span class="ml-2 text-sm text-violet-600 font-normal">✅ Showing available halls for selected date & time</span>
 </label>
 <div id="hallsList" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
 <input type="hidden" name="hall_id" id="selectedHallId">
 <input type="hidden" name="hall_rent" id="hallRent" value="0">
 </div>

 <!-- Customer Info -->
 <div class="space-y-6">
 <div>
 <label class="block text-gray-700 font-semibold mb-2">
 📱 Phone Number *
 <span id="customerFound" class="ml-2 text-violet-600 text-sm font-bold hidden">✅ Customer found!</span>
 </label>
 <input type="tel" name="customer_phone" id="customerPhone" required
 class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500"
 placeholder="Enter Phone Number (existing customer info will auto-fill)"
 onblur="searchCustomer(this.value)">
 <p class="text-sm text-gray-500 mt-1">💡 Enter Phone and press Tab - existing customer info will auto-fill</p>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div>
 <label class="block text-gray-700 font-semibold mb-2">Customer Name *</label>
 <input type="text" name="customer_name" id="customerName" required
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500">
 </div>
 <div>
 <label class="block text-gray-700 font-semibold mb-2">Organization Name</label>
 <input type="text" name="organization_name" id="organizationName"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500">
 </div>
 <div>
 <label class="block text-gray-700 font-semibold mb-2">Email</label>
 <input type="email" name="customer_email" id="customerEmail"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500">
 </div>
 <div>
 <label class="block text-gray-700 font-semibold mb-2">WhatsApp</label>
 <input type="tel" name="customer_whatsapp" id="customerWhatsapp"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500">
 </div>
 <div>
 <label class="block text-gray-700 font-semibold mb-2">NID Number</label>
 <input type="text" name="customer_nid" id="customerNid"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500">
 </div>
 <div>
 <label class="block text-gray-700 font-semibold mb-2">Event Type *</label>
 <select name="event_type" required
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500">
 <option value="conference">Conference</option>
 <option value="wedding">Wedding</option>
 <option value="meeting">Meeting</option>
 <option value="seminar">Seminar</option>
 <option value="party">Party</option>
 <option value="other">Other</option>
 </select>
 </div>
 <div>
 <label class="block text-gray-700 font-semibold mb-2">Guest Count *</label>
 <input type="number" name="number_of_guests" id="numberOfGuests" min="1"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500"
 onchange="updateFoodCost()">
 </div>
 <div class="md:col-span-2">
 <label class="block text-gray-700 font-semibold mb-2">Address</label>
 <textarea name="customer_address" id="customerAddress" rows="2"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500"></textarea>
 </div>
 </div>
 </div>

 <div class="flex justify-end mt-8">
 <button type="button" onclick="nextStep(2)" class="bg-gradient-to-r from-violet-600 to-purple-600 text-white px-8 py-3 rounded-xl hover:from-violet-700 hover:to-purple-700 transition font-semibold shadow-lg">
 Next Step <i class="fas fa-arrow-right ml-2"></i>
 </button>
 </div>
 </div>

 <!-- Step 2: Food & Addon Services -->
 <div id="step2" class="bg-white rounded-2xl shadow-xl p-6 lg:p-8 hidden">
 <h2 class="text-xl lg:text-2xl font-bold mb-6 text-violet-600 flex items-center">
 <i class="fas fa-utensils mr-3"></i>Food & Addon Services
 </h2>

 <!-- Food Packages -->
 <div class="mb-8">
 <h3 class="text-xl font-bold mb-4 text-gray-700">Food Package</h3>
 <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
 <div class="border-2 border-gray-200 rounded-xl p-4 cursor-pointer hover:border-violet-400 hover:bg-violet-50 transition"
 onclick="selectFoodPackage(0, 'Custom', 0)">
 <input type="radio" name="selected_food_package_id" value="" class="mr-3">
 <span class="font-semibold">Custom Food</span>
 <p class="text-sm text-gray-600">Custom Food </p>
 </div>
 @foreach($foodPackages as $package)
 <div class="border-2 border-gray-300 rounded-lg p-4 cursor-pointer hover:border-primary-500 transition"
 onclick="selectFoodPackage({{ $package->id }}, '{{ $package->name }}', {{ $package->price_per_person }})">
 <input type="radio" name="selected_food_package_id" value="{{ $package->id }}" class="mr-3">
 <div>
 <div class="font-semibold">{{ $package->name }}</div>
 <div class="text-violet-600 font-bold">{{ number_format($package->price_per_person, 0) }}/person</div>
 </div>
 </div>
 @endforeach
 </div>
 <input type="hidden" name="food_cost" id="foodCost" value="0">
 </div>

 <!-- Addon Services -->
 <div class="mb-8">
 <h3 class="text-xl font-bold mb-4 text-gray-700">Addon Services</h3>
 <div class="flex flex-wrap gap-2 mb-4">
 <button type="button" class="px-4 py-2 rounded-lg bg-violet-600 text-white" onclick="filterAddons('all')">All</button>
 <button type="button" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-violet-100" onclick="filterAddons('decoration')">Decoration</button>
 <button type="button" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-violet-100" onclick="filterAddons('sound_system')">Sound</button>
 <button type="button" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-violet-100" onclick="filterAddons('photography')">Photography</button>
 <button type="button" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-violet-100" onclick="filterAddons('catering')">Catering</button>
 <button type="button" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-violet-100" onclick="filterAddons('transport')">Transport</button>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="addonsList">
 @foreach($addonServices as $addon)
 <div class="border-2 border-gray-200 rounded-xl p-4 addon-item hover:border-violet-300 hover:bg-violet-50 transition" data-category="{{ $addon->category }}">
 <div class="flex items-start mb-2">
 <input type="checkbox" name="selected_addons[]" value="{{ $addon->id }}" class="mr-3 w-5 h-5 text-violet-600"
 data-price="{{ $addon->price }}" onchange="toggleAddonQuantity({{ $addon->id }}, this.checked)">
 <div class="flex-1">
 <div class="font-semibold">{{ $addon->name }}</div>
 <div class="text-violet-600 font-bold">{{ number_format($addon->price, 0) }}</div>
 </div>
 </div>
 <div class="addon-quantity hidden" id="quantity-{{ $addon->id }}">
 <label class="text-xs">Amount:</label>
 <input type="number" name="addon_quantities[{{ $addon->id }}]" value="1" min="1"
 class="w-20 px-2 py-1 border rounded text-sm" onchange="updateAddonsCost()">
 </div>
 </div>
 @endforeach
 </div>
 <input type="hidden" name="addons_cost" id="addonsCost" value="0">
 </div>

 <div class="flex justify-between mt-8">
 <button type="button" onclick="nextStep(1)" class="bg-gray-500 text-white px-8 py-3 rounded-xl hover:bg-gray-600 transition font-semibold">
 <i class="fas fa-arrow-left mr-2"></i>Previous
 </button>
 <button type="button" onclick="nextStep(3)" class="bg-gradient-to-r from-violet-600 to-purple-600 text-white px-8 py-3 rounded-xl hover:from-violet-700 hover:to-purple-700 transition font-semibold shadow-lg">
 Next Step <i class="fas fa-arrow-right ml-2"></i>
 </button>
 </div>
 </div>

 <!-- Step 3: Payment & Summary -->
 <div id="step3" class="bg-white rounded-2xl shadow-xl p-6 lg:p-8 hidden">
 <h2 class="text-xl lg:text-2xl font-bold mb-6 text-violet-600 flex items-center">
 <i class="fas fa-calculator mr-3"></i>Payment & Summary
 </h2>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
 <!-- Left: Pricing Details -->
 <div class="space-y-6">
 <!-- Discount -->
 <div>
 <h3 class="text-lg font-bold mb-3">Discount (Discount)</h3>
 <div class="flex gap-4 mb-3">
 <label class="flex items-center">
 <input type="radio" name="discount_type" value="flat" checked onclick="calculateTotal()" class="mr-2">
 <span>Flat</span>
 </label>
 <label class="flex items-center">
 <input type="radio" name="discount_type" value="percentage" onclick="calculateTotal()" class="mr-2">
 <span>Percentage</span>
 </label>
 </div>
 <input type="number" name="discount_value" id="discountValue" value="0" step="0.01"
 class="w-full px-4 py-3 border rounded-lg" onchange="calculateTotal()"
 placeholder="Discount Amount ">
 <input type="hidden" name="discount" id="discountAmount" value="0">
 </div>

 <!-- VAT -->
 <div>
 <h3 class="text-lg font-bold mb-3">VAT (VAT)</h3>
 <label class="flex items-center mb-3">
 <input type="checkbox" id="vatEnabled" onchange="calculateTotal()" class="mr-2">
 <span>VAT Add</span>
 </label>
 <div id="vatSection" class="hidden">
 <label class="block mb-2">VAT Percentage (%)</label>
 <input type="number" name="vat_percentage" id="vatPercentage" value="15" step="0.01"
 class="w-full px-4 py-3 border rounded-lg" onchange="calculateTotal()">
 </div>
 <input type="hidden" name="vat_amount" id="vatAmount" value="0">
 </div>

 <!-- Advance Payment -->
 <div>
 <h3 class="text-lg font-bold mb-3">Advance Payment</h3>
 <input type="number" name="advance_payment" id="advancePayment" value="0" step="0.01"
 class="w-full px-4 py-3 border rounded-lg" onchange="calculateTotal()">
 </div>

 <div>
 <h3 class="text-lg font-bold mb-3">Payment Method</h3>
 <select name="payment_method" id="payment_method" class="w-full px-4 py-3 border rounded-lg" onchange="togglePaymentFields()">
 <option value="cash">Cash</option>
 <option value="bkash">bKash</option>
 <option value="card">Card</option>
 </select>
 </div>
 <div id="bkash_field" class="hidden">
 <h3 class="text-lg font-bold mb-3">bKash Number</h3>
 <input type="text" name="bkash_number" id="bkash_number" placeholder="01XXXXXXXXX" class="w-full px-4 py-3 border rounded-lg">
 </div>
 <div id="bank_field" class="hidden">
 <h3 class="text-lg font-bold mb-3">Bank Name</h3>
 <select name="bank_name" id="bank_name" class="w-full px-4 py-3 border rounded-lg">
 <option value="">Select Bank</option>
 <option value="Pubali Bank">Pubali Bank</option>
 <option value="City Bank">City Bank</option>
 <option value="Sonali Bank">Sonali Bank</option>
 <option value="Janata Bank">Janata Bank</option>
 <option value="Agrani Bank">Agrani Bank</option>
 <option value="Rupali Bank">Rupali Bank</option>
 <option value="Islami Bank">Islami Bank</option>
 <option value="Dutch-Bangla Bank">Dutch-Bangla Bank</option>
 <option value="BRAC Bank">BRAC Bank</option>
 <option value="UCB">UCB</option>
 <option value="Other">Other</option>
 </select>
 </div>
 </div>

 <!-- Document Upload -->
 <div class="mt-6">
 <h3 class="text-lg font-bold mb-3">Documents (Optional)</h3>
 <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Photo</label>
 <input type="file" name="customer_photo[]" accept="image/*" multiple class="w-full px-3 py-2 border rounded-lg text-sm">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">NID Document</label>
 <input type="file" name="customer_nid_document[]" accept="image/*,.pdf" multiple class="w-full px-3 py-2 border rounded-lg text-sm">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Passport</label>
 <input type="file" name="passport_document[]" accept="image/*,.pdf" multiple class="w-full px-3 py-2 border rounded-lg text-sm">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Visiting Card</label>
 <input type="file" name="visiting_card[]" accept="image/*,.pdf" multiple class="w-full px-3 py-2 border rounded-lg text-sm">
 </div>
 </div>
 </div>

 <!-- Right: Summary -->
 <div class="bg-gradient-to-br from-violet-50 to-purple-50 p-6 rounded-xl border border-violet-100">
 <h3 class="text-xl font-bold mb-4 text-violet-800">Booking Summary</h3>
 <div class="space-y-3">
 <div class="flex justify-between">
 <span>Hall Rent:</span>
 <span class="font-semibold" id="displayHallRent">BDT 0</span>
 </div>
 <div class="flex justify-between">
 <span>Food Cost:</span>
 <span class="font-semibold" id="displayFoodCost">BDT 0</span>
 </div>
 <div class="flex justify-between">
 <span>Addon Cost:</span>
 <span class="font-semibold" id="displayAddonsCost">BDT 0</span>
 </div>
 <div class="flex justify-between text-red-600">
 <span>Discount:</span>
 <span class="font-semibold" id="displayDiscount">-BDT 0</span>
 </div>
 <div class="flex justify-between">
 <span>VAT:</span>
 <span class="font-semibold" id="displayVat">BDT 0</span>
 </div>
 <div class="border-t border-violet-200 pt-3">
 <div class="flex justify-between text-lg font-bold text-violet-700">
 <span>Total Amount:</span>
 <span id="displayTotal">BDT 0</span>
 </div>
 </div>
 <div class="flex justify-between text-violet-600">
 <span>Advance Payment:</span>
 <span class="font-semibold" id="displayAdvance">BDT 0</span>
 </div>
 <div class="flex justify-between text-lg font-bold text-red-600">
 <span>Remaining:</span>
 <span id="displayRemaining">BDT 0</span>
 </div>
 </div>
 </div>
 </div>

 <input type="hidden" name="total_amount" id="totalAmount" value="0">
 <input type="hidden" name="status" value="confirmed">

 <div class="flex justify-between mt-8">
 <button type="button" onclick="nextStep(2)" class="bg-gray-500 text-white px-8 py-3 rounded-xl hover:bg-gray-600 transition font-semibold">
 <i class="fas fa-arrow-left mr-2"></i>Previous
 </button>
 <button type="submit" class="bg-gradient-to-r from-violet-600 to-purple-600 text-white px-8 py-4 rounded-xl hover:from-violet-700 hover:to-purple-700 transition font-semibold text-lg shadow-lg">
 <i class="fas fa-check mr-2"></i>Confirm Booking
 </button>
 </div>
 </div>
 </form>
</div>

<script>
let selectedFoodPrice = 0;

function togglePaymentFields() {
 const method = document.getElementById('payment_method').value;
 const bkashField = document.getElementById('bkash_field');
 const bankField = document.getElementById('bank_field');
 
 bkashField.classList.add('hidden');
 bankField.classList.add('hidden');
 
 if (method === 'bkash') {
 bkashField.classList.remove('hidden');
 } else if (method === 'card') {
 bankField.classList.remove('hidden');
 }
}

function validateForm() {
 const requiredFields = [
 { id: 'eventDate', name: 'Event Date' },
 { id: 'timeSlot', name: 'Time Slot' },
 { id: 'selectedHallId', name: 'Convention Hall' },
 { id: 'customerPhone', name: 'Phone Number' },
 { id: 'customerName', name: 'Customer Name' },
 { id: 'numberOfGuests', name: 'Guest Count' }
 ];
 
 let errors = [];
 
 for (let field of requiredFields) {
 const el = document.getElementById(field.id);
 if (!el || !el.value || el.value.trim() === '') {
 errors.push(field.name);
 }
 }
 
 if (errors.length > 0) {
 alert('Please fill in the following information::\n\n• ' + errors.join('\n• '));
 return false;
 }
 
 return true;
}

function nextStep(step) {
 // Hide all steps
 document.getElementById('step1').classList.add('hidden');
 document.getElementById('step2').classList.add('hidden');
 document.getElementById('step3').classList.add('hidden');
 
 // Reset step circles
 document.getElementById('step1-circle').classList.remove('bg-violet-600', 'text-white', 'shadow-lg');
 document.getElementById('step1-circle').classList.add('bg-gray-200', 'text-gray-500');
 document.getElementById('step2-circle').classList.remove('bg-violet-600', 'text-white', 'shadow-lg');
 document.getElementById('step2-circle').classList.add('bg-gray-200', 'text-gray-500');
 document.getElementById('step3-circle').classList.remove('bg-violet-600', 'text-white', 'shadow-lg');
 document.getElementById('step3-circle').classList.add('bg-gray-200', 'text-gray-500');
 
 // Show selected step
 document.getElementById('step' + step).classList.remove('hidden');
 document.getElementById('step' + step + '-circle').classList.remove('bg-gray-200', 'text-gray-500');
 document.getElementById('step' + step + '-circle').classList.add('bg-violet-600', 'text-white', 'shadow-lg');
 
 if (step === 3) {
 calculateTotal();
 }
 window.scrollTo(0, 0);
}

async function checkAvailability() {
 const date = document.getElementById('eventDate').value;
 const slot = document.getElementById('timeSlot').value;
 
 if (!date || !slot) return;
 
 try {
 const response = await fetch('{{ route("admin.premium-convention.search") }}', {
 method: 'POST',
 headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
 body: JSON.stringify({date, slot})
 });
 
 const data = await response.json();
 console.log('Search response:', data); // Debug log
 const container = document.getElementById('hallsList');
 container.innerHTML = '';
 
 if (data.availableHalls.length === 0) {
 container.innerHTML = '<div class="col-span-2 bg-red-50 border-2 border-red-300 rounded-lg p-6 text-center"><p class="text-red-800 font-semibold">❌ Hall </p></div>';
 } else {
 data.availableHalls.forEach(hall => {
 console.log('Hall data:', hall); // Debug log
 const images = hall.images || [];
 const firstImage = images.length > 0 ? images[0] : null;
 const hallCapacity = hall.capacity || hall.max_capacity || 0;
 const imageHtml = firstImage 
 ? `<div class="relative h-40 bg-gray-100 overflow-hidden">
 <img src="/storage/${firstImage}" alt="${hall.name}" class="w-full h-full object-cover">
 ${images.length > 1 ? `<span class="absolute top-2 right-2 bg-black/50 text-white text-xs px-2 py-1 rounded">${images.length}images</span>` : ''}
 </div>`
 : `<div class="h-40 bg-gradient-to-br from-primary-100 to-primary-100 flex items-center justify-center">
 <i class="fas fa-building text-5xl text-purple-300"></i>
 </div>`;
 
 container.innerHTML += `
 <div class="relative border-2 border-gray-300 rounded-xl overflow-hidden cursor-pointer hover:border-purple-400 hover:shadow-lg transition hall-card"
 data-hall-id="${hall.id}" data-price="${hall.price_per_day}"
 onclick="selectHall(${hall.id}, ${hall.price_per_day}, '${slot}')">
 ${imageHtml}
 <div class="p-4">
 <h4 class="font-bold text-lg text-gray-800">${hall.name}</h4>
 <p class="text-sm text-gray-600 mt-1"><i class="fas fa-users mr-1"></i>Capacity: ${hallCapacity} people</p>
 <p class="text-xl font-bold text-violet-600 mt-2">BDT ${Number(hall.price_per_day).toLocaleString()}/day</p>
 <span class="inline-block mt-3 px-3 py-1 bg-violet-100 text-primary-800 text-xs font-bold rounded-full">✅ Available</span>
 </div>
 </div>
 `;
 });
 
 // Auto-select hall if pre-selected from dashboard
 if (window.preSelectedHallId) {
 const hallToSelect = data.availableHalls.find(h => h.id == window.preSelectedHallId);
 if (hallToSelect) {
 setTimeout(() => {
 selectHall(hallToSelect.id, hallToSelect.price_per_day, slot);
 }, 100);
 }
 window.preSelectedHallId = null; // Clear after use
 }
 }
 
 document.getElementById('hallsContainer').classList.remove('hidden');
 } catch (error) {
 console.error('Error:', error);
 }
}

function selectHall(hallId, price, timeSlot) {
 document.querySelectorAll('.hall-card').forEach(card => {
 card.classList.remove('border-violet-500', 'bg-violet-50');
 card.classList.add('border-gray-300');
 });
 
 const clickedCard = event.target.closest('.hall-card');
 if (clickedCard) {
 clickedCard.classList.remove('border-gray-300');
 clickedCard.classList.add('border-violet-500', 'bg-violet-50');
 }

 let finalPrice = price;

 document.getElementById('selectedHallId').value = hallId;
 document.getElementById('hallRent').value = finalPrice;
 document.getElementById('displayHallRent').textContent = 'BDT ' + finalPrice.toFixed(2);
}

async function searchCustomer(phone) {
 if (!phone || phone.length < 10) return;
 
 try {
 const response = await fetch(`{{ url('/admin/convention-bookings/customer') }}/${phone}`);
 if (response.ok) {
 const data = await response.json();
 document.getElementById('customerName').value = data.customerName || '';
 document.getElementById('customerEmail').value = data.customerEmail || '';
 document.getElementById('customerWhatsapp').value = data.customerWhatsapp || '';
 document.getElementById('customerNid').value = data.customerNid || '';
 document.getElementById('customerAddress').value = data.customerAddress || '';
 document.getElementById('organizationName').value = data.organizationName || '';
 document.getElementById('customerFound').classList.remove('hidden');
 }
 } catch (error) {
 console.log('Customer not found');
 }
}

function selectFoodPackage(id, name, pricePerPerson) {
 selectedFoodPrice = pricePerPerson;
 document.querySelectorAll('[name="selected_food_package_id"]').forEach(r => r.checked = false);
 if (id > 0) {
 document.querySelector(`[name="selected_food_package_id"][value="${id}"]`).checked = true;
 } else {
 document.querySelector(`[name="selected_food_package_id"][value=""]`).checked = true;
 }
 updateFoodCost();
}

function updateFoodCost() {
 const guests = parseInt(document.getElementById('numberOfGuests').value) || 0;
 const foodCost = guests * selectedFoodPrice;
 document.getElementById('foodCost').value = foodCost;
 document.getElementById('displayFoodCost').textContent = 'BDT ' + foodCost.toFixed(2);
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
 
 // Remaining
 const advance = parseFloat(document.getElementById('advancePayment').value) || 0;
 document.getElementById('displayAdvance').textContent = 'BDT ' + advance.toFixed(2);
 const remaining = Math.max(0, total - advance);
 document.getElementById('displayRemaining').textContent = 'BDT ' + remaining.toFixed(2);
}

// Handle URL parameters for pre-selection from dashboard
document.addEventListener('DOMContentLoaded', function() {
 const urlParams = new URLSearchParams(window.location.search);
 const preHall = urlParams.get('hall');
 const preDate = urlParams.get('date');
 const preSlot = urlParams.get('slot');
 
 if (preDate && preSlot) {
 // Set date and slot
 document.getElementById('eventDate').value = preDate;
 document.getElementById('timeSlot').value = preSlot;
 
 // Store pre-selected hall ID
 if (preHall) {
 window.preSelectedHallId = preHall;
 }
 
 // Trigger availability check
 setTimeout(() => checkAvailability(), 100);
 }
});
</script>
@endsection
