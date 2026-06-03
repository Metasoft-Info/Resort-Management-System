@extends('layouts.admin')
@section('content')
<div class="p-6 space-y-6">
 <div class="mb-6">
 <h1 class="text-3xl font-bold text-gray-800">Room Booking</h1>
 <p class="text-gray-600 mt-2">Complete booking system with guest search and availability check</p>
 </div>

 <!-- Step 1: Customer Search -->
 <div class="bg-gradient-to-r from-primary-50 to-primary-50 rounded-xl shadow-lg p-6">
 <div class="flex items-center mb-4">
 <i class="fas fa-user-search text-3xl text-primary-600 mr-4"></i>
 <div>
 <h2 class="text-2xl font-bold text-gray-800">Step 1: Search Existing Customer</h2>
 <p class="text-sm text-gray-600">Search by phone number to auto-fill customer details</p>
 </div>
 </div>
 <div class="flex gap-4">
 <input type="tel" id="searchPhone" placeholder="Enter phone number..." 
 class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 <button onclick="searchCustomer()" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-8 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
 <i class="fas fa-search mr-2"></i>Search Customer
 </button>
 </div>
 <div id="customerResults" class="mt-4"></div>
 </div>

 <!-- Step 2: Check Room Availability -->
 <div class="bg-white rounded-xl shadow-lg p-6">
 <div class="flex items-center mb-4">
 <i class="fas fa-calendar-check text-3xl text-primary-600 mr-4"></i>
 <div>
 <h2 class="text-2xl font-bold text-gray-800">Step 2: Check Room Availability</h2>
 <p class="text-sm text-gray-600">Select dates and view available rooms</p>
 </div>
 </div>
 <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Check-in Date *</label>
 <input type="date" id="checkInDate" required onchange="searchRooms()"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Check-out Date *</label>
 <input type="date" id="checkOutDate" required onchange="searchRooms()"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Check-in Time</label>
 <input type="time" id="checkInTime" value="14:00"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Check-out Time</label>
 <input type="time" id="checkOutTime" value="11:00"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 </div>
 <div id="roomsSection" class="mt-6 hidden">
 <h3 class="text-xl font-bold mb-4">Available Rooms</h3>
 <div id="availableRooms" class="grid grid-cols-1 md:grid-cols-3 gap-4"></div>
 </div>
 </div>

 <!-- Step 3: Booking Form -->
 <form id="bookingForm" class="hidden" enctype="multipart/form-data">
 @csrf
 <input type="hidden" id="selectedRoomId" name="room_id">
 
 <!-- Customer Information -->
 <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
 <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
 <i class="fas fa-user text-primary-600 mr-3"></i>
 Customer Information
 </h2>
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Customer Name *</label>
 <input type="text" name="customer_name" id="customer_name" required 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">NID Number *</label>
 <input type="text" name="customer_nid" id="customer_nid" required 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number *</label>
 <input type="tel" name="customer_phone" id="customer_phone" required 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">WhatsApp Number</label>
 <input type="tel" name="customer_whatsapp" id="customer_whatsapp" 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
 <input type="email" name="customer_email" id="customer_email" required 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Passport Number</label>
 <input type="text" name="passport_number" id="passport_number" 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div class="md:col-span-3">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
 <textarea name="customer_address" id="customer_address" rows="2" 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"></textarea>
 </div>
 </div>

 <!-- Document Uploads -->
 <div class="mt-6">
 <h3 class="text-lg font-bold text-gray-700 mb-4">Document Uploads</h3>
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Customer Photo</label>
 <input type="file" name="customer_photo" accept="image/*" 
 class="w-full px-4 py-2 border border-gray-300 rounded-lg">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">NID Document</label>
 <input type="file" name="customer_nid_document" accept="image/*,application/pdf" 
 class="w-full px-4 py-2 border border-gray-300 rounded-lg">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Passport Document</label>
 <input type="file" name="passport_document" accept="image/*,application/pdf" 
 class="w-full px-4 py-2 border border-gray-300 rounded-lg">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Visiting Card</label>
 <input type="file" name="visiting_card" accept="image/*" 
 class="w-full px-4 py-2 border border-gray-300 rounded-lg">
 </div>
 </div>
 </div>

 <!-- Reference Person -->
 <div class="mt-6">
 <h3 class="text-lg font-bold text-gray-700 mb-4">Reference Person (Optional)</h3>
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Reference Name</label>
 <input type="text" name="reference_name" 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Reference Phone</label>
 <input type="tel" name="reference_phone" 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 </div>
 </div>
 </div>

 <!-- Booking Details -->
 <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
 <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
 <i class="fas fa-bed text-primary-600 mr-3"></i>
 Booking Details
 </h2>
 
 <div class="bg-primary-50 border-l-4 border-primary-600 p-4 mb-6">
 <p class="font-semibold text-blue-900">Selected Room: <span id="selectedRoomInfo"></span></p>
 <p class="text-sm text-primary-700 mt-1">Period: <span id="bookingPeriod"></span></p>
 <p class="text-sm text-primary-700">Price per night: <span id="pricePerNight">0</span></p>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Number of Guests *</label>
 <input type="number" name="number_of_guests" id="number_of_guests" min="1" value="1" required 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">AC Preference *</label>
 <select name="ac_preference" id="ac_preference" onchange="recalculateAmount()" required 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 <option value="ac">AC</option>
 <option value="non-ac">Non-AC</option>
 </select>
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
 <select name="status" required 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 <option value="confirmed">Confirmed</option>
 <option value="pending">Pending</option>
 </select>
 </div>
 </div>

 <div class="mt-6">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Special Requests / Notes</label>
 <textarea name="notes" rows="3" 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"></textarea>
 </div>
 </div>

 <!-- Payment Details -->
 <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
 <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
 <i class="fas fa-money-bill-wave text-emerald-600 mr-3"></i>
 Payment & Pricing
 </h2>
 
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
 <!-- Base Amount -->
 <div class="bg-gray-50 p-4 rounded-lg">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Base Amount</label>
 <input type="number" id="baseAmount" readonly 
 class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-lg font-bold">
 </div>

 <!-- VAT -->
 <div>
 <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
 <input type="checkbox" name="vat_enabled" id="vat_enabled" onchange="recalculateAmount()" class="mr-2">
 Apply VAT (15%)
 </label>
 <input type="number" name="vat_amount" id="vat_amount" readonly 
 class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg">
 </div>

 <!-- Discount -->
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Type</label>
 <select name="discount_type" id="discount_type" onchange="recalculateAmount()" 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 <option value="none">No Discount</option>
 <option value="percentage">Percentage</option>
 <option value="flat">Flat Amount</option>
 </select>
 </div>

 <div id="discountInput" class="hidden">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Value</label>
 <input type="number" name="discount_amount" id="discount_amount" value="0" step="0.01" onchange="recalculateAmount()" 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>

 <div id="discountPercentInput" class="hidden">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Discount %</label>
 <input type="number" name="discount_percentage" id="discount_percentage" value="0" min="0" max="100" onchange="recalculateAmount()" 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>

 <!-- Extra Charges -->
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Extra Charges</label>
 <input type="number" name="extra_charges" id="extra_charges" value="0" step="0.01" onchange="recalculateAmount()" 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>

 <div class="md:col-span-2">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Extra Charges Description</label>
 <input type="text" name="extra_charges_description" 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>

 <!-- Total Amount -->
 <div class="bg-green-50 p-4 rounded-lg">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Total Amount</label>
 <input type="number" name="total_amount" id="total_amount" readonly 
 class="w-full px-4 py-3 bg-primary-100 border border-green-300 rounded-lg text-lg font-bold text-primary-700">
 </div>

 <!-- Payment -->
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Advance Payment *</label>
 <input type="number" name="advance_payment" id="advance_payment" value="0" step="0.01" onchange="calculateRemaining()" required 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>

 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method *</label>
 <select name="payment_method" required 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 <option value="cash">Cash</option>
 <option value="card">Card</option>
 <option value="mfs">Mobile Banking</option>
 </select>
 </div>

 <div class="bg-yellow-50 p-4 rounded-lg">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Remaining Payment</label>
 <input type="number" name="remaining_payment" id="remaining_payment" readonly 
 class="w-full px-4 py-3 bg-yellow-100 border border-yellow-300 rounded-lg text-lg font-bold text-yellow-700">
 </div>
 </div>
 </div>

 <!-- Submit Buttons -->
 <div class="flex gap-4">
 <button type="submit" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-12 py-4 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg text-lg font-semibold">
 <i class="fas fa-check-circle mr-2"></i>Confirm Booking
 </button>
 <button type="button" onclick="resetForm()" class="bg-gray-500 text-white px-8 py-4 rounded-lg hover:bg-gray-600 transition">
 <i class="fas fa-redo mr-2"></i>Reset
 </button>
 </div>
 </form>
