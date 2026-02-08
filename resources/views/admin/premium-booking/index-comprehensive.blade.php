@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Room Booking</h1>
        <p class="text-gray-600 mt-2">Comprehensive booking system with guest search, room availability, and complete customer information</p>
    </div>

    <!-- Step 1: Room Availability -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-bed text-3xl text-primary-600 mr-4"></i>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Step 1: Check Room Availability</h2>
                <p class="text-sm text-gray-600">Select dates and find available rooms</p>
            </div>
        </div>
        <form id="searchRoomsForm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check-in Date *</label>
                    <input type="date" id="checkInDate" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check-out Date *</label>
                    <input type="date" id="checkOutDate" required
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
            <button type="submit" class="mt-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
                <i class="fas fa-search mr-2"></i>Search Available Rooms
            </button>
        </form>
        <div id="roomResults" class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>
    </div>

    <!-- Step 2: Customer Search (Optional) -->
    <div class="bg-gradient-to-r from-primary-50 to-primary-50 rounded-xl shadow-lg p-6 mb-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-user-search text-3xl text-primary-600 mr-4"></i>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Step 2: Customer Search (Optional)</h2>
                <p class="text-sm text-gray-600">Search for existing customer by phone to auto-fill information</p>
            </div>
        </div>
        <div class="flex gap-3">
            <input type="text" id="searchPhone" placeholder="Enter phone number..." 
                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            <button onclick="searchCustomer()" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
                <i class="fas fa-search mr-2"></i>Search Customer
            </button>
        </div>
        <div id="customerSearchResults" class="mt-4"></div>
    </div>

    <!-- Step 3: Booking Form -->
    <form id="bookingForm" class="hidden" onsubmit="submitBooking(event)">
        <div id="selectedRoomInfo" class="bg-primary-50 border-l-4 border-primary-600 p-4 mb-6 rounded-lg"></div>

        <!-- Customer Information -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-user text-primary-600 mr-3"></i>
                Customer Information
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                    <input type="text" id="customer_name" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">NID Number *</label>
                    <input type="text" id="customer_nid" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number *</label>
                    <input type="tel" id="customer_phone" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">WhatsApp Number</label>
                    <input type="tel" id="customer_whatsapp"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                    <input type="email" id="customer_email" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Passport Number</label>
                    <input type="text" id="passport_number"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                    <textarea id="customer_address" rows="2"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"></textarea>
                </div>
            </div>

            <!-- Document Uploads -->
            <div class="mt-6 border-t pt-6">
                <h3 class="text-lg font-bold text-gray-700 mb-4">Document Uploads (Optional)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Customer Photo</label>
                        <input type="file" id="customer_photo" accept="image/*"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">NID Document</label>
                        <input type="file" id="customer_nid_document" accept="image/*,application/pdf"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Passport Document</label>
                        <input type="file" id="passport_document" accept="image/*,application/pdf"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Visiting Card</label>
                        <input type="file" id="visiting_card" accept="image/*"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
            </div>

            <!-- Reference Person -->
            <div class="mt-6 border-t pt-6">
                <h3 class="text-lg font-bold text-gray-700 mb-4">Reference Person (Optional)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Reference Name</label>
                        <input type="text" id="reference_name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Reference Phone</label>
                        <input type="tel" id="reference_phone"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Guests -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-users text-primary-600 mr-3"></i>
                    Additional Guests
                </h2>
                <button type="button" onclick="addAdditionalGuest()" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                    <i class="fas fa-plus mr-2"></i>Add Guest
                </button>
            </div>
            <div id="additionalGuestsList"></div>
        </div>

        <!-- Booking Details -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-calendar-check text-primary-600 mr-3"></i>
                Booking Details
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Number of Guests *</label>
                    <input type="number" id="number_of_guests" min="1" value="1" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">AC Preference *</label>
                    <select id="ac_preference" required onchange="recalculateAmount()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="ac">AC</option>
                        <option value="non-ac">Non-AC</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Booking Status *</label>
                    <select id="status" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="confirmed">Confirmed</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Total Nights</label>
                    <input type="text" id="totalNights" readonly
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100">
                </div>
                <div class="md:col-span-2 lg:col-span-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                    <textarea id="notes" rows="2"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
                </div>
            </div>
        </div>

        <!-- Payment & Pricing -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-money-bill-wave text-yellow-600 mr-3"></i>
                Payment & Pricing
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Base Amount (৳)</label>
                    <input type="number" id="baseAmount" step="0.01" readonly
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <input type="checkbox" id="vat_enabled" onchange="recalculateAmount()"> VAT (15%)
                    </label>
                    <input type="number" id="vat_amount" step="0.01" readonly
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Type</label>
                    <select id="discount_type" onchange="recalculateAmount()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                        <option value="none">No Discount</option>
                        <option value="percentage">Percentage</option>
                        <option value="flat">Flat Amount</option>
                    </select>
                </div>
                <div id="discount_percentage_div" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Percentage (%)</label>
                    <input type="number" id="discount_percentage" min="0" max="100" step="0.01" value="0" onchange="recalculateAmount()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                </div>
                <div id="discount_amount_div" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Amount (৳)</label>
                    <input type="number" id="discount_amount" step="0.01" value="0" onchange="recalculateAmount()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Extra Charges (৳)</label>
                    <input type="number" id="extra_charges" step="0.01" value="0" onchange="recalculateAmount()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Extra Charges Description</label>
                    <input type="text" id="extra_charges_description"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                </div>
                <div class="lg:col-span-3 bg-green-50 border-2 border-primary-500 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-primary-700 mb-2">Total Amount (৳) *</label>
                            <input type="number" id="total_amount" step="0.01" required readonly
                                class="w-full px-4 py-3 border-2 border-green-600 rounded-lg bg-white font-bold text-primary-600 text-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Advance Payment (৳) *</label>
                            <input type="number" id="advance_payment" step="0.01" required oninput="calculateRemaining()"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Remaining Payment (৳)</label>
                            <input type="number" id="remaining_payment" step="0.01" readonly
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-yellow-50 font-bold text-yellow-600">
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method *</label>
                    <select id="payment_method" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="mfs">Mobile Banking (MFS)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Hidden Fields -->
        <input type="hidden" id="room_id">
        <input type="hidden" id="check_in_date">
        <input type="hidden" id="check_out_date">
        <input type="hidden" id="check_in_time_hidden">
        <input type="hidden" id="check_out_time_hidden">
        <input type="hidden" id="price_per_night">

        <!-- Submit Buttons -->
        <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-gradient-to-r from-primary-600 to-primary-700 text-white px-8 py-4 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg text-lg font-bold">
                <i class="fas fa-check-circle mr-2"></i>Confirm Booking
            </button>
            <button type="button" onclick="resetAll()" class="bg-gray-500 text-white px-8 py-4 rounded-lg hover:bg-gray-600 transition">
                <i class="fas fa-times mr-2"></i>Cancel
            </button>
        </div>
    </form>
