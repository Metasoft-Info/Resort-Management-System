@extends('layouts.admin')

@section('title', 'Bookings')
@section('header', 'Bookings Management')

@section('content')
<!-- Filters Section -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <form method="GET" action="{{ route('admin.bookings.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">অনুসন্ধান</label>
                <div class="flex gap-2">
                    <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="name" {{ request('type') == 'name' ? 'selected' : '' }}>নাম</option>
                        <option value="phone" {{ request('type') == 'phone' ? 'selected' : '' }}>ফোন</option>
                        <option value="email" {{ request('type') == 'email' ? 'selected' : '' }}>ইমেইল</option>
                    </select>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="অনুসন্ধান..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">বুকিং স্ট্যাটাস</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>সব</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>পেন্ডিং</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>কনফার্মড</option>
                    <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>চেক-ইন</option>
                    <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>চেক-আউট</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>বাতিল</option>
                </select>
            </div>

            <!-- Payment Status Filter -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">পেমেন্ট স্ট্যাটাস</label>
                <select name="payment_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="all" {{ request('payment_status') == 'all' ? 'selected' : '' }}>সব</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>পেন্ডিং</option>
                    <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>আংশিক</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>পরিশোধিত</option>
                    <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>ফেরত</option>
                </select>
            </div>

            <!-- Quick Date Filters -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">তারিখ ফিল্টার</label>
                <select id="dateFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">কাস্টম</option>
                    <option value="today">আজ</option>
                    <option value="yesterday">গতকাল</option>
                    <option value="this_week">এই সপ্তাহ</option>
                    <option value="this_month">এই মাস</option>
                    <option value="last_month">গত মাস</option>
                </select>
            </div>
        </div>

        <!-- Date Range Filters -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200">
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">চেক-ইন তারিখ</label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="date" name="check_in_from" value="{{ request('check_in_from') }}" placeholder="থেকে" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <input type="date" name="check_in_to" value="{{ request('check_in_to') }}" placeholder="পর্যন্ত" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">চেক-আউট তারিখ</label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="date" name="check_out_from" value="{{ request('check_out_from') }}" placeholder="থেকে" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <input type="date" name="check_out_to" value="{{ request('check_out_to') }}" placeholder="পর্যন্ত" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">বুকিং তারিখ</label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="date" name="booking_from" value="{{ request('booking_from') }}" placeholder="থেকে" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <input type="date" name="booking_to" value="{{ request('booking_to') }}" placeholder="পর্যন্ত" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
        </div>

        <!-- Filter Buttons -->
        <div class="flex gap-3 pt-4">
            <button type="submit" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-2 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-md">
                <i class="fas fa-filter mr-2"></i>ফিল্টার প্রয়োগ করুন
            </button>
            <a href="{{ route('admin.bookings.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                <i class="fas fa-redo mr-2"></i>রিসেট
            </a>
        </div>
    </form>
</div>

<!-- Bookings Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-primary-50 to-primary-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">আইডি</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">কাস্টমার</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">রুম</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">চেক-ইন</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">চেক-আউট</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">মোট টাকা</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">রেফারেন্স</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">তৈরি করেছেন</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">স্ট্যাটাস</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($bookings as $booking)
                    <tr class="hover:bg-primary-50 transition">
                        <td class="px-4 py-3 font-semibold text-gray-700">#{{ $booking->id }}</td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-800">{{ $booking->customer_name }}</div>
                            <div class="text-xs text-gray-500"><i class="fas fa-phone mr-1"></i>{{ $booking->customer_phone }}</div>
                            @if($booking->customer_email)
                            <div class="text-xs text-gray-500"><i class="fas fa-envelope mr-1"></i>{{ $booking->customer_email }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php $allRooms = $booking->getAllRooms(); @endphp
                            @if($allRooms->count() > 1)
                                <div class="font-medium text-gray-800">{{ $allRooms->count() }} Rooms</div>
                                <div class="text-xs text-gray-500">{{ $allRooms->pluck('room_number')->implode(', ') }}</div>
                            @elseif($allRooms->count() == 1)
                                <div class="font-medium text-gray-800">{{ $allRooms->first()->roomType->name ?? 'Room' }}</div>
                                <div class="text-xs text-gray-500">{{ $allRooms->first()->room_number }}</div>
                            @else
                                <div class="text-gray-500">No room</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-gray-700">{{ $booking->check_in_date->format('d M Y') }}</div>
                            @if($booking->check_in_time)
                            <div class="text-xs text-gray-500">{{ $booking->check_in_time }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-gray-700">{{ $booking->check_out_date->format('d M Y') }}</div>
                            @if($booking->check_out_time)
                            <div class="text-xs text-gray-500">{{ $booking->check_out_time }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php 
                                $calculatedTotal = $booking->getCalculatedTotal();
                                $calculatedRemaining = $booking->getCalculatedRemaining();
                            @endphp
                            <div class="font-bold text-gray-800">৳{{ number_format($calculatedTotal, 2) }}</div>
                            @if($booking->advance_payment > 0)
                            <div class="text-xs text-primary-600">অগ্রিম: ৳{{ number_format($booking->advance_payment, 2) }}</div>
                            @endif
                            @if($calculatedRemaining > 0)
                            <div class="text-xs text-red-600">বাকি: ৳{{ number_format($calculatedRemaining, 2) }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($booking->reference_name)
                            <div class="font-medium text-gray-800">{{ $booking->reference_name }}</div>
                            @if($booking->reference_phone)
                            <div class="text-xs text-gray-500">{{ $booking->reference_phone }}</div>
                            @endif
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($booking->createdBy)
                            <div class="font-medium text-gray-800">{{ $booking->createdBy->name }}</div>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                @if($booking->status == 'confirmed') bg-primary-100 text-primary-800
                                @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($booking->status == 'checked_in') bg-primary-100 text-primary-800
                                @elseif($booking->status == 'checked_out') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800
                                @endif">
                                @if($booking->status == 'confirmed') কনফার্মড
                                @elseif($booking->status == 'pending') পেন্ডিং
                                @elseif($booking->status == 'checked_in') চেক-ইন
                                @elseif($booking->status == 'checked_out') চেক-আউট
                                @else বাতিল
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="text-primary-600 hover:text-primary-800 transition" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.bookings.edit', $booking) }}" class="text-primary-600 hover:text-primary-800 transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                            <div>কোন বুকিং পাওয়া যায়নি</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $bookings->appends(request()->query())->links() }}
</div>

<script>
// Quick date filter functionality
document.getElementById('dateFilter').addEventListener('change', function(e) {
    const today = new Date();
    let startDate, endDate;
    
    switch(e.target.value) {
        case 'today':
            startDate = endDate = today.toISOString().split('T')[0];
            break;
        case 'yesterday':
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            startDate = endDate = yesterday.toISOString().split('T')[0];
            break;
        case 'this_week':
            const weekStart = new Date(today);
            weekStart.setDate(today.getDate() - today.getDay());
            startDate = weekStart.toISOString().split('T')[0];
            endDate = today.toISOString().split('T')[0];
            break;
        case 'this_month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            endDate = today.toISOString().split('T')[0];
            break;
        case 'last_month':
            const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            startDate = lastMonth.toISOString().split('T')[0];
            const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
            endDate = lastMonthEnd.toISOString().split('T')[0];
            break;
    }
    
    if (startDate) {
        document.querySelector('input[name="booking_from"]').value = startDate;
        document.querySelector('input[name="booking_to"]').value = endDate;
    }
});
</script>
@endsection
