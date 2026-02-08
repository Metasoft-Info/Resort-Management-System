@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8 print:mb-4">
        <h1 class="text-3xl font-bold text-gray-800">কনভেনশন বুকিং রিপোর্ট</h1>
        <p class="text-gray-600 mt-2 print:hidden">সকল কনভেনশন হল বুকিং এর বিস্তারিত রিপোর্ট</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 print:hidden">
        <form method="GET" action="{{ route('admin.reports.convention-bookings') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
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
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">পেমেন্ট স্ট্যাটাস</label>
                    <select name="payment_status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">সব</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>পরিশোধিত</option>
                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>আংশিক</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>বকেয়া</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">হল নির্বাচন</label>
                    <select name="hall_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">সব হল</option>
                        @foreach($halls as $hall)
                        <option value="{{ $hall->id }}" {{ request('hall_id') == $hall->id ? 'selected' : '' }}>{{ $hall->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">সময় স্লট</label>
                    <select name="time_slot" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">সব সময়</option>
                        <option value="morning" {{ request('time_slot') == 'morning' ? 'selected' : '' }}>সকাল</option>
                        <option value="evening" {{ request('time_slot') == 'evening' ? 'selected' : '' }}>সন্ধ্যা</option>
                        <option value="full_day" {{ request('time_slot') == 'full_day' ? 'selected' : '' }}>পুরো দিন</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">খুঁজুন</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="নাম / ফোন / প্রতিষ্ঠান" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
                        <i class="fas fa-filter mr-2"></i>ফিল্টার
                    </button>
                    <a href="{{ route('admin.reports.convention-bookings') }}" class="bg-gray-500 text-white px-4 py-3 rounded-lg hover:bg-gray-600 transition">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Print Header (visible only when printing) -->
    <div class="hidden print:block mb-4 text-center border-b-2 border-gray-800 pb-4">
        @if($resortInfo && $resortInfo->header_logo)
            <img src="{{ asset('storage/' . $resortInfo->header_logo) }}" alt="{{ $resortInfo->resort_name ?? 'Resort' }}" class="h-12 mx-auto mb-2">
        @endif
        <h1 class="text-2xl font-bold">{{ $resortInfo->resort_name ?? 'Resort' }}</h1>
        <p class="text-sm text-gray-600">{{ $resortInfo->address ?? '' }}</p>
        <h2 class="text-xl font-bold mt-3">কনভেনশন বুকিং রিপোর্ট</h2>
        <p class="text-sm text-gray-600">
            @if(request('start_date') && request('end_date'))
                তারিখ: {{ request('start_date') }} থেকে {{ request('end_date') }}
            @else
                সব তারিখ
            @endif
            @if(request('status'))
                | স্ট্যাটাস: {{ ucfirst(request('status')) }}
            @endif
        </p>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl shadow-lg p-4 text-white print:bg-primary-100 print:text-primary-800 print:rounded print:border print:border-green-300">
            <p class="text-green-100 text-xs print:text-primary-600">মোট বুকিং</p>
            <p class="text-2xl font-bold mt-1">{{ $totalBookings }}</p>
        </div>
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl shadow-lg p-4 text-white print:bg-primary-100 print:text-primary-800 print:rounded print:border print:border-blue-300">
            <p class="text-primary-100 text-xs print:text-primary-600">মোট আয়</p>
            <p class="text-2xl font-bold mt-1">৳{{ number_format($totalRevenue, 0) }}</p>
        </div>
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl shadow-lg p-4 text-white print:bg-primary-100 print:text-primary-800 print:rounded print:border print:border-purple-300">
            <p class="text-primary-100 text-xs print:text-primary-600">মোট অগ্রিম</p>
            <p class="text-2xl font-bold mt-1">৳{{ number_format($totalAdvance, 0) }}</p>
        </div>
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl shadow-lg p-4 text-white print:bg-primary-100 print:text-primary-800 print:rounded print:border print:border-primary-300">
            <p class="text-primary-100 text-xs print:text-primary-600">মোট বকেয়া</p>
            <p class="text-2xl font-bold mt-1">৳{{ number_format($totalRemaining, 0) }}</p>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden print:shadow-none print:border print:border-gray-300">
        <div class="p-4 border-b bg-gradient-to-r from-primary-50 to-emerald-50 flex justify-between items-center print:hidden">
            <h3 class="text-lg font-bold text-gray-800">বুকিং তালিকা ({{ $totalBookings }})</h3>
            <div class="flex gap-2">
                <button onclick="window.print()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-print mr-2"></i>প্রিন্ট
                </button>
                <a href="{{ route('admin.reports.convention-bookings.export', request()->query()) }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                    <i class="fas fa-file-excel mr-2"></i>এক্সপোর্ট
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 print:bg-gray-200">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-bold text-gray-700">ID</th>
                        <th class="px-3 py-3 text-left text-xs font-bold text-gray-700">গ্রাহক</th>
                        <th class="px-3 py-3 text-left text-xs font-bold text-gray-700">প্রতিষ্ঠান</th>
                        <th class="px-3 py-3 text-left text-xs font-bold text-gray-700">ফোন</th>
                        <th class="px-3 py-3 text-left text-xs font-bold text-gray-700">হল</th>
                        <th class="px-3 py-3 text-left text-xs font-bold text-gray-700">তারিখ</th>
                        <th class="px-3 py-3 text-left text-xs font-bold text-gray-700">সময়</th>
                        <th class="px-3 py-3 text-right text-xs font-bold text-gray-700">টাকা</th>
                        <th class="px-3 py-3 text-right text-xs font-bold text-gray-700">অগ্রিম</th>
                        <th class="px-3 py-3 text-right text-xs font-bold text-gray-700">বকেয়া</th>
                        <th class="px-3 py-3 text-center text-xs font-bold text-gray-700">পেমেন্ট</th>
                        <th class="px-3 py-3 text-center text-xs font-bold text-gray-700">স্ট্যাটাস</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-3 py-2 font-semibold text-gray-800">#{{ $booking->id }}</td>
                        <td class="px-3 py-2 text-gray-700">{{ $booking->customer_name }}</td>
                        <td class="px-3 py-2 text-gray-600">{{ $booking->organization_name ?? '-' }}</td>
                        <td class="px-3 py-2 text-gray-600">{{ $booking->customer_phone }}</td>
                        <td class="px-3 py-2 text-gray-700">{{ $booking->conventionHall->name ?? 'N/A' }}</td>
                        <td class="px-3 py-2 text-gray-600">{{ \Carbon\Carbon::parse($booking->event_date)->format('d/m/Y') }}</td>
                        <td class="px-3 py-2 text-gray-600">
                            @if($booking->time_slot == 'morning') সকাল
                            @elseif($booking->time_slot == 'evening') সন্ধ্যা
                            @elseif($booking->time_slot == 'full_day') পুরো দিন
                            @else {{ $booking->time_slot }}
                            @endif
                        </td>
                        <td class="px-3 py-2 font-semibold text-gray-800 text-right">৳{{ number_format($booking->total_amount, 0) }}</td>
                        <td class="px-3 py-2 text-primary-600 font-semibold text-right">৳{{ number_format($booking->advance_payment, 0) }}</td>
                        <td class="px-3 py-2 text-red-600 font-semibold text-right">৳{{ number_format($booking->remaining_payment, 0) }}</td>
                        <td class="px-3 py-2 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($booking->payment_status == 'paid') bg-primary-100 text-primary-800
                                @elseif($booking->payment_status == 'partial') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $booking->payment_status == 'paid' ? 'পরিশোধিত' : ($booking->payment_status == 'partial' ? 'আংশিক' : 'বকেয়া') }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($booking->status == 'confirmed') bg-primary-100 text-primary-800
                                @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                                @elseif($booking->status == 'completed') bg-primary-100 text-primary-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="12" class="px-6 py-12 text-center text-gray-500">কোনো বুকিং পাওয়া যায়নি</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 print:hidden">{{ $bookings->links() }}</div>

    <!-- Print Footer -->
    <div class="hidden print:block mt-4 pt-3 border-t border-gray-300 text-center text-xs text-gray-600">
        <p>প্রিন্ট তারিখ: {{ now()->format('d/m/Y H:i') }} | {{ $resortInfo->resort_name ?? 'Resort' }}</p>
    </div>
</div>

<style>
@media print {
    @page {
        size: A4 landscape;
        margin: 10mm;
    }
    body {
        font-size: 10px !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .print\:hidden {
        display: none !important;
    }
    .print\:block {
        display: block !important;
    }
    nav, header, aside, footer {
        display: none !important;
    }
    .p-6 {
        padding: 0 !important;
    }
    .mb-8 {
        margin-bottom: 8px !important;
    }
    .mb-6 {
        margin-bottom: 6px !important;
    }
}
</style>
@endsection