</div>

<script>
let additionalGuests = [];
let selectedRoomData = null;

// Customer Search
async function searchCustomer() {
    const phone = document.getElementById('searchPhone').value.trim();
    const resultsDiv = document.getElementById('customerSearchResults');
    
    if (!phone) {
        resultsDiv.innerHTML = '<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4"><p class="text-yellow-700">Please enter a phone number</p></div>';
        return;
    }

    resultsDiv.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-primary-600"></i><p class="text-gray-600 mt-2">Searching...</p></div>';

    try {
        const response = await fetch(`/admin/bookings?search=${encodeURIComponent(phone)}&type=phone`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const bookings = await response.json();

        if (bookings.data && bookings.data.length > 0) {
            const booking = bookings.data[0];
            fillCustomerInfo(booking);
            resultsDiv.innerHTML = '<div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-4"><p class="text-primary-700"><i class="fas fa-check-circle mr-2"></i>Customer found! Information auto-filled below.</p></div>';
        } else {
            resultsDiv.innerHTML = '<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4"><p class="text-yellow-700"><i class="fas fa-info-circle mr-2"></i>No previous bookings found</p></div>';
        }
    } catch (error) {
        console.error('Search error:', error);
        resultsDiv.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-4 mt-4"><p class="text-red-600">Error searching customer</p></div>';
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
    document.getElementById('reference_name').value = booking.reference_name || '';
    document.getElementById('reference_phone').value = booking.reference_phone || '';
}

// Room Search
document.getElementById('searchRoomsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const checkIn = document.getElementById('checkInDate').value;
    const checkOut = document.getElementById('checkOutDate').value;
    const checkInTime = document.getElementById('checkInTime').value;
    const checkOutTime = document.getElementById('checkOutTime').value;

    try {
        const response = await fetch('{{ route("admin.premium-booking.search") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                checkIn,
                checkOut,
                roomTypeId: null
            })
        });

        const data = await response.json();
        const container = document.getElementById('roomResults');
        container.innerHTML = '';

        if (data.availableRooms.length === 0) {
            container.innerHTML = '<p class="text-gray-500 col-span-3 text-center py-8">No rooms available for selected dates</p>';
        } else {
            data.availableRooms.forEach(room => {
                const pricePerNight = parseFloat(room.price_per_night) || parseFloat(room.room_type?.base_price) || 0;
                const totalPrice = pricePerNight * data.nights;
                
                container.innerHTML += `
                    <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-primary-500 hover:shadow-lg transition cursor-pointer">
                        <h3 class="font-bold text-lg text-gray-800">${room.room_number}</h3>
                        <p class="text-gray-600">${room.room_type?.name || room.type || 'N/A'}</p>
                        <p class="text-primary-600 font-semibold mt-2">৳${pricePerNight.toLocaleString()} / night</p>
                        <p class="text-sm text-gray-500">${data.nights} nights = ৳${totalPrice.toLocaleString()}</p>
                        <button type="button" onclick="selectRoom(${room.id}, '${room.room_number}', '${room.name || room.room_type?.name || 'Room'}', ${data.nights}, ${pricePerNight}, '${checkIn}', '${checkOut}', '${checkInTime}', '${checkOutTime}')" 
                            class="mt-3 w-full bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                            <i class="fas fa-hand-pointer mr-2"></i>Select Room
                        </button>
                    </div>
                `;
            });
        }
    } catch (error) {
        console.error('Search error:', error);
        showGlobalModal('error', 'রুম খুঁজতে সমস্যা হয়েছে!');
    }
});

