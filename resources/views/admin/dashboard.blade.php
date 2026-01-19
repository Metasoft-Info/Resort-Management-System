@extends('layouts.admin')
@section('content')
<div class="p-6 space-y-6">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-purple-700 rounded-2xl shadow-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold mb-2">Welcome, {{ auth()->user()->name }}! 👋</h1>
                <p class="text-blue-100 text-lg">Tufan Resort Management System</p>
                <p class="text-blue-200 text-sm mt-2"><i class="fas fa-calendar mr-2"></i>{{ date('l, F j, Y') }}</p>
            </div>
            <div class="text-6xl opacity-20"><i class="fas fa-chart-line"></i></div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold mb-2">Total Bookings</p>
                    <p class="text-4xl font-bold text-blue-600">{{ $stats['total_bookings'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">All room bookings</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-calendar-check text-2xl text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold mb-2">Active Bookings</p>
                    <p class="text-4xl font-bold text-green-600">{{ $stats['active_bookings'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">Currently active</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-check-circle text-2xl text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold mb-2">Available Rooms</p>
                    <p class="text-4xl font-bold text-purple-600">{{ $stats['available_rooms'] }}<span class="text-xl text-gray-400">/{{ $stats['total_rooms'] }}</span></p>
                    <p class="text-xs text-gray-400 mt-1">Ready to book</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-bed text-2xl text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500 hover:shadow-xl transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold mb-2">Total Revenue</p>
                    <p class="text-3xl font-bold text-orange-600">৳{{ number_format($stats['total_revenue']) }}</p>
                    <p class="text-xs text-gray-400 mt-1">All time earnings</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-bangladeshi-taka-sign text-2xl text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-cyan-500 hover:shadow-xl transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold mb-2">Convention Bookings</p>
                    <p class="text-4xl font-bold text-cyan-600">{{ $stats['convention_bookings'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">Hall reservations</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-building text-2xl text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-pink-500 hover:shadow-xl transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold mb-2">Today's Check-ins</p>
                    <p class="text-4xl font-bold text-pink-600">{{ $stats['today_checkins'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">Today's arrivals</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-door-open text-2xl text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 hover:shadow-xl transition transform hover:-translate-y-1 lg:col-span-2">
            <h3 class="text-white font-bold text-lg mb-4"><i class="fas fa-bolt mr-2"></i>Quick Actions</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.bookings.create') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 backdrop-blur-sm text-white rounded-lg p-3 text-center transition">
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
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
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
                    <input type="date" id="roomCheckIn" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check-out Date</label>
                    <input type="date" id="roomCheckOut" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition shadow-lg">
                    <i class="fas fa-search mr-2"></i>Search Available Rooms
                </button>
            </form>
            <div id="roomResults" class="mt-4"></div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center mb-6 pb-4 border-b">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
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
                    <input type="date" id="hallDate" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Time Slot</label>
                    <select id="hallTime" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="morning">Morning (8AM - 12PM)</option>
                        <option value="afternoon">Afternoon (12PM - 5PM)</option>
                        <option value="evening">Evening (5PM - 10PM)</option>
                        <option value="full_day">Full Day</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-3 rounded-lg font-semibold hover:from-green-700 hover:to-green-800 transition shadow-lg">
                    <i class="fas fa-search mr-2"></i>Search Available Halls
                </button>
            </form>
            <div id="hallResults" class="mt-4"></div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 px-6 py-4 border-b">
            <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-clock-rotate-left mr-3 text-blue-600"></i>
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
                        <tr class="hover:bg-blue-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-700">#{{ $booking->id }}</td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-800">{{ $booking->guest_name }}</div>
                                <div class="text-sm text-gray-500">{{ $booking->guest_phone }}</div>
                            </td>
                            <td class="px-6 py-4"><span class="font-semibold text-blue-600">{{ $booking->room->room_number ?? 'N/A' }}</span></td>
                            <td class="px-6 py-4 text-gray-700">{{ $booking->check_in_date }}</td>
                            <td class="px-6 py-4 font-bold text-green-600">৳{{ number_format($booking->total_amount) }}</td>
                            <td class="px-6 py-4">
                                @if($booking->status == 'confirmed')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold"><i class="fas fa-check-circle mr-1"></i>Confirmed</span>
                                @elseif($booking->status == 'pending')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold"><i class="fas fa-clock mr-1"></i>Pending</span>
                                @elseif($booking->status == 'checked_in')
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold"><i class="fas fa-door-open mr-1"></i>Checked In</span>
                                @else
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">{{ $booking->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.bookings.edit', $booking) }}" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
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
</div>

<script>
document.getElementById('roomSearchForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const checkIn = document.getElementById('roomCheckIn').value;
    const checkOut = document.getElementById('roomCheckOut').value;
    const resultsDiv = document.getElementById('roomResults');
    resultsDiv.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i></div>';
    
    try {
        const response = await fetch(`/admin/dashboard/search-rooms?checkIn=${checkIn}&checkOut=${checkOut}`);
        const rooms = await response.json();
        
        if (rooms.length === 0) {
            resultsDiv.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center"><p class="text-red-600 font-semibold">No available rooms found</p></div>';
        } else {
            resultsDiv.innerHTML = '<div class="bg-green-50 border border-green-200 rounded-lg p-4"><p class="font-semibold text-green-700 mb-2"><i class="fas fa-check-circle mr-2"></i>' + rooms.length + ' available rooms found:</p><ul class="space-y-1">' + rooms.map(r => `<li class="text-sm text-gray-700">• Room ${r.room_number} - ${r.room_type?.name || 'N/A'}</li>`).join('') + '</ul></div>';
        }
    } catch (error) {
        resultsDiv.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center"><p class="text-red-600">Error loading rooms</p></div>';
    }
});

document.getElementById('hallSearchForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const date = document.getElementById('hallDate').value;
    const timeSlot = document.getElementById('hallTime').value;
    const resultsDiv = document.getElementById('hallResults');
    resultsDiv.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-green-600"></i></div>';
    
    try {
        const response = await fetch(`/admin/dashboard/search-halls?date=${date}&timeSlot=${timeSlot}`);
        const halls = await response.json();
        
        if (halls.length === 0) {
            resultsDiv.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center"><p class="text-red-600 font-semibold">No available halls found</p></div>';
        } else {
            resultsDiv.innerHTML = '<div class="bg-green-50 border border-green-200 rounded-lg p-4"><p class="font-semibold text-green-700 mb-2"><i class="fas fa-check-circle mr-2"></i>' + halls.length + ' available halls found:</p><ul class="space-y-1">' + halls.map(h => `<li class="text-sm text-gray-700">• ${h.name} (Capacity: ${h.capacity})</li>`).join('') + '</ul></div>';
        }
    } catch (error) {
        resultsDiv.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center"><p class="text-red-600">Error loading halls</p></div>';
    }
});
</script>
@endsection