</div>

<script>
let selectedRoom = null;
let nights = 0;

// Search customer by phone
async function searchCustomer() {
 const phone = document.getElementById('searchPhone').value.trim();
 if (!phone) {
 showGlobalModal('warning', 'Please enter phone number!');
 return;
 }

 const resultsDiv = document.getElementById('customerResults');
 resultsDiv.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-primary-600"></i></div>';

 try {
 const response = await fetch(`/admin/bookings?search=${phone}&type=phone`);
 const bookings = await response.json();

 if (bookings.data && bookings.data.length > 0) {
 const booking = bookings.data[0];
 let html = '<div class="bg-green-50 border border-green-200 rounded-lg p-4">';
 html += `<p class="font-semibold text-primary-700 mb-3"><i class="fas fa-check-circle mr-2"></i>Customer found! Details below:</p>`;
 html += `<div class="grid grid-cols-2 gap-2 text-sm mb-3">`;
 html += `<p><strong>Name:</strong> ${booking.customer_name}</p>`;
 html += `<p><strong>Phone:</strong> ${booking.customer_phone}</p>`;
 html += `<p><strong>Email:</strong> ${booking.customer_email || 'N/A'}</p>`;
 html += `<p><strong>NID:</strong> ${booking.customer_nid || 'N/A'}</p>`;
 html += `</div>`;
 html += `<button onclick='fillCustomerInfo(${JSON.stringify(booking)})' class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">`;
 html += `<i class="fas fa-user-check mr-1"></i>Use This Customer</button></div>`;
 resultsDiv.innerHTML = html;
 } else {
 resultsDiv.innerHTML = '<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4"><p class="text-yellow-700">New customer - please fill in all details</p></div>';
 }
 } catch (error) {
 resultsDiv.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-4"><p class="text-red-600">Error searching customer</p></div>';
 }
}

