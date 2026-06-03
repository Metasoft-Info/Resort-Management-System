@extends('layouts.admin')
@section('content')
<div class="p-6">
    @include('admin.reports.partials.shared-header', [
        'title' => 'কম্বাইন্ড রিপোর্ট',
        'subtitle' => 'রুম বুকিং, অগ্রিম বুকিং ও বকেয়া বুকিং একসাথে'
    ])
    @include('admin.reports.partials.shared-styles')

    @if(!request('print'))
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 print:hidden">
        <form method="GET" action="{{ route('admin.reports.combined') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">শুরুর তারিখ</label>
                <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">শেষ তারিখ</label>
                <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">রুমের ধরন</label>
                <select name="room_type_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">সব</option>
                    @foreach($roomTypes as $rt)
                        <option value="{{ $rt->id }}">{{ $rt->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
    @endif

    @if(!request('print'))
    <div class="flex gap-2 mb-6 print:hidden">
        <button onclick="window.print()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
            <i class="fas fa-print mr-2"></i>প্রিন্ট
        </button>
        <a href="{{ route('admin.reports.room-bookings', request()->query()) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Room Bookings Report
        </a>
        <a href="{{ route('admin.reports.advance-bookings', request()->query()) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            Advance Report
        </a>
    </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6 print:grid-cols-6 print:gap-2 print:text-xs">
        <div class="bg-blue-50 rounded-lg p-4 text-center border border-blue-200">
            <p class="text-gray-600 text-xs">সব Rooms</p>
            <p class="text-xl font-bold text-blue-700">{{ $roomBookingsCount }}</p>
        </div>
        <div class="bg-green-50 rounded-lg p-4 text-center border border-green-200">
            <p class="text-gray-600 text-xs">অগ্রিম</p>
            <p class="text-xl font-bold text-green-600">{{ $advanceCount }}</p>
        </div>
        <div class="bg-red-50 rounded-lg p-4 text-center border border-red-200">
            <p class="text-gray-600 text-xs">নগদ না করা চেক-ইন</p>
            <p class="text-xl font-bold text-red-600">{{ $unpaidCount }}</p>
        </div>
        <div class="bg-purple-50 rounded-lg p-4 text-center border border-purple-200">
            <p class="text-gray-600 text-xs">মোট বুকিং</p>
            <p class="text-xl font-bold text-purple-700">{{ $grandTotalBookings }}</p>
        </div>
        <div class="bg-primary-50 rounded-lg p-4 text-center border border-primary-200">
            <p class="text-gray-600 text-xs">আয়</p>
            <p class="text-xl font-bold text-primary-700">৳{{ number_format($grandTotalRevenue) }}</p>
        </div>
        <div class="bg-orange-50 rounded-lg p-4 text-center border border-orange-200">
            <p class="text-gray-600 text-xs">বকেয়া</p>
            <p class="text-xl font-bold text-orange-600">৳{{ number_format($grandTotalRemaining) }}</p>
        </div>
    </div>

    <!-- Section 1: সব Room Bookings -->
    <div class="bg-white rounded-lg shadow mb-6 print:shadow-none print:rounded-none">
        <div class="px-4 py-3 bg-primary-50 border-b border-primary-200 flex items-center justify-between">
            <h2 class="font-bold text-primary-800"><i class="fas fa-bed mr-2"></i>সব Room Bookings</h2>
            <span class="text-sm text-gray-600">{{ count($roomBookings) }} bookings</span>
        </div>
        <div class="report-table-container">
            <table class="report-table text-sm border border-gray-300">
                <thead><tr class="bg-gray-100">
                    <th class="border border-gray-300 px-2 py-2 text-xs">Date</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Name</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Phone</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Room</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Total</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Advance</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Remaining</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Payment</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Status</th>
                </tr></thead>
                <tbody>
                    @forelse($roomBookings as $b)
                    @php $remaining = $b->getCalculatedRemaining(); @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($b->check_in_date)->format('d-m') }}</td>
                        <td class="border border-gray-300 px-2 py-1">{{ $b->customer_name }}</td>
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ $b->customer_phone }}</td>
                        <td class="border border-gray-300 px-2 py-1">{{ $b->room ? $b->room->room_number : 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($b->getGrandTotal()) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($b->advance_payment) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right font-bold {{ $remaining > 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($remaining) }}</td>
                        <td class="border border-gray-300 px-2 py-1">
                            @if($b->payment_status == 'paid') <span class="bg-green-100 text-green-800 px-2 rounded text-xs">Paid</span>
                            @elseif($b->payment_status == 'partial') <span class="bg-yellow-100 text-yellow-800 px-2 rounded text-xs">Partial</span>
                            @else <span class="bg-red-100 text-red-800 px-2 rounded text-xs">Unpaid</span>
                            @endif
                        </td>
                        <td class="border border-gray-300 px-2 py-1">
                            @if($b->status == 'confirmed') <span class="bg-blue-100 text-blue-800 px-2 rounded text-xs">নিশ্চিত</span>
                            @elseif($b->status == 'checked_in') <span class="bg-green-100 text-green-800 px-2 rounded text-xs">চেক-ইন</span>
                            @else <span class="bg-gray-100 text-gray-800 px-2 rounded text-xs">{{ ucfirst($b->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="border border-gray-300 px-4 py-8 text-center text-gray-500">কোনো বুকিং পাওয়া যায়নি</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Advance Bookings -->
    @if($advanceBookings->count() > 0)
    <div class="bg-white rounded-lg shadow mb-6 print:shadow-none print:rounded-none">
        <div class="px-4 py-3 bg-green-50 border-b border-green-200 flex items-center justify-between">
            <h2 class="font-bold text-green-800"><i class="fas fa-calendar-check mr-2"></i>অগ্রিম Bookings (Future)</h2>
            <span class="text-sm text-green-600">{{ count($advanceBookings) }} bookings</span>
        </div>
        <div class="report-table-container">
            <table class="report-table text-sm border border-gray-300">
                <thead><tr class="bg-green-50">
                    <th class="border border-gray-300 px-2 py-2 text-xs">Date</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Name</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Room</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Room Type</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs whitespace-nowrap">Check-In</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs whitespace-nowrap">Check-Out</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Total</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Advance</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Payment</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Status</th>
                </tr></thead>
                <tbody>
                    @forelse($advanceBookings as $b)
                    <tr class="hover:bg-green-50">
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($b->check_in_date)->format('d-m-Y') }}</td>
                        <td class="border border-gray-300 px-2 py-1">{{ $b->customer_name }}</td>
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ $b->room ? $b->room->room_number : 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1">{{ $b->room?->roomType?->name ?? 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($b->check_in_date)->format('d-m') }}</td>
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($b->check_out_date)->format('d-m') }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($b->getGrandTotal()) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right text-green-600">{{ number_format($b->advance_payment) }}</td>
                        <td class="border border-gray-300 px-2 py-1">
                            <span class="px-2 py-1 rounded text-xs {{ $b->payment_status == 'paid' ? 'bg-green-100 text-green-700' : ($b->payment_status == 'partial' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($b->payment_status) }}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-2 py-1">
                            <span class="px-2 py-1 rounded text-xs {{ $b->status == 'confirmed' ? 'bg-blue-100 text-blue-700' : ($b->status == 'checked_in' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ ucfirst($b->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="border border-gray-300 px-4 py-8 text-center text-gray-500">কোনো অগ্রিম বুকিং পাওয়া যায়নি</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Section 3: Unpaid Checked-in -->
    @if($unpaidBookings->count() > 0)
    <div class="bg-white rounded-lg shadow mb-6 print:shadow-none print:rounded-none">
        <div class="px-4 py-3 bg-red-50 border-b border-red-200 flex items-center justify-between">
            <h2 class="font-bold text-red-800"><i class="fas fa-exclamation-triangle mr-2"></i>চেক-ইন কিন্তু পুরোপুরি টাকা নাও দিয়েছেন</h2>
            <span class="text-sm text-red-600">{{ count($unpaidBookings) }} bookings</span>
        </div>
        <div class="report-table-container">
            <table class="report-table text-sm border border-gray-300">
                <thead><tr class="bg-red-50">
                    <th class="border border-gray-300 px-2 py-2 text-xs">#ID</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Name</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Phone</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Room</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs whitespace-nowrap">Check-In</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Total Bill</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Advance</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Due</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Payment</th>
                </tr></thead>
                <tbody>
                    @forelse($unpaidBookings as $b)
                    @php $due = $b->getGrandTotal() - $b->advance_payment; @endphp
                    <tr class="hover:bg-red-50">
                        <td class="border border-gray-300 px-2 py-1 font-bold">#{{ $b->id }}</td>
                        <td class="border border-gray-300 px-2 py-1">{{ $b->customer_name }}</td>
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ $b->customer_phone }}</td>
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ $b->room ? $b->room->room_number : 'N/A' }}</td>
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($b->check_in_date)->format('d-m-Y') }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($b->getGrandTotal()) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($b->advance_payment) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right font-bold text-red-600">{{ number_format($due) }}</td>
                        <td class="border border-gray-300 px-2 py-1">
                            <span class="px-2 py-1 rounded text-xs {{ $b->payment_status == 'paid' ? 'bg-green-100 text-green-700' : ($b->payment_status == 'partial' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($b->payment_status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="border border-gray-300 px-4 py-8 text-center text-gray-500">কোনো বুকিং পাওয়া যায়নি</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if(!request('print'))
    <div class="mt-6 print:hidden">
        <a href="{{ route('admin.reports.room-bookings') }}" class="text-primary-600 hover:text-primary-800">
            <i class="fas fa-arrow-left mr-2"></i>Room Bookings Report
        </a>
    </div>
    @endif

    @include('admin.reports.partials.shared-footer')
</div>

<style>
@media print {
    @page { size: A4 portrait; margin: 10mm; }
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    body { font-size: 9px !important; }
    .print\:hidden { display: none !important; }
    .print\:block { display: block !important; }
    nav, header, aside, footer { display: none !important; }
    .lg\:ml-64 { margin-left: 0 !important; }
    table { width: 100% !important; border-collapse: collapse !important; }
    th, td { padding: 2px 4px !important; border: 1px solid #666 !important; font-size: 8px !important; }
    tr { page-break-inside: avoid !important; }
}
</style>
@endsection