function selectRoom(roomId, roomNumber, roomName, nights, pricePerNight, checkIn, checkOut, checkInTime, checkOutTime) {
    selectedRoomData = {roomId, roomNumber, roomName, nights, pricePerNight};
    
    document.getElementById('room_id').value = roomId;
    document.getElementById('check_in_date').value = checkIn;
    document.getElementById('check_out_date').value = checkOut;
    document.getElementById('check_in_time_hidden').value = checkInTime;
    document.getElementById('check_out_time_hidden').value = checkOutTime;
    document.getElementById('price_per_night').value = pricePerNight;
    document.getElementById('totalNights').value = nights;
    
    document.getElementById('selectedRoomInfo').innerHTML = `
        <div class="flex items-center justify-between">
            <div>
                <p class="font-bold text-blue-900 text-lg">Room: ${roomNumber} - ${roomName}</p>
                <p class="text-primary-700">Check-in: ${checkIn} at ${checkInTime} | Check-out: ${checkOut} at ${checkOutTime}</p>
                <p class="text-primary-700">${nights} nights × ৳${pricePerNight.toLocaleString()}/night</p>
            </div>
            <button type="button" onclick="resetRoomSelection()" class="text-red-600 hover:text-red-800">
                <i class="fas fa-times-circle text-2xl"></i>
            </button>
        </div>
    `;
    
    recalculateAmount();
    document.getElementById('bookingForm').classList.remove('hidden');
    document.getElementById('bookingForm').scrollIntoView({behavior: 'smooth'});
}

function resetRoomSelection() {
    selectedRoomData = null;
    document.getElementById('bookingForm').classList.add('hidden');
    document.getElementById('roomResults').innerHTML = '';
}

