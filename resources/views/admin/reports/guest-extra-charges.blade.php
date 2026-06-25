@extends('layouts.admin')
@section('content')
<div class="p-6">
    @include('admin.reports.partials.shared-header', [
        'title' => 'Guest Extra Charge Report'
    ])
    @include('admin.reports.partials.shared-styles')

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 print:hidden">
        <form method="GET" action="{{ route('admin.reports.guest-extra-charges') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">All</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Check-In</option>
                        <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Check-Out</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name / Phone / Room" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.reports.guest-extra-charges') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                        <i class="fas fa-times"></i>
                    </a>
                    <button type="button" onclick="window.print()" class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Report Content -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden print:shadow-none">
        <div class="p-4 bg-gray-50 border-b print:bg-white">
            <p class="text-sm text-gray-600">
                <strong>Total Bookings:</strong> {{ $bookings->total() }}
                @if(request('start_date') || request('end_date'))
                    | <strong>Date:</strong> {{ request('start_date') ?: 'Start' }} to {{ request('end_date') ?: 'End' }}
                @endif
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-400 border-collapse">
                <thead>
                    <tr class="bg-gray-200 print:bg-gray-100">
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-center">#</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">Date</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">Guest Name</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">Phone</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">Room</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">In</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">Out</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">Status</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right">Charge Description</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right">Total Extra</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sumExtraTotal = 0;
                    @endphp
                    @forelse($bookings as $index => $booking)
                    @php
                        $rooms = $booking->getAllRooms();
                        $roomNumbers = $rooms->pluck('room_number')->implode(', ');
                        $extraData = $booking->extra_charges_data ?? [];
                        $extraTotal = $booking->extra_charges ?? 0;
                        $sumExtraTotal += $extraTotal;
                        $chargeLines = [];
                        if (!empty($extraData) && is_array($extraData)) {
                            foreach ($extraData as $item) {
                                $name = $item['name'] ?? 'Unknown';
                                $qty = $item['quantity'] ?? 1;
                                $price = $item['price'] ?? 0;
                                $amount = $item['amount'] ?? 0;
                                $chargeLines[] = ($qty > 1 ? $name . ' × ' . $qty . ' @ ' . number_format($price) : $name) . ' = ' . number_format($amount);
                            }
                        } elseif ($booking->extra_charges_description) {
                            $chargeLines[] = $booking->extra_charges_description;
                        }
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-400 px-2 py-1 text-center">{{ $bookings->firstItem() + $index }}</td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d-m-Y') }}</td>
                        <td class="border border-gray-400 px-2 py-1 font-semibold whitespace-nowrap">{{ $booking->customer_name }}</td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap">{{ $booking->customer_phone }}</td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap font-semibold">{{ $roomNumbers ?: 'N/A' }}</td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d-m-Y') }}</td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d-m-Y') }}</td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap">
                            @if($booking->status == 'checked_in')
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-semibold">Check-In</span>
                            @elseif($booking->status == 'checked_out')
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs font-semibold">Check-Out</span>
                            @elseif($booking->status == 'confirmed')
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-semibold">Confirmed</span>
                            @elseif($booking->status == 'pending')
                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs font-semibold">Pending</span>
                            @else
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs">{{ $booking->status }}</span>
                            @endif
                        </td>
                        <td class="border border-gray-400 px-2 py-1 text-right text-sm">
                            @if(count($chargeLines) > 0)
                                @foreach($chargeLines as $line)
                                    <div class="text-gray-700">{{ $line }}</div>
                                @endforeach
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="border border-gray-400 px-2 py-1 text-right font-bold whitespace-nowrap">{{ number_format($extraTotal) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="border border-gray-400 px-6 py-10 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                            No extra charges found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 font-bold">
                        <td colspan="9" class="border border-gray-400 px-2 py-2 text-right">Total Extra Charges:</td>
                        <td class="border border-gray-400 px-2 py-2 text-right text-emerald-700">{{ number_format($sumExtraTotal) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="p-4 print:hidden">
            {{ $bookings->links() }}
        </div>

        @include('admin.reports.partials.signature-section')

        <div class="p-3 text-center text-[10px] text-gray-500 border-t print:block">
            <p>Print/Report Time: {{ now()->format('d-m-Y h:i A') }} | Developed by Mir Javed Jeetu | 01811480222</p>
            @if($resortInfo)
                <p class="text-[10px]">{{ $resortInfo->name }} | {{ $resortInfo->phone }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
