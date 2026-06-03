@extends('layouts.admin')
@section('content')
<div class="p-6">
    @include('admin.reports.partials.shared-header', [
        'title' => 'চেক-ইন হয়েছে, পূর্ণ পরিশোধ হয়নি',
        'subtitle' => 'চেক-ইন করা কিন্তু বকেয়া থাকা বুকিংসমূহ'
    ])
    @include('admin.reports.partials.shared-styles')

    @if(!request('print'))
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 print:hidden">
        <form method="GET" action="{{ route('admin.reports.unpaid-checked-in') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">শুরুর তারিখ</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">শেষ তারিখ</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">রুম টাইপ</label>
                <select name="room_type_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">সব</option>
                    @foreach($roomTypes as $rt)
                        <option value="{{ $rt->id }}">{{ $rt->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">নাম / ফোন</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="খুঁজুন" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
        </form>
    </div>
    @endif

    @if(!request('print'))
    <div class="flex gap-2 mb-6 print:hidden">
        <button onclick="window.print()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
            <i class="fas fa-print mr-2"></i>প্রিন্ট
        </button>
        <a href="{{ route('admin.reports.combined') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
            কম্বাইন্ড রিপোর্ট দেখুন
        </a>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden print:shadow-none print:rounded-none">
        <div class="report-table-container">
            <table class="report-table text-sm border border-gray-300">
                <thead><tr class="bg-red-50">
                    <th class="border border-gray-300 px-3 py-2 text-xs font-bold text-red-700">#ID</th>
                    <th class="border border-gray-300 px-3 py-2 text-xs font-bold text-red-700">তারিখ</th>
                    <th class="border border-gray-300 px-3 py-2 text-xs font-bold text-red-700">নাম</th>
                    <th class="border border-gray-300 px-3 py-2 text-xs font-bold text-red-700">ফোন</th>
                    <th class="border border-gray-300 px-3 py-2 text-xs font-bold text-red-700">রুম</th>
                    <th class="border border-gray-300 px-3 py-2 text-xs font-bold text-red-700 whitespace-nowrap">চেক-ইন</th>
                    <th class="border border-gray-300 px-3 py-2 text-xs font-bold text-red-700 text-right">মোট বিল</th>
                    <th class="border border-gray-300 px-3 py-2 text-xs font-bold text-red-700">পেমেন্ট</th>
                    <th class="border border-gray-300 px-3 py-2 text-xs font-bold text-red-700">স্ট্যাটাস</th>
                </tr></thead>
                <tbody>
                    @forelse($bookings as $booking)
                    @php $due = $booking->getGrandTotal() - $booking->advance_payment; @endphp
                    <tr class="hover:bg-red-50">
                        <td class="border border-gray-300 px-3 py-2 font-bold">#{{ $booking->id }}</td>
                        <td class="border border-gray-300 px-3 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d-m-Y') }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $booking->customer_name }}</td>
                        <td class="border border-gray-300 px-3 py-2 whitespace-nowrap">{{ $booking->customer_phone }}</td>
                        <td class="border border-gray-300 px-3 py-2 whitespace-nowrap">{{ $booking->room ? $booking->room->room_number : ($booking->bookingRooms->first()?->room?->room_number ?? 'N/A') }}</td>
                        <td class="border border-gray-300 px-3 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d-m-Y') }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right font-bold">৳{{ number_format($booking->getGrandTotal()) }}</td>
                        <td class="border border-gray-300 px-3 py-2">
                            <span class="px-2 py-1 rounded text-xs {{ $booking->payment_status == 'paid' ? 'bg-green-100 text-green-700' : ($booking->payment_status == 'partial' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-3 py-2">
                            <span class="px-2 py-1 rounded text-xs {{ $booking->status == 'checked_in' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($booking->status) }}
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

    @if(!request('print'))
    <div class="mt-6 print:hidden">{{ $bookings->links() }}</div>
    @endif

    @include('admin.reports.partials.shared-footer')
</div>

<style>
@media print {
    @page { size: A4 portrait; margin: 10mm; }
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    body { font-size: 10px !important; }
    .print\:hidden { display: none !important; }
    nav, header, aside, footer { display: none !important; }
    .lg\:ml-64 { margin-left: 0 !important; }
    table { width: 100% !important; border-collapse: collapse !important; }
    th, td { padding: 3px 5px !important; border: 1px solid #666 !important; }
    tr { page-break-inside: avoid !important; }
}
</style>
@endsection