// Additional Guests
function addAdditionalGuest() {
    const index = additionalGuests.length;
    additionalGuests.push({name: '', nid: '', phone: ''});
    
    const guestHtml = `
        <div class="border-2 border-primary-200 rounded-lg p-4 mb-3 bg-primary-50" id="guest-${index}">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-primary-900">Guest #${index + 2}</h3>
                <button type="button" onclick="removeAdditionalGuest(${index})" class="text-red-600 hover:text-red-800 font-semibold">
                    <i class="fas fa-trash mr-1"></i>Remove
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Name *</label>
                    <input type="text" id="guest_name_${index}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">NID *</label>
                    <input type="text" id="guest_nid_${index}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Phone *</label>
                    <input type="tel" id="guest_phone_${index}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('additionalGuestsList').insertAdjacentHTML('beforeend', guestHtml);
}

function removeAdditionalGuest(index) {
    document.getElementById(`guest-${index}`).remove();
    additionalGuests[index] = null;
}

// Calculations
function recalculateAmount() {
    if (!selectedRoomData) return;
    
    const discountType = document.getElementById('discount_type').value;
    document.getElementById('discount_percentage_div').classList.toggle('hidden', discountType !== 'percentage');
    document.getElementById('discount_amount_div').classList.toggle('hidden', discountType !== 'flat');
    
    const baseAmount = selectedRoomData.nights * selectedRoomData.pricePerNight;
    document.getElementById('baseAmount').value = baseAmount.toFixed(2);
    
    let total = baseAmount;
    
    // VAT
    const vatEnabled = document.getElementById('vat_enabled').checked;
    const vatAmount = vatEnabled ? (baseAmount * 0.15) : 0;
    document.getElementById('vat_amount').value = vatAmount.toFixed(2);
    if (vatEnabled) total += vatAmount;
    
    // Discount
    if (discountType === 'percentage') {
        const discountPercentage = parseFloat(document.getElementById('discount_percentage').value) || 0;
        const discountAmount = (total * discountPercentage) / 100;
        total -= discountAmount;
    } else if (discountType === 'flat') {
        const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
        total -= discountAmount;
    }
    
    // Extra charges
    const extraCharges = parseFloat(document.getElementById('extra_charges').value) || 0;
    total += extraCharges;
    
    document.getElementById('total_amount').value = total.toFixed(2);
    calculateRemaining();
}

function calculateRemaining() {
    const total = parseFloat(document.getElementById('total_amount').value) || 0;
    const advance = parseFloat(document.getElementById('advance_payment').value) || 0;
    document.getElementById('remaining_payment').value = (total - advance).toFixed(2);
}

// Form Submission
async function submitBooking(e) {
    e.preventDefault();
    
    const formData = new FormData();
    
    // Basic fields
    formData.append('room_id', document.getElementById('room_id').value);
    formData.append('check_in_date', document.getElementById('check_in_date').value);
    formData.append('check_out_date', document.getElementById('check_out_date').value);
    formData.append('check_in_time', document.getElementById('check_in_time_hidden').value);
    formData.append('check_out_time', document.getElementById('check_out_time_hidden').value);
    
    // Customer info
    formData.append('customer_name', document.getElementById('customer_name').value);
    formData.append('customer_nid', document.getElementById('customer_nid').value);
    formData.append('customer_phone', document.getElementById('customer_phone').value);
    formData.append('customer_whatsapp', document.getElementById('customer_whatsapp').value);
    formData.append('customer_email', document.getElementById('customer_email').value);
    formData.append('passport_number', document.getElementById('passport_number').value);
    formData.append('customer_address', document.getElementById('customer_address').value);
    formData.append('reference_name', document.getElementById('reference_name').value);
    formData.append('reference_phone', document.getElementById('reference_phone').value);
    
    // Documents
    const customerPhoto = document.getElementById('customer_photo').files[0];
    if (customerPhoto) formData.append('customer_photo', customerPhoto);
    
    const customerNidDoc = document.getElementById('customer_nid_document').files[0];
    if (customerNidDoc) formData.append('customer_nid_document', customerNidDoc);
    
    const passportDoc = document.getElementById('passport_document').files[0];
    if (passportDoc) formData.append('passport_document', passportDoc);
    
    const visitingCard = document.getElementById('visiting_card').files[0];
    if (visitingCard) formData.append('visiting_card', visitingCard);
    
    // Booking details
    formData.append('number_of_guests', document.getElementById('number_of_guests').value);
    formData.append('ac_preference', document.getElementById('ac_preference').value);
    formData.append('status', document.getElementById('status').value);
    formData.append('notes', document.getElementById('notes').value);
    
    // Payment
    formData.append('total_amount', document.getElementById('total_amount').value);
    formData.append('vat_enabled', document.getElementById('vat_enabled').checked ? '1' : '0');
    formData.append('vat_amount', document.getElementById('vat_amount').value);
    formData.append('discount_type', document.getElementById('discount_type').value);
    formData.append('discount_percentage', document.getElementById('discount_percentage').value || '0');
    formData.append('discount_amount', document.getElementById('discount_amount').value || '0');
    formData.append('extra_charges', document.getElementById('extra_charges').value || '0');
    formData.append('extra_charges_description', document.getElementById('extra_charges_description').value);
    formData.append('advance_payment', document.getElementById('advance_payment').value);
    formData.append('remaining_payment', document.getElementById('remaining_payment').value);
    formData.append('payment_method', document.getElementById('payment_method').value);
    
    // Additional guests
    const guestList = [];
    additionalGuests.forEach((guest, index) => {
        if (guest !== null) {
            const name = document.getElementById(`guest_name_${index}`)?.value;
            const nid = document.getElementById(`guest_nid_${index}`)?.value;
            const phone = document.getElementById(`guest_phone_${index}`)?.value;
            if (name && nid && phone) {
                guestList.push({name, nid, phone});
            }
        }
    });
    if (guestList.length > 0) {
        formData.append('additional_guests', JSON.stringify(guestList));
    }
    
    try {
        const response = await fetch('{{ route("admin.premium-booking.book") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });

        const data = await response.json();
        
        if (data.success) {
            showGlobalModal('success', 'বুকিং সফল হয়েছে!');
            setTimeout(() => { window.location.href = '{{ route("admin.bookings.index") }}'; }, 1500);
        } else {
            showGlobalModal('error', 'Error: ' + (data.message || 'বুকিং ব্যর্থ হয়েছে!'));
            console.error('Validation errors:', data.errors);
        }
    } catch (error) {
        console.error('Booking error:', error);
        showGlobalModal('error', 'বুকিং তৈরি করতে সমস্যা হয়েছে: ' + error.message);
    }
}

function resetAll() {
    showConfirmModal('আপনি কি সব ফিল্ড রিসেট করতে চান?', function() {
        location.reload();
    });
}

// Initialize discount type change
document.getElementById('discount_type').addEventListener('change', recalculateAmount);
</script>
@endsection
