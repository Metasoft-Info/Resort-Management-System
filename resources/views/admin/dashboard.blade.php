@extends('layouts.admin')
@section('content')
<div class="p-6 space-y-6">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-primary-600 via-primary-700 to-primary-700 rounded-2xl shadow-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold mb-2">Welcome, {{ auth()->user()->name }}! 👋</h1>
                <p class="text-primary-100 text-lg">Tufan Resort Management System</p>
                <p class="text-primary-200 text-sm mt-2"><i class="fas fa-calendar mr-2"></i>{{ date('l, F j, Y') }}</p>
            </div>
            <div class="text-6xl opacity-20"><i class="fas fa-chart-line"></i></div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-primary-500 hover:shadow-xl transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold mb-2">Total Bookings</p>
                    <p class="text-4xl font-bold text-primary-600">{{ $stats['total_bookings'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">All room bookings</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-calendar-check text-2xl text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-primary-500 hover:shadow-xl transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold mb-2">Active Bookings</p>
                    <p class="text-4xl font-bold text-primary-600">{{ $stats['active_bookings'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">Currently active</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-check-circle text-2xl text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-primary-500 hover:shadow-xl transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold mb-2">Available Rooms</p>
                    <p class="text-4xl font-bold text-primary-600">{{ $stats['available_rooms'] }}<span class="text-xl text-gray-400">/{{ $stats['total_rooms'] }}</span></p>
                    <p class="text-xs text-gray-400 mt-1">Ready to book</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-bed text-2xl text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-primary-500 hover:shadow-xl transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold mb-2">Total Revenue</p>
                    <p class="text-3xl font-bold text-primary-600">৳{{ number_format($stats['total_revenue']) }}</p>
                    <p class="text-xs text-gray-400 mt-1">All time earnings</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-bangladeshi-taka-sign text-2xl text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-primary-500 hover:shadow-xl transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold mb-2">Convention Bookings</p>
                    <p class="text-4xl font-bold text-primary-600">{{ $stats['convention_bookings'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">Hall reservations</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-building text-2xl text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-primary-500 hover:shadow-xl transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold mb-2">Today's Check-ins</p>
                    <p class="text-4xl font-bold text-primary-600">{{ $stats['today_checkins'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">Today's arrivals</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-door-open text-2xl text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl shadow-lg p-6 hover:shadow-xl transition transform hover:-translate-y-1 lg:col-span-2">
            <h3 class="text-white font-bold text-lg mb-4"><i class="fas fa-bolt mr-2"></i>Quick Actions</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.premium-booking.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 backdrop-blur-sm text-white rounded-lg p-3 text-center transition">
                    <i class="fas fa-plus-circle text-2xl mb-2"></i>
                    <p class="text-sm font-semibold">New Booking</p>
                </a>
                <a href="{{ route('admin.premium-booking.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 backdrop-blur-sm text-white rounded-lg p-3 text-center transition">
                    <i class="fas fa-search text-2xl mb-2"></i>
                    <p class="text-sm font-semibold">Search Rooms</p>
                </a>
                <a href="{{ route('admin.todays-summary') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 backdrop-blur-sm text-white rounded-lg p-3 text-center transition">
                    <i class="fas fa-chart-bar text-2xl mb-2"></i>
                    <p class="text-sm font-semibold">Today Summary</p>
                </a>
                <a href="{{ route('admin.reports.room-bookings') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 backdrop-blur-sm text-white rounded-lg p-3 text-center transition">
                    <i class="fas fa-file-alt text-2xl mb-2"></i>
                    <p class="text-sm font-semibold">Reports</p>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Search Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center mb-6 pb-4 border-b">
                <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-bed text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Check Room Availability</h3>
                    <p class="text-sm text-gray-500">Search available rooms</p>
                </div>
            </div>
            <form id="roomSearchForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check-in Date</label>
                    <input type="date" id="roomCheckIn" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check-out Date</label>
                    <input type="date" id="roomCheckOut" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" required>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white py-3 rounded-lg font-semibold hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
                    <i class="fas fa-search mr-2"></i>Search Available Rooms
                </button>
            </form>
            <div id="roomResults" class="mt-4"></div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center mb-6 pb-4 border-b">
                <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-building text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Check Hall Availability</h3>
                    <p class="text-sm text-gray-500">Search convention halls</p>
                </div>
            </div>
            <form id="hallSearchForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Event Date</label>
                    <input type="date" id="hallDate" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Time Slot</label>
                    <select id="hallTime" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="morning">Morning (8AM - 2PM)</option>
                        <option value="night">Night (6PM - 11PM)</option>
                        <option value="full_day">Full Day (8AM - 11PM)</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white py-3 rounded-lg font-semibold hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
                    <i class="fas fa-search mr-2"></i>Search Available Halls
                </button>
            </form>
            <div id="hallResults" class="mt-4"></div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-primary-50 to-primary-50 px-6 py-4 border-b">
            <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-clock-rotate-left mr-3 text-primary-600"></i>
                Recent Bookings
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Guest</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Room</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Check-in</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentBookings as $booking)
                        <tr class="hover:bg-primary-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-700">#{{ $booking->id }}</td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-800">{{ $booking->customer_name }}</div>
                                <div class="text-sm text-gray-500">{{ $booking->customer_phone }}</div>
                            </td>
                            <td class="px-6 py-4"><span class="font-semibold text-primary-600">{{ $booking->room->room_number ?? 'N/A' }}</span></td>
                            <td class="px-6 py-4 text-gray-700">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-bold text-primary-600">৳{{ number_format($booking->total_amount) }}</td>
                            <td class="px-6 py-4">
                                @if($booking->status == 'confirmed')
                                    <span class="px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-xs font-bold"><i class="fas fa-check-circle mr-1"></i>Confirmed</span>
                                @elseif($booking->status == 'pending')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold"><i class="fas fa-clock mr-1"></i>Pending</span>
                                @elseif($booking->status == 'checked_in')
                                    <span class="px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-xs font-bold"><i class="fas fa-door-open mr-1"></i>Checked In</span>
                                @else
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">{{ $booking->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.bookings.edit', $booking) }}" class="text-primary-600 hover:text-primary-800"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-400 text-5xl mb-3"><i class="fas fa-inbox"></i></div>
                                <p class="text-gray-500 font-semibold">No bookings found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Room Status Grid -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-primary-50 to-primary-50 px-6 py-4 border-b flex items-center justify-between">
            <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-bed mr-3 text-primary-600"></i>
                রুম স্ট্যাটাস
            </h3>
            <div class="flex items-center gap-2">
                <input type="text" id="roomSearch" placeholder="রুম নম্বর খুঁজুন..." class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
            </div>
        </div>
        <div class="p-6">
            <div id="roomStatusGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($roomsWithStatus as $rs)
                <div class="room-card border rounded-xl p-4 transition hover:shadow-lg {{ $rs['status'] == 'occupied' ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }}" data-room="{{ strtolower($rs['room']->room_number) }}">
                    <div class="text-center">
                        <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center mb-2 {{ $rs['status'] == 'occupied' ? 'bg-red-200' : 'bg-green-200' }}">
                            <i class="fas fa-bed text-xl {{ $rs['status'] == 'occupied' ? 'text-red-600' : 'text-green-600' }}"></i>
                        </div>
                        <h4 class="font-bold text-lg text-gray-800">{{ $rs['room']->room_number }}</h4>
                        <p class="text-xs text-gray-500 mb-2">{{ $rs['room']->roomType->name ?? '' }}</p>
                        
                        @if($rs['status'] == 'occupied')
                            <span class="inline-block px-2 py-1 bg-red-200 text-red-800 text-xs font-bold rounded-full mb-2">বুকড</span>
                            @if($rs['current_booking'])
                                <p class="text-xs text-gray-600"><i class="fas fa-user mr-1"></i>{{ Str::limit($rs['current_booking']->customer_name, 12) }}</p>
                                <p class="text-xs text-red-600 font-semibold mt-1">
                                    <i class="fas fa-calendar-check mr-1"></i>খালি হবে: {{ \Carbon\Carbon::parse($rs['current_booking']->check_out_date)->format('d/m') }}
                                </p>
                            @endif
                        @else
                            <span class="inline-block px-2 py-1 bg-green-200 text-green-800 text-xs font-bold rounded-full mb-2">ফাঁকা</span>
                            @if($rs['upcoming_booking'])
                                <p class="text-xs text-orange-600 font-semibold">
                                    <i class="fas fa-clock mr-1"></i>পরবর্তী: {{ \Carbon\Carbon::parse($rs['upcoming_booking']->check_in_date)->format('d/m') }}
                                </p>
                            @else
                                <p class="text-xs text-green-600 font-semibold">
                                    <i class="fas fa-check mr-1"></i>সম্পূর্ণ ফ্রি
                                </p>
                            @endif
                        @endif
                        
                        <a href="{{ route('admin.premium-booking.index') }}?room={{ $rs['room']->id }}" 
                           class="mt-3 inline-block w-full bg-primary-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-primary-700 transition">
                            <i class="fas fa-plus mr-1"></i>বুক করুন
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Room status legend -->
            <div class="mt-6 flex items-center justify-center gap-6 text-sm">
                <span class="flex items-center"><span class="w-3 h-3 bg-green-400 rounded-full mr-2"></span>ফাঁকা ({{ collect($roomsWithStatus)->where('status', 'available')->count() }})</span>
                <span class="flex items-center"><span class="w-3 h-3 bg-red-400 rounded-full mr-2"></span>বুকড ({{ collect($roomsWithStatus)->where('status', 'occupied')->count() }})</span>
            </div>
        </div>
    </div>
</div>

<script>
// Room search filter
document.getElementById('roomSearch').addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    document.querySelectorAll('.room-card').forEach(card => {
        const roomNumber = card.dataset.room;
        card.style.display = roomNumber.includes(query) ? 'block' : 'none';
    });
});

document.getElementById('roomSearchForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const checkIn = document.getElementById('roomCheckIn').value;
    const checkOut = document.getElementById('roomCheckOut').value;
    const resultsDiv = document.getElementById('roomResults');
    resultsDiv.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-primary-600"></i></div>';
    
    try {
        const response = await fetch(`/admin/dashboard/search-rooms?checkIn=${checkIn}&checkOut=${checkOut}`, {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.json();
        const rooms = data.availableRooms || [];
        
        if (rooms.length === 0) {
            resultsDiv.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center"><p class="text-red-600 font-semibold"><i class="fas fa-times-circle mr-2"></i>No available rooms found for these dates</p></div>';
        } else {
            let roomCardsHtml = `<div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="font-semibold text-primary-700 mb-3"><i class="fas fa-check-circle mr-2"></i>${rooms.length} available rooms found:</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">`;
            rooms.forEach(room => {
                const roomImage = room.images && room.images.length > 0 ? room.images[0] : null;
                const roomType = room.room_type?.name || room.type || '';
                roomCardsHtml += `
                    <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
                        <div class="h-20 bg-gradient-to-br from-blue-400 to-primary-500 relative">
                            ${roomImage ? `<img src="/storage/${roomImage}" alt="Room ${room.room_number}" class="w-full h-full object-cover">` : `<div class="w-full h-full flex items-center justify-center"><i class="fas fa-bed text-2xl text-white/50"></i></div>`}
                        </div>
                        <div class="p-2 text-center">
                            <p class="font-bold text-gray-800">Room ${room.room_number}</p>
                            ${roomType ? `<p class="text-xs text-gray-500">${roomType}</p>` : ''}
                            <p class="text-xs text-primary-600 font-semibold">৳${parseFloat(room.price_per_night || room.room_type?.base_price || 0).toLocaleString()}/night</p>
                        </div>
                    </div>`;
            });
            roomCardsHtml += `</div><a href="/admin/premium-booking" class="mt-3 inline-block bg-primary-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-primary-700"><i class="fas fa-plus mr-1"></i>Create Booking</a></div>`;
            resultsDiv.innerHTML = roomCardsHtml;
        }
    } catch (error) {
        console.error('Room search error:', error);
        resultsDiv.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center"><p class="text-red-600">Error loading rooms</p></div>';
    }
});

document.getElementById('hallSearchForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const date = document.getElementById('hallDate').value;
    const slot = document.getElementById('hallTime').value;
    const resultsDiv = document.getElementById('hallResults');
    resultsDiv.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-primary-600"></i></div>';
    
    try {
        const response = await fetch(`/admin/dashboard/search-halls?date=${date}&slot=${slot}`, {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.json();
        const bookedIds = data.bookedHallIds || [];
        const allHalls = @json($allHalls ?? []);
        const availableHalls = allHalls.filter(h => !bookedIds.includes(h.id));
        
        if (availableHalls.length === 0) {
            resultsDiv.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center"><p class="text-red-600 font-semibold"><i class="fas fa-times-circle mr-2"></i>এই সময়ের জন্য কোনো হল উপলব্ধ নেই</p></div>';
        } else {
            let hallCardsHtml = '<div class="grid gap-4">';
            availableHalls.forEach(h => {
                const images = h.images || [];
                const firstImage = images.length > 0 ? images[0] : null;
                const imageHtml = firstImage 
                    ? `<img src="/storage/${firstImage}" alt="${h.name}" class="w-20 h-20 object-cover rounded-lg">`
                    : `<div class="w-20 h-20 bg-gradient-to-br from-primary-100 to-primary-100 rounded-lg flex items-center justify-center"><i class="fas fa-building text-2xl text-green-300"></i></div>`;
                
                hallCardsHtml += `
                    <div class="flex items-center gap-4 bg-green-50 border border-green-200 rounded-lg p-3">
                        ${imageHtml}
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-800">${h.name}</h4>
                            <p class="text-sm text-gray-600"><i class="fas fa-users mr-1"></i>ধারণক্ষমতা: ${h.max_capacity} জন</p>
                            <p class="text-sm font-bold text-primary-600">৳${Number(h.price_per_day).toLocaleString()}/দিন</p>
                        </div>
                        <span class="px-2 py-1 bg-primary-100 text-primary-700 text-xs font-bold rounded-full">✅ উপলব্ধ</span>
                    </div>
                `;
            });
            hallCardsHtml += '</div>';
            hallCardsHtml += '<a href="/admin/premium-convention" class="mt-4 inline-flex items-center bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700"><i class="fas fa-plus mr-2"></i>বুকিং তৈরি করুন</a>';
            resultsDiv.innerHTML = hallCardsHtml;
        }
    } catch (error) {
        console.error('Hall search error:', error);
        resultsDiv.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center"><p class="text-red-600">Error loading halls</p></div>';
    }
});

// Set default dates
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('roomCheckIn').value = today;
    document.getElementById('hallDate').value = today;
    
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('roomCheckOut').value = tomorrow.toISOString().split('T')[0];
});
</script>
@endsection
