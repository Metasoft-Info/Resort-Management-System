@extends('layouts.admin')
@section('content')
<div class="p-6">
    @include('admin.reports.partials.shared-header', [
        'title' => 'পুলিশ স্টেশনের জন্য গেস্ট রিপোর্ট'
    ])
    @include('admin.reports.partials.shared-styles')

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 print:hidden">
        <form method="GET" action="{{ route('admin.reports.police-station') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
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
                    <label class="block text-sm font-semibold text-gray-700 mb-2">খুঁজুন</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="নাম / ফোন / রুম / এনআইডি" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                        <i class="fas fa-filter mr-2"></i>ফিল্টার
                    </button>
                    <a href="{{ route('admin.reports.police-station') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
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
                <strong>মোট বুকিং:</strong> {{ $bookings->total() }}
                @if(request('start_date') || request('end_date'))
                    | <strong>তারিখ:</strong> {{ request('start_date') ?: 'শুরু' }} থেকে {{ request('end_date') ?: 'শেষ' }}
                @endif
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-400 border-collapse">
                <thead>
                    <tr class="bg-gray-200 print:bg-gray-100">
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-center">#</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">গেস্টের নাম</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">ফোন</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">এনআইডি/পাসপোর্ট</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">কোম্পানী</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">ঠিকানা</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">ভ্রমণের উদ্দেশ্য</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">রেফারেন্স</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">রুম</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">অতিথি সংখ্যা</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">ইন</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">আউট</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">স্ট্যাটাস</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">বুক করেছে</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $index => $booking)
                    @php
                        $rooms = $booking->getAllRooms();
                        $roomNumbers = $rooms->pluck('room_number')->implode(', ');
                        $additionalGuestsList = $booking->additionalGuests ?? collect([]);
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-400 px-2 py-1 text-center">{{ $bookings->firstItem() + $index }}</td>
                        <td class="border border-gray-400 px-2 py-1 font-semibold whitespace-nowrap">{{ $booking->customer_name }}</td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap">{{ $booking->customer_phone }}</td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap">
                            {{ $booking->customer_nid ?: ($booking->passport_number ?: '-') }}
                        </td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap">{{ $booking->company_name ?: '-' }}</td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap max-w-[150px] truncate">{{ $booking->customer_address ?: '-' }}</td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap">{{ $booking->booking_purpose ?: '-' }}</td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap">
                            @if($booking->reference_name || $booking->reference_phone)
                                {{ $booking->reference_name }} {{ $booking->reference_phone ? '(' . $booking->reference_phone . ')' : '' }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap font-semibold">{{ $roomNumbers ?: 'N/A' }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-center">{{ $booking->number_of_guests }}</td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d-m-Y') }}</td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d-m-Y') }}</td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap">
                            @if($booking->status == 'checked_in')
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-semibold">চেক-ইন</span>
                            @elseif($booking->status == 'checked_out')
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs font-semibold">চেক-আউট</span>
                            @elseif($booking->status == 'confirmed')
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-semibold">নিশ্চিত</span>
                            @elseif($booking->status == 'pending')
                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs font-semibold">পেন্ডিং</span>
                            @else
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs">{{ $booking->status }}</span>
                            @endif
                        </td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap text-xs">{{ $booking->createdBy->name ?? '-' }}</td>
                    </tr>
                    @if($additionalGuestsList->count() > 0)
                    <tr class="bg-amber-50/50">
                        <td class="border border-gray-400 px-2 py-1 text-center text-xs text-gray-500" colspan="14">
                            <strong>অতিরিক্ত অতিথি:</strong>
                            @foreach($additionalGuestsList as $ag)
                                {{ $ag->name }} {{ $ag->phone ? '(' . $ag->phone . ')' : '' }}{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="14" class="border border-gray-400 px-6 py-10 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                            কোনো বুকিং পাওয়া যায়নি
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 print:hidden">
            {{ $bookings->links() }}
        </div>

        <div class="p-4 text-center text-xs text-gray-500 border-t print:block">
            <p>প্রিন্ট/রিপোর্ট সময়: {{ now()->format('d-m-Y h:i A') }}</p>
            @if($resortInfo)
                <p>{{ $resortInfo->name }} | {{ $resortInfo->phone }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