function fillCustomerInfo(booking) {
 document.getElementById('customer_name').value = booking.customer_name || '';
 document.getElementById('customer_nid').value = booking.customer_nid || '';
 document.getElementById('customer_phone').value = booking.customer_phone || '';
 document.getElementById('customer_whatsapp').value = booking.customer_whatsapp || '';
 document.getElementById('customer_email').value = booking.customer_email || '';
 document.getElementById('passport_number').value = booking.passport_number || '';
 document.getElementById('customer_address').value = booking.customer_address || '';
}

// Search available rooms
async function searchRooms() {
 const checkIn = document.getElementById('checkInDate').value;
 const checkOut = document.getElementById('checkOutDate').value;

 if (!checkIn || !checkOut) return;

 const roomsSection = document.getElementById('roomsSection');
 const roomsDiv = document.getElementById('availableRooms');
 
 roomsSection.classList.remove('hidden');
 roomsDiv.innerHTML = '<div class="col-span-full text-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-primary-600"></i></div>';

 try {
 const response = await fetch('{{ route("admin.premium-booking.search") }}', {
 method: 'POST',
 headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
 body: JSON.stringify({checkIn, checkOut})
 });
 
 const data = await response.json();
 nights = data.nights || 0;
 
 if (data.availableRooms.length === 0) {
 roomsDiv.innerHTML = '<div class="col-span-full bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center"><p class="text-yellow-700 font-semibold">No rooms available</p></div>';
 } else {
 let html = '';
 data.availableRooms.forEach(room => {
 const price = room.room_type?.base_price || 0;
 html += `<div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition">
 <h3 class="font-bold text-lg">Room ${room.room_number}</h3>
 <p class="text-gray-600">${room.room_type?.name || 'N/A'}</p>
 <p class="text-2xl font-bold text-primary-600 my-2">${price.toLocaleString()}/night</p>
 <p class="text-sm text-gray-600 mb-3">${nights} nights = ${(nights * price).toLocaleString()}</p>
 <button onclick='selectRoom(${JSON.stringify(room)})' class="w-full bg-primary-600 text-white py-2 rounded-lg hover:bg-primary-700">
 <i class="fas fa-check mr-1"></i>Select Room
 </button>
 </div>`;
 });
 roomsDiv.innerHTML = html;
 }
 } catch (error) {
 roomsDiv.innerHTML = '<div class="col-span-full bg-red-50 p-8 text-center"><p class="text-red-600">Error loading rooms</p></div>';
 }
}

