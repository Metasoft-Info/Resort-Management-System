@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">কনভেনশন বুকিং রিপোর্ট</h1>
        <p class="text-gray-600 mt-2">সকল কনভেনশন হল বুকিং এর বিস্তারিত রিপোর্ট</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('admin.reports.convention-bookings') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">শুরুর তারিখ</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">শেষ তারিখ</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">স্ট্যাটাস</label>
                    <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">সব</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>নিশ্চিত</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>পেন্ডিং</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>বাতিল</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>সম্পন্ন</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition shadow-lg">
                        <i class="fas fa-filter mr-2"></i>ফিল্টার করুন
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-green-100 text-sm">মোট বুকিং</p>
            <p class="text-3xl font-bold mt-2">{{ $bookings->total() }}</p>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-blue-100 text-sm">মোট আয়</p>
            <p class="text-3xl font-bold mt-2">৳{{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-purple-100 text-sm">গড় বুকিং মূল্য</p>
            <p class="text-3xl font-bold mt-2">৳{{ $bookings->total() > 0 ? number_format($totalRevenue / $bookings->total(), 2) : 0 }}</p>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b bg-gradient-to-r from-green-50 to-emerald-50 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-800">বুকিং তালিকা</h3>
            <button onclick="window.print()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-print mr-2"></i>প্রিন্ট করুন
            </button>
        </div>
        <table class="min-w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">ID</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">গ্রাহক</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">হল</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">তারিখ</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">সময়</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">টাকা</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">স্ট্যাটাস</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($bookings as $booking)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-semibold text-gray-800">#{{ $booking->id }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $booking->customer_name }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $booking->conventionHall->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $booking->event_date }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $booking->time_slot }}</td>
                    <td class="px-6 py-4 font-semibold text-gray-800">৳{{ number_format($booking->total_amount, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            @if($booking->status == 'confirmed') bg-green-100 text-green-800
                            @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">কোনো বুকিং পাওয়া যায়নি</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $bookings->links() }}</div>
</div>
@endsection
