@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-6 print:hidden">
        <h1 class="text-3xl font-bold text-gray-800">রুম বুকিং রিপোর্ট</h1>
        <p class="text-gray-600 mt-2">তারিখ: {{ date('d-m-Y') }}</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 print:hidden">
        <form method="GET" action="{{ route('admin.reports.room-bookings') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">শুরুর তারিখ</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">শেষ তারিখ</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">স্ট্যাটাস</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">সব</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>নিশ্চিত</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>পেন্ডিং</option>
                        <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>চেক-ইন</option>
                        <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>চেক-আউট</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">পেমেন্ট স্ট্যাটাস</label>
                    <select name="payment_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">সব</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>পেন্ডিং</option>
                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>আংশিক</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>পরিশোধিত</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">খুঁজুন</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="নাম / ফোন / রুম" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                        <i class="fas fa-filter mr-2"></i>ফিল্টার
                    </button>
                    <a href="{{ route('admin.reports.room-bookings') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Print Header - Invoice Style -->
    <div class="hidden print:block mb-6">
        <div class="text-center border-b-2 border-gray-700 pb-4 mb-4">
            @if($resortInfo && $resortInfo->header_logo)
                <img src="{{ asset('storage/' . $resortInfo->header_logo) }}" alt="{{ $resortInfo->resort_name ?? 'Resort' }}" class="h-16 mx-auto mb-2">
            @else
                <h1 class="text-2xl font-bold text-gray-800">{{ $resortInfo->resort_name ?? 'তুফান কনভেনশন রিসোর্ট' }}</h1>
            @endif
            @if($resortInfo && $resortInfo->address)
                <p class="text-gray-600 text-sm">{{ $resortInfo->address }}</p>
            @endif
            <p class="text-gray-500 text-xs mt-1">
                @if($resortInfo)
                    @if($resortInfo->phone)Phone: {{ $resortInfo->phone }}@endif
                    @if($resortInfo->phone && $resortInfo->email) | @endif
                    @if($resortInfo->email)Email: {{ $resortInfo->email }}@endif
                @endif
            </p>
        </div>
        
        <!-- Report Title -->
        <div class="text-center mb-4">
            <h2 class="text-xl font-bold text-gray-800 tracking-wider">রুম বুকিং রিপোর্ট</h2>
            <p class="text-sm text-gray-600 mt-1">
                @if(request('start_date') || request('end_date'))
                    তারিখ: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d-m-Y') : 'শুরু' }} 
                    থেকে {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d-m-Y') : 'শেষ' }}
                @else
                    তারিখ: {{ date('d-m-Y') }}
                @endif
                @if(request('status'))
                    | স্ট্যাটাস: {{ request('status') }}
                @endif
            </p>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6 print:grid-cols-5 print:gap-2 print:text-xs">
        <div class="bg-primary-50 rounded-lg p-4 text-center border border-primary-200 print:p-2">
            <p class="text-gray-600 text-xs">মোট বুকিং</p>
            <p class="text-xl font-bold text-primary-700 print:text-base">{{ $totalBookings }}</p>
        </div>
        <div class="bg-primary-50 rounded-lg p-4 text-center border border-primary-200 print:p-2">
            <p class="text-gray-600 text-xs">মোট বিল</p>
            <p class="text-xl font-bold text-primary-700 print:text-base">৳{{ number_format($totalRevenue, 0) }}</p>
        </div>
        <div class="bg-primary-50 rounded-lg p-4 text-center border border-primary-200 print:p-2">
            <p class="text-gray-600 text-xs">বিল জমা</p>
            <p class="text-xl font-bold text-primary-700 print:text-base">৳{{ number_format($totalAdvance, 0) }}</p>
        </div>
        <div class="bg-primary-50 rounded-lg p-4 text-center border border-primary-200 print:p-2">
            <p class="text-gray-600 text-xs">অতিরিক্ত চার্জ</p>
            <p class="text-xl font-bold text-primary-700 print:text-base">৳{{ number_format($bookings->sum('extra_charges'), 0) }}</p>
        </div>
        <div class="bg-red-50 rounded-lg p-4 text-center border border-red-200 print:p-2">
            <p class="text-gray-600 text-xs">বাকি</p>
            <p class="text-xl font-bold text-red-600 print:text-base">৳{{ number_format($totalRemaining, 0) }}</p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-2 mb-4 print:hidden">
        <button onclick="window.print()" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
            <i class="fas fa-print mr-2"></i>প্রিন্ট
        </button>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden print:shadow-none print:rounded-none">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border-collapse border border-gray-400">
                <thead>
                    <tr class="bg-gray-200 print:bg-gray-300">
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800">তারিখ</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800">মোবাইল নম্বর</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800">নাম</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800">পেশা/কোম্পানী</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800">রুম</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right">বিল</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right">বিল জমা</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right">অতিরিক্ত চার্জ</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right">বাকি</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800">চেক ইন</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800">চেক আউট</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-center">রাত্রি (দিন)</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800">মন্তব্য</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalNights = 0; @endphp
                    @forelse($bookings as $booking)
                    @php 
                        $nights = \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date));
                        $totalNights += $nights;
                        $calculatedTotal = $booking->getCalculatedTotal();
                        $calculatedRemaining = $booking->getCalculatedRemaining();
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-400 px-2 py-1">{{ \Carbon\Carbon::parse($booking->created_at)->format('d-m-Y') }}</td>
                        <td class="border border-gray-400 px-2 py-1">{{ $booking->customer_phone }}</td>
                        <td class="border border-gray-400 px-2 py-1 font-medium">{{ $booking->customer_name }}</td>
                        <td class="border border-gray-400 px-2 py-1">{{ $booking->company_name ?? '-' }}</td>
                        <td class="border border-gray-400 px-2 py-1 font-semibold text-primary-700">{{ $booking->bookingRooms->count() > 0 ? $booking->bookingRooms->map(fn($br) => $br->room->room_number)->join(', ') : ($booking->room ? $booking->room->room_number : 'N/A') }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-right font-semibold">{{ number_format($calculatedTotal, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-right text-primary-600">{{ number_format($booking->advance_payment, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-right">{{ number_format($booking->extra_charges ?? 0, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-right text-red-600 font-semibold">{{ number_format($calculatedRemaining, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-1">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d-m-Y') }}</td>
                        <td class="border border-gray-400 px-2 py-1">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d-m-Y') }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-center">{{ $nights }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-xs">{{ $booking->notes ?? '' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="13" class="border border-gray-400 px-4 py-8 text-center text-gray-500">কোনো বুকিং পাওয়া যায়নি</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-200 font-bold">
                        <td colspan="5" class="border border-gray-400 px-2 py-2 text-right">মোট:</td>
                        <td class="border border-gray-400 px-2 py-2 text-right">{{ number_format($totalRevenue, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-2 text-right text-primary-700">{{ number_format($totalAdvance, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-2 text-right">{{ number_format($bookings->sum('extra_charges'), 0) }}</td>
                        <td class="border border-gray-400 px-2 py-2 text-right text-red-600">{{ number_format($totalRemaining, 0) }}</td>
                        <td colspan="2" class="border border-gray-400 px-2 py-2"></td>
                        <td class="border border-gray-400 px-2 py-2 text-center">{{ $totalNights }}</td>
                        <td class="border border-gray-400 px-2 py-2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="mt-6 print:hidden">{{ $bookings->links() }}</div>

    <!-- Print Footer -->
    <div class="hidden print:block mt-6 pt-3 border-t border-gray-400 text-xs text-gray-600">
        <div class="flex justify-between">
            <div>প্রিন্ট তারিখ: {{ now()->format('d-m-Y H:i') }}</div>
            <div>Developed by Mir Javed Jeetu | 01811480222</div>
        </div>
    </div>
</div>

<style>
@media print {
    @page {
        size: A4 landscape;
        margin: 8mm;
    }
    body {
        font-size: 9px !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .print\:hidden {
        display: none !important;
    }
    .print\:block {
        display: block !important;
    }
    nav, header, aside, footer, .lg\:ml-64 > header, .lg\:ml-64 > footer {
        display: none !important;
    }
    .p-6 {
        padding: 0 !important;
    }
    table {
        font-size: 8px !important;
    }
    th, td {
        padding: 2px 4px !important;
    }
}
</style>
@endsection