function selectRoom(room) {
 selectedRoom = room;
 const checkIn = document.getElementById('checkInDate').value;
 const checkOut = document.getElementById('checkOutDate').value;
 
 document.getElementById('selectedRoomId').value = room.id;
 document.getElementById('selectedRoomInfo').textContent = `Room ${room.room_number} - ${room.room_type?.name || 'N/A'}`;
 document.getElementById('bookingPeriod').textContent = `${checkIn} to ${checkOut} (${nights} nights)`;
 document.getElementById('pricePerNight').textContent = room.room_type?.base_price || 0;
 
 const baseAmount = nights * (room.room_type?.base_price || 0);
 document.getElementById('baseAmount').value = baseAmount;
 document.getElementById('total_amount').value = baseAmount;
 
 document.getElementById('bookingForm').classList.remove('hidden');
 document.getElementById('bookingForm').scrollIntoView({ behavior: 'smooth' });
}

// Show/hide discount inputs
document.getElementById('discount_type').addEventListener('change', function() {
 const discountInput = document.getElementById('discountInput');
 const discountPercentInput = document.getElementById('discountPercentInput');
 
 if (this.value === 'flat') {
 discountInput.classList.remove('hidden');
 discountPercentInput.classList.add('hidden');
 } else if (this.value === 'percentage') {
 discountInput.classList.add('hidden');
 discountPercentInput.classList.remove('hidden');
 } else {
 discountInput.classList.add('hidden');
 discountPercentInput.classList.add('hidden');
 }
 recalculateAmount();
});

function recalculateAmount() {
 if (!selectedRoom) return;
 
 const basePrice = selectedRoom.room_type?.base_price || 0;
 let baseAmount = nights * basePrice;
 document.getElementById('baseAmount').value = baseAmount;
 
 // Apply VAT
 let vatAmount = 0;
 if (document.getElementById('vat_enabled').checked) {
 vatAmount = baseAmount * 0.15;
 document.getElementById('vat_amount').value = vatAmount.toFixed(2);
 } else {
 document.getElementById('vat_amount').value = 0;
 }
 
 let totalAfterVat = baseAmount + vatAmount;
 
 // Apply discount
 let discountAmount = 0;
 const discountType = document.getElementById('discount_type').value;
 if (discountType === 'percentage') {
 const percent = parseFloat(document.getElementById('discount_percentage').value) || 0;
 discountAmount = (totalAfterVat * percent) / 100;
 } else if (discountType === 'flat') {
 discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
 }
 
 // Add extra charges
 const extraCharges = parseFloat(document.getElementById('extra_charges').value) || 0;
 
 // Calculate total
 const totalAmount = totalAfterVat - discountAmount + extraCharges;
 document.getElementById('total_amount').value = totalAmount.toFixed(2);
 
 calculateRemaining();
}

function calculateRemaining() {
 const total = parseFloat(document.getElementById('total_amount').value) || 0;
 const advance = parseFloat(document.getElementById('advance_payment').value) || 0;
 const remaining = total - advance;
 document.getElementById('remaining_payment').value = remaining.toFixed(2);
}

// Form submission
document.getElementById('bookingForm').addEventListener('submit', async function(e) {
 e.preventDefault();
 
 const formData = new FormData(this);
 formData.append('check_in_date', document.getElementById('checkInDate').value);
 formData.append('check_out_date', document.getElementById('checkOutDate').value);
 formData.append('check_in_time', document.getElementById('checkInTime').value);
 formData.append('check_out_time', document.getElementById('checkOutTime').value);
 
 try {
 const response = await fetch('{{ route("admin.premium-booking.book") }}', {
 method: 'POST',
 headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
 body: formData
 });
 
 const result = await response.json();
 
 if (result.success) {
 showGlobalModal('success', 'Booking successful!');
 setTimeout(() => { window.location.href = '{{ route("admin.bookings.index") }}'; }, 1500);
 } else {
 showGlobalModal('error', 'Error: ' + (result.message || 'Booking failed!'));
 }
 } catch (error) {
 showGlobalModal('error', 'Failed to create booking: ' + error.message);
 }
});

function resetForm() {
 document.getElementById('bookingForm').classList.add('hidden');
 document.getElementById('roomsSection').classList.add('hidden');
 document.getElementById('bookingForm').reset();
 selectedRoom = null;
}
</script>
@endsection
