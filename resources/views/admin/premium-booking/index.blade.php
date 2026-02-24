@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Room Booking</h1>
        <p class="text-gray-600 mt-2">Comprehensive booking system with guest search, room availability, and complete customer information</p>
    </div>

    @if(isset($existingBooking) && $existingBooking)
    <!-- Existing Booking Info - Adding Room Mode -->
    <div class="bg-blue-50 border-2 border-blue-400 rounded-xl p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-3xl text-blue-600 mr-4"></i>
                <div>
                    <h2 class="text-xl font-bold text-blue-800">Adding Room to Existing Booking #{{ $existingBooking->id }}</h2>
                    <p class="text-sm text-blue-600">Select additional rooms to add to this booking</p>
                </div>
            </div>
            <a href="{{ route('admin.bookings.show', $existingBooking->id) }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-1"></i>Back to Booking
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <span class="font-semibold text-blue-800">Customer:</span>
                <span class="text-gray-700">{{ $existingBooking->customer_name }} ({{ $existingBooking->customer_phone }})</span>
            </div>
            <div>
                <span class="font-semibold text-blue-800">Dates:</span>
                <span class="text-gray-700">{{ \Carbon\Carbon::parse($existingBooking->check_in_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($existingBooking->check_out_date)->format('d M Y') }}</span>
            </div>
            <div>
                <span class="font-semibold text-blue-800">Current Total:</span>
                <span class="text-gray-700">৳{{ number_format($existingBooking->getCalculatedTotal(), 0) }}</span>
            </div>
        </div>
        <div class="mt-4">
            <span class="font-semibold text-blue-800">Already Booked Rooms:</span>
            <div class="flex flex-wrap gap-2 mt-2">
                @php $existingRooms = $existingBooking->getAllRooms(); @endphp
                @foreach($existingRooms as $room)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-200 text-blue-800">
                        <i class="fas fa-bed mr-1"></i>Room {{ $room->room_number }} - {{ $room->roomType->name ?? 'N/A' }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
    @endif

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
                    <input type="time" id="checkInTime" value="12:00"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check-out Time</label>
                    <input type="time" id="checkOutTime" value="12:00"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
            <button type="submit" class="mt-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
                <i class="fas fa-search mr-2"></i>Search Available Rooms
            </button>
        </form>
        <div id="roomResults" class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>
        
        <!-- Selected Rooms Panel -->
        <div id="selectedRoomsPanel" class="hidden mt-6 bg-green-50 border-2 border-green-300 rounded-xl p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-bold text-green-800"><i class="fas fa-check-circle mr-2"></i>Selected Rooms</h3>
                <button type="button" onclick="clearAllSelectedRooms()" class="text-red-600 hover:text-red-800 text-sm"><i class="fas fa-trash mr-1"></i>Clear All</button>
            </div>
            <div id="selectedRoomsList" class="flex flex-wrap gap-2"></div>
            <div class="mt-3 pt-3 border-t border-green-300 flex items-center justify-between">
                <p class="text-green-800 font-semibold">Total: <span id="selectedRoomsTotal">৳0</span></p>
                @if(isset($existingBooking) && $existingBooking)
                <button type="button" onclick="submitAddRooms()" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-plus-circle mr-2"></i>Add Rooms to Booking
                </button>
                @else
                <button type="button" onclick="proceedToBookingForm()" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-arrow-right mr-2"></i>Proceed to Booking
                </button>
                @endif
            </div>
        </div>
    </div>

    @if(!isset($existingBooking) || !$existingBooking)
    <!-- Step 2: Customer Search (Optional) - Hidden when adding room to existing booking -->
    <div class="bg-gradient-to-r from-primary-50 to-primary-50 rounded-xl shadow-lg p-6 mb-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-user-search text-3xl text-primary-600 mr-4"></i>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Step 2: Customer Search (Optional)</h2>
                <p class="text-sm text-gray-600">Search by Phone, NID, or Passport number to auto-fill information</p>
            </div>
        </div>
        <div class="flex gap-3">
            <input type="text" id="searchPhone" placeholder="Enter Phone / NID / Passport number..." 
                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            <button onclick="searchCustomer()" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
                <i class="fas fa-search mr-2"></i>Search Customer
            </button>
        </div>
        <div id="customerSearchResults" class="mt-4"></div>
    </div>
    @endif

    <!-- Step 3: Booking Form -->
    <form id="bookingForm" class="hidden" onsubmit="submitBooking(event)">
        <!-- Hidden field for existing booking ID when adding rooms -->
        <input type="hidden" id="existing_booking_id" value="{{ $existingBooking->id ?? '' }}">
        
        <div id="selectedRoomInfo" class="bg-primary-50 border-l-4 border-primary-600 p-4 mb-6 rounded-lg"></div>

        @if(!isset($existingBooking) || !$existingBooking)
        <!-- Customer Information - Hidden when adding room to existing booking -->
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
                    <label class="block text-sm font-semibold text-gray-700 mb-2">NID Number</label>
                    <input type="text" id="customer_nid"
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
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" id="customer_email"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Passport Number</label>
                    <input type="text" id="passport_number"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Company Name</label>
                    <input type="text" id="company_name"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
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
        @endif

        @if(!isset($existingBooking) || !$existingBooking)
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
        @endif

        @if(!isset($existingBooking) || !$existingBooking)
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

        <!-- Payment & Pricing - Hidden when adding to existing booking -->
        @endif
        @if(!isset($existingBooking) || !$existingBooking)
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
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Advance Payment (৳)</label>
                            <input type="number" id="advance_payment" step="0.01" value="0" oninput="calculateRemaining()"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Remaining Payment (৳)</label>
                            <input type="number" id="remaining_payment" step="0.01" readonly
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-yellow-50 font-bold text-yellow-600">
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method *</label>
                        <select id="payment_method" required onchange="togglePaymentFields()"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                            <option value="cash">Cash</option>
                            <option value="bkash">bKash</option>
                            <option value="card">Card</option>
                        </select>
                    </div>
                    <div id="bkash_field" class="hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">bKash Number</label>
                        <input type="text" id="bkash_number" placeholder="01XXXXXXXXX"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div id="bank_field" class="hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Bank Name</label>
                        <select id="bank_name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                            <option value="">Select Bank</option>
                            <option value="Pubali Bank">Pubali Bank</option>
                            <option value="City Bank">City Bank</option>
                            <option value="Dutch Bangla Bank">Dutch Bangla Bank</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Hidden Fields -->
        <input type="hidden" id="room_id">
        <input type="hidden" id="check_in_date">
        <input type="hidden" id="check_out_date">
        <input type="hidden" id="check_in_time_hidden">
        <input type="hidden" id="check_out_time_hidden">
        <input type="hidden" id="price_per_night">

        <!-- Submit Buttons -->
        <div class="flex gap-4">
            @if(isset($existingBooking) && $existingBooking)
            <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-purple-700 text-white px-8 py-4 rounded-lg hover:from-purple-700 hover:to-purple-800 transition shadow-lg text-lg font-bold">
                <i class="fas fa-plus-circle mr-2"></i>Add Rooms to Booking #{{ $existingBooking->id }}
            </button>
            <a href="{{ route('admin.bookings.show', $existingBooking->id) }}" class="bg-gray-500 text-white px-8 py-4 rounded-lg hover:bg-gray-600 transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Back to Booking
            </a>
            @else
            <button type="submit" class="flex-1 bg-gradient-to-r from-primary-600 to-primary-700 text-white px-8 py-4 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg text-lg font-bold">
                <i class="fas fa-check-circle mr-2"></i>Confirm Booking
            </button>
            <button type="button" onclick="resetAll()" class="bg-gray-500 text-white px-8 py-4 rounded-lg hover:bg-gray-600 transition">
                <i class="fas fa-times mr-2"></i>Cancel
            </button>
            @endif
        </div>
    </form>
</div>

<script>
let additionalGuests = [];
let selectedRooms = [];
let currentSearchDates = {};

// Customer Search
async function searchCustomer() {
    const searchQuery = document.getElementById('searchPhone').value.trim();
    const resultsDiv = document.getElementById('customerSearchResults');
    
    if (!searchQuery) {
        resultsDiv.innerHTML = '<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4"><p class="text-yellow-700">Please enter Phone, NID, or Passport number</p></div>';
        return;
    }

    resultsDiv.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-primary-600"></i><p class="text-gray-600 mt-2">Searching...</p></div>';

    try {
        // Use dedicated endpoint that returns latest customer data
        const response = await fetch(`{{ route('admin.premium-booking.search-customer') }}?query=${encodeURIComponent(searchQuery)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();

        if (data.success && data.customer) {
            fillCustomerInfo(data.customer);
            resultsDiv.innerHTML = '<div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-4"><p class="text-primary-700"><i class="fas fa-check-circle mr-2"></i>Customer found! Latest information auto-filled below.</p></div>';
        } else {
            resultsDiv.innerHTML = '<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4"><p class="text-yellow-700"><i class="fas fa-info-circle mr-2"></i>No previous bookings found</p></div>';
        }
    } catch (error) {
        console.error('Search error:', error);
        resultsDiv.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-4 mt-4"><p class="text-red-600">Error searching customer</p></div>';
    }
}

function fillCustomerInfo(customer) {
    document.getElementById('customer_name').value = customer.customer_name || '';
    document.getElementById('customer_nid').value = customer.customer_nid || '';
    document.getElementById('customer_phone').value = customer.customer_phone || '';
    document.getElementById('customer_whatsapp').value = customer.customer_whatsapp || '';
    document.getElementById('customer_email').value = customer.customer_email || '';
    document.getElementById('passport_number').value = customer.passport_number || '';
    document.getElementById('customer_address').value = customer.customer_address || '';
    document.getElementById('company_name').value = customer.company_name || '';
    document.getElementById('reference_name').value = customer.reference_name || '';
    document.getElementById('reference_phone').value = customer.reference_phone || '';
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
                roomTypeId: null,
                excludeBookingId: '{{ $existingBooking->id ?? "" }}'
            })
        });

        const data = await response.json();
        const container = document.getElementById('roomResults');
        container.innerHTML = '';
        
        const nights = Math.max(1, Math.floor(data.nights || 1));
        
        // Store search dates for later use
        currentSearchDates = { checkIn, checkOut, checkInTime, checkOutTime, nights };

        if (data.availableRooms.length === 0) {
            container.innerHTML = '<p class="text-gray-500 col-span-3 text-center py-8">No rooms available for selected dates</p>';
        } else {
            data.availableRooms.forEach(room => {
                const pricePerNight = parseFloat(room.price_per_night) || parseFloat(room.room_type?.base_price) || 0;
                const totalPrice = pricePerNight * nights;
                const roomImage = room.images && room.images.length > 0 ? room.images[0] : null;
                const isSelected = selectedRooms.some(r => r.roomId === room.id);
                
                container.innerHTML += `
                    <div id="roomCard-${room.id}" class="border-2 ${isSelected ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-white'} rounded-lg overflow-hidden hover:border-primary-500 hover:shadow-lg transition cursor-pointer">
                        <div class="h-32 bg-gradient-to-br from-blue-400 to-primary-500 relative">
                            ${roomImage ? `<img src="/storage/${roomImage}" alt="${room.name || room.room_number}" class="w-full h-full object-cover">` : `<div class="w-full h-full flex items-center justify-center"><i class="fas fa-bed text-4xl text-white/50"></i></div>`}
                            <div class="absolute top-2 right-2 bg-white px-2 py-1 rounded text-xs font-bold text-primary-700">${room.room_type?.name || room.type || 'N/A'}</div>
                            ${isSelected ? '<div class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 rounded text-xs font-bold"><i class="fas fa-check"></i></div>' : ''}
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-lg text-gray-800">Room ${room.room_number}</h3>
                            <p class="text-sm text-gray-600">${room.room_type?.name || 'Room'}</p>
                            <p class="text-primary-600 font-semibold mt-2">৳${pricePerNight.toLocaleString()} / night</p>
                            <p class="text-sm text-gray-500">${nights} night${nights > 1 ? 's' : ''} = ৳${totalPrice.toLocaleString()}</p>
                            <button type="button" onclick="toggleRoomSelection(${room.id}, '${room.room_number}', '${(room.name || room.room_type?.name || 'Room').replace(/'/g, "\\'")}', ${nights}, ${pricePerNight})" 
                                class="mt-3 w-full ${isSelected ? 'bg-red-500 hover:bg-red-600' : 'bg-primary-600 hover:bg-primary-700'} text-white px-4 py-2 rounded-lg transition">
                                <i class="fas ${isSelected ? 'fa-times' : 'fa-plus'} mr-2"></i>${isSelected ? 'Remove' : 'Add Room'}
                            </button>
                        </div>
                    </div>
                `;
            });
        }
    } catch (error) {
        console.error('Search error:', error);
        showGlobalModal('error', 'Error searching rooms');
    }
});

// Toggle room selection (add/remove from list)
function toggleRoomSelection(roomId, roomNumber, roomName, nights, pricePerNight) {
    const existingIndex = selectedRooms.findIndex(r => r.roomId === roomId);
    
    if (existingIndex >= 0) {
        // Remove room
        selectedRooms.splice(existingIndex, 1);
    } else {
        // Add room
        selectedRooms.push({roomId, roomNumber, roomName, nights, pricePerNight});
    }
    
    updateSelectedRoomsPanel();
    refreshRoomCards();
}

// Update selected rooms panel
function updateSelectedRoomsPanel() {
    const panel = document.getElementById('selectedRoomsPanel');
    const list = document.getElementById('selectedRoomsList');
    const totalSpan = document.getElementById('selectedRoomsTotal');
    
    if (selectedRooms.length === 0) {
        panel.classList.add('hidden');
        document.getElementById('bookingForm').classList.add('hidden');
        return;
    }
    
    panel.classList.remove('hidden');
    
    // Build chips for each selected room
    let html = '';
    let total = 0;
    selectedRooms.forEach(room => {
        const roomTotal = room.nights * room.pricePerNight;
        total += roomTotal;
        html += `
            <div class="bg-white border border-green-400 rounded-lg px-3 py-2 flex items-center gap-2">
                <span class="font-semibold text-green-800">Room ${room.roomNumber}</span>
                <span class="text-sm text-gray-600">৳${roomTotal.toLocaleString()}</span>
                <button type="button" onclick="toggleRoomSelection(${room.roomId}, '${room.roomNumber}', '${room.roomName.replace(/'/g, "\\'")}', ${room.nights}, ${room.pricePerNight})" 
                    class="text-red-500 hover:text-red-700 ml-1"><i class="fas fa-times"></i></button>
            </div>
        `;
    });
    
    list.innerHTML = html;
    totalSpan.textContent = '৳' + total.toLocaleString();
}

// Refresh room cards to show selected state
function refreshRoomCards() {
    selectedRooms.forEach(room => {
        const card = document.getElementById('roomCard-' + room.roomId);
        if (card) {
            card.classList.remove('border-gray-200', 'bg-white');
            card.classList.add('border-green-500', 'bg-green-50');
        }
    });
}

// Clear all selected rooms
function clearAllSelectedRooms() {
    selectedRooms = [];
    updateSelectedRoomsPanel();
    // Re-trigger search to refresh cards
    document.getElementById('searchRoomsForm').dispatchEvent(new Event('submit'));
}

// Submit add rooms to existing booking (simplified flow)
async function submitAddRooms() {
    if (selectedRooms.length === 0) {
        showGlobalModal('error', 'Please select at least one room');
        return;
    }
    
    const existingBookingId = '{{ $existingBooking->id ?? '' }}';
    if (!existingBookingId) {
        showGlobalModal('error', 'No existing booking found');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('existing_booking_id', existingBookingId);
        formData.append('rooms_data', JSON.stringify(selectedRooms.map(room => ({
            roomId: room.roomId,
            roomNumber: room.roomNumber,
            pricePerNight: room.pricePerNight
        }))));
        
        const response = await fetch('{{ route("admin.premium-booking.book") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });

        const data = await response.json();
        
        if (data.success) {
            showGlobalModal('success', data.message);
            setTimeout(() => { 
                window.location.href = '{{ route("admin.bookings.show", ["booking" => $existingBooking->id ?? 0]) }}'; 
            }, 1500);
        } else {
            showGlobalModal('error', data.message || 'Failed to add rooms!');
        }
    } catch (error) {
        console.error('Add rooms error:', error);
        showGlobalModal('error', 'Error adding rooms');
    }
}

// Proceed to booking form
function proceedToBookingForm() {
    if (selectedRooms.length === 0) {
        showGlobalModal('error', 'Please select at least one room');
        return;
    }
    
    // Use first room's data for form (all rooms share same dates)
    const firstRoom = selectedRooms[0];
    document.getElementById('room_id').value = selectedRooms.map(r => r.roomId).join(',');
    document.getElementById('check_in_date').value = currentSearchDates.checkIn;
    document.getElementById('check_out_date').value = currentSearchDates.checkOut;
    document.getElementById('check_in_time_hidden').value = currentSearchDates.checkInTime;
    document.getElementById('check_out_time_hidden').value = currentSearchDates.checkOutTime;
    document.getElementById('price_per_night').value = selectedRooms.reduce((sum, r) => sum + r.pricePerNight, 0);
    document.getElementById('totalNights').value = currentSearchDates.nights;
    
    // Build selected rooms info display
    let roomsHtml = '<div class="space-y-2">';
    roomsHtml += '<div class="flex items-center justify-between"><div>';
    roomsHtml += `<p class="font-bold text-blue-900 text-lg"><i class="fas fa-bed mr-2"></i>${selectedRooms.length} Room${selectedRooms.length > 1 ? 's' : ''} Selected</p>`;
    roomsHtml += '<div class="flex flex-wrap gap-2 mt-2">';
    selectedRooms.forEach(room => {
        roomsHtml += `<span class="bg-white border border-primary-300 rounded px-2 py-1 text-sm font-semibold text-primary-800">Room ${room.roomNumber} - ৳${room.pricePerNight.toLocaleString()}/night</span>`;
    });
    roomsHtml += '</div>';
    roomsHtml += `<p class="text-primary-700 mt-2">Check-in: ${currentSearchDates.checkIn} at ${currentSearchDates.checkInTime} | Check-out: ${currentSearchDates.checkOut} at ${currentSearchDates.checkOutTime}</p>`;
    roomsHtml += `<p class="text-primary-700">${currentSearchDates.nights} night${currentSearchDates.nights > 1 ? 's' : ''}</p>`;
    roomsHtml += '</div>';
    roomsHtml += '<button type="button" onclick="resetRoomSelection()" class="text-red-600 hover:text-red-800"><i class="fas fa-times-circle text-2xl"></i></button>';
    roomsHtml += '</div></div>';
    
    document.getElementById('selectedRoomInfo').innerHTML = roomsHtml;
    
    recalculateAmount();
    document.getElementById('bookingForm').classList.remove('hidden');
    document.getElementById('bookingForm').scrollIntoView({behavior: 'smooth'});
}

function resetRoomSelection() {
    selectedRooms = [];
    updateSelectedRoomsPanel();
    document.getElementById('bookingForm').classList.add('hidden');
    document.getElementById('roomResults').innerHTML = '';
}

// Additional Guests
function addAdditionalGuest() {
    const index = additionalGuests.length;
    additionalGuests.push({name: '', nid: '', phone: '', company_name: ''});
    
    const guestHtml = `
        <div class="border-2 border-primary-200 rounded-lg p-4 mb-3 bg-primary-50" id="guest-${index}">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-primary-900">Guest #${index + 2}</h3>
                <button type="button" onclick="removeAdditionalGuest(${index})" class="text-red-600 hover:text-red-800 font-semibold">
                    <i class="fas fa-trash mr-1"></i>Remove
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                    <input type="text" id="guest_name_${index}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">NID</label>
                    <input type="text" id="guest_nid_${index}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Phone</label>
                    <input type="tel" id="guest_phone_${index}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Company</label>
                    <input type="text" id="guest_company_${index}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('additionalGuestsList').insertAdjacentHTML('beforeend', guestHtml);
    
    // Auto-increment number of guests
    updateGuestCount();
}

function removeAdditionalGuest(index) {
    document.getElementById(`guest-${index}`).remove();
    additionalGuests[index] = null;
    
    // Update guest count after removal
    updateGuestCount();
}

function updateGuestCount() {
    const activeGuests = additionalGuests.filter(g => g !== null).length;
    document.getElementById('number_of_guests').value = 1 + activeGuests;
}

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

// Calculations
function recalculateAmount() {
    if (selectedRooms.length === 0) return;
    
    const discountType = document.getElementById('discount_type').value;
    document.getElementById('discount_percentage_div').classList.toggle('hidden', discountType !== 'percentage');
    document.getElementById('discount_amount_div').classList.toggle('hidden', discountType !== 'flat');
    
    // Calculate base amount for all selected rooms
    const baseAmount = selectedRooms.reduce((sum, room) => sum + (room.nights * room.pricePerNight), 0);
    document.getElementById('baseAmount').value = baseAmount.toFixed(2);
    
    // total_amount stores only the base room rent (VAT is calculated dynamically in display)
    document.getElementById('total_amount').value = baseAmount.toFixed(2);
    
    // VAT (stored separately, calculated dynamically in display)
    const vatEnabled = document.getElementById('vat_enabled').checked;
    const vatAmount = vatEnabled ? (baseAmount * 0.15) : 0;
    document.getElementById('vat_amount').value = vatAmount.toFixed(2);
    
    // Calculate display grand total for UI only
    let displayTotal = baseAmount;
    if (vatEnabled) displayTotal += vatAmount;
    
    // Discount
    if (discountType === 'percentage') {
        const discountPercentage = parseFloat(document.getElementById('discount_percentage').value) || 0;
        const discountAmount = (displayTotal * discountPercentage) / 100;
        displayTotal -= discountAmount;
    } else if (discountType === 'flat') {
        const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
        displayTotal -= discountAmount;
    }
    
    // Extra charges
    const extraCharges = parseFloat(document.getElementById('extra_charges').value) || 0;
    displayTotal += extraCharges;
    
    calculateRemaining();
}

function calculateRemaining() {
    const baseAmount = parseFloat(document.getElementById('total_amount').value) || 0;
    const vatEnabled = document.getElementById('vat_enabled').checked;
    const vatAmount = vatEnabled ? (baseAmount * 0.15) : 0;
    
    let grandTotal = baseAmount + vatAmount;
    
    // Apply discount
    const discountType = document.getElementById('discount_type').value;
    if (discountType === 'percentage') {
        const discountPercentage = parseFloat(document.getElementById('discount_percentage').value) || 0;
        grandTotal -= (grandTotal * discountPercentage) / 100;
    } else if (discountType === 'flat') {
        const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
        grandTotal -= discountAmount;
    }
    
    // Add extra charges
    const extraCharges = parseFloat(document.getElementById('extra_charges').value) || 0;
    grandTotal += extraCharges;
    
    const advance = parseFloat(document.getElementById('advance_payment').value) || 0;
    document.getElementById('remaining_payment').value = (grandTotal - advance).toFixed(2);
}

// Form Submission - Single booking with multiple rooms
async function submitBooking(e) {
    e.preventDefault();
    
    // Check if we're adding to existing booking
    const existingBookingId = document.getElementById('existing_booking_id')?.value;
    
    // Validate required fields before submission
    const customerName = document.getElementById('customer_name').value;
    const customerPhone = document.getElementById('customer_phone').value;
    
    if (selectedRooms.length === 0) {
        showGlobalModal('error', 'Please select at least one room!');
        return;
    }
    if (!currentSearchDates.checkIn || !currentSearchDates.checkOut) {
        showGlobalModal('error', 'Please select check-in and check-out dates!');
        return;
    }
    // Only validate customer info for new bookings
    if (!existingBookingId && (!customerName || !customerPhone)) {
        showGlobalModal('error', 'Please enter customer name and phone!');
        return;
    }
    
    // Show loading
    const submitBtn = document.querySelector('#bookingForm button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>' + (existingBookingId ? 'Adding Rooms...' : 'Creating Booking...');
    submitBtn.disabled = true;
    submitBtn.disabled = true;
    
    // Get additional guests
    const guestList = [];
    additionalGuests.forEach((guest, index) => {
        if (guest !== null) {
            const name = document.getElementById(`guest_name_${index}`)?.value;
            const nid = document.getElementById(`guest_nid_${index}`)?.value;
            const phone = document.getElementById(`guest_phone_${index}`)?.value;
            const company = document.getElementById(`guest_company_${index}`)?.value || '';
            if (name || phone) {
                guestList.push({name, nid, phone, company_name: company});
            }
        }
    });
    
    try {
        const formData = new FormData();
        
        // Prepare rooms data for single booking with multiple rooms
        const roomsData = selectedRooms.map(room => ({
            roomId: room.roomId,
            roomNumber: room.roomNumber,
            pricePerNight: room.pricePerNight
        }));
        formData.append('rooms_data', JSON.stringify(roomsData));
        
        // Check if we're adding to existing booking
        if (existingBookingId) {
            formData.append('existing_booking_id', existingBookingId);
            
            // For existing booking, only send rooms data
            const response = await fetch('{{ route("admin.premium-booking.book") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            const data = await response.json();
            
            if (data.success) {
                showGlobalModal('success', data.message);
                setTimeout(() => { window.location.href = '{{ route("admin.bookings.index") }}/' + existingBookingId; }, 1500);
            } else {
                showGlobalModal('error', data.message || 'Failed to add rooms!');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
            return;
        }
        
        // Date/time fields
        formData.append('check_in_date', currentSearchDates.checkIn);
        formData.append('check_out_date', currentSearchDates.checkOut);
        formData.append('check_in_time', currentSearchDates.checkInTime);
        formData.append('check_out_time', currentSearchDates.checkOutTime);
        
        // Customer info
        formData.append('customer_name', customerName);
        formData.append('customer_nid', document.getElementById('customer_nid').value);
        formData.append('customer_phone', customerPhone);
        formData.append('customer_whatsapp', document.getElementById('customer_whatsapp').value);
        formData.append('customer_email', document.getElementById('customer_email').value);
        formData.append('passport_number', document.getElementById('passport_number').value);
        formData.append('customer_address', document.getElementById('customer_address').value);
        formData.append('company_name', document.getElementById('company_name').value);
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
        
        // Payment - full amounts for single booking
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
        formData.append('bkash_number', document.getElementById('bkash_number').value || '');
        formData.append('bank_name', document.getElementById('bank_name').value || '');
        
        // Additional guests
        if (guestList.length > 0) {
            formData.append('additional_guests', JSON.stringify(guestList));
        }
        
        const response = await fetch('{{ route("admin.premium-booking.book") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });

        const data = await response.json();
        
        if (data.success) {
            const roomCount = selectedRooms.length;
            showGlobalModal('success', `Booking created successfully with ${roomCount} room${roomCount > 1 ? 's' : ''}!`);
            setTimeout(() => { window.location.href = '{{ route("admin.bookings.index") }}'; }, 1500);
        } else {
            showGlobalModal('error', data.message || 'Booking failed!');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    } catch (error) {
        console.error('Booking error:', error);
        showGlobalModal('error', 'Error creating booking');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

function resetAll() {
    showConfirmModal('Are you sure you want to reset all fields?', function() {
        location.reload();
    });
}

// Initialize discount type change
document.getElementById('discount_type').addEventListener('change', recalculateAmount);

// Pre-fill customer info from URL parameters
(function() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('phone')) {
        document.getElementById('customer_phone').value = params.get('phone');
    }
    if (params.get('name')) {
        document.getElementById('customer_name').value = decodeURIComponent(params.get('name'));
    }
    if (params.get('nid')) {
        document.getElementById('customer_nid').value = params.get('nid');
    }
    if (params.get('address')) {
        document.getElementById('customer_address').value = decodeURIComponent(params.get('address'));
    }
    if (params.get('company')) {
        document.getElementById('company_name').value = decodeURIComponent(params.get('company'));
    }
    
    // Pre-fill dates and auto-search if provided
    if (params.get('checkin')) {
        document.getElementById('checkInDate').value = params.get('checkin');
    }
    if (params.get('checkout')) {
        document.getElementById('checkOutDate').value = params.get('checkout');
    }
    
    // Auto-search rooms if dates are pre-filled
    if (params.get('checkin') && params.get('checkout')) {
        setTimeout(() => {
            document.getElementById('searchRoomsForm').dispatchEvent(new Event('submit'));
        }, 500);
    }
})();
</script>
@endsection
