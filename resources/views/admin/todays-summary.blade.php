@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">আজকের সারাংশ</h1>
        <p class="text-gray-600 mt-2">{{ date('d F Y') }} - দৈনিক কার্যক্রম</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div><p class="text-green-100 text-sm">আজকের চেক-ইন</p><p class="text-3xl font-bold mt-2">{{ $stats['checkins_count'] }}</p></div>
                <div class="bg-white bg-opacity-20 rounded-full p-3"><i class="fas fa-sign-in-alt text-2xl"></i></div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div><p class="text-primary-100 text-sm">আজকের চেক-আউট</p><p class="text-3xl font-bold mt-2">{{ $stats['checkouts_count'] }}</p></div>
                <div class="bg-white bg-opacity-20 rounded-full p-3"><i class="fas fa-sign-out-alt text-2xl"></i></div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div><p class="text-primary-100 text-sm">বর্তমানে অবস্থানরত</p><p class="text-3xl font-bold mt-2">{{ $stats['staying_count'] }}</p></div>
                <div class="bg-white bg-opacity-20 rounded-full p-3"><i class="fas fa-bed text-2xl"></i></div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div><p class="text-primary-100 text-sm">আজকের কনভেনশন</p><p class="text-3xl font-bold mt-2">{{ $stats['conventions_count'] }}</p></div>
                <div class="bg-white bg-opacity-20 rounded-full p-3"><i class="fas fa-building text-2xl"></i></div>
            </div>
        </div>
    </div>

    <!-- Today's Checkins -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4"><i class="fas fa-arrow-right mr-2 text-primary-600"></i>আজকের চেক-ইন</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-primary-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">ক্রম</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">গ্রাহক</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">মোবাইল</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">রুম</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">চেক-ইন</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">চেক-আউট</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">মোট বিল</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">জমা</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">বাকি</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">স্ট্যাটাস</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($todayCheckins as $index => $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $booking->customer_name }}</td>
                        <td class="px-4 py-3">{{ $booking->customer_phone }}</td>
                        <td class="px-4 py-3 font-semibold text-primary-700">{{ $booking->room->room_number ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-right font-semibold">৳{{ number_format($booking->total_amount, 0) }}</td>
                        <td class="px-4 py-3 text-right text-primary-600">৳{{ number_format($booking->advance_payment, 0) }}</td>
                        <td class="px-4 py-3 text-right text-red-600">৳{{ number_format($booking->remaining_payment, 0) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($booking->status == 'checked_in') bg-primary-100 text-primary-800
                                @elseif($booking->status == 'confirmed') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($booking->status == 'checked_in') চেক-ইন @elseif($booking->status == 'confirmed') নিশ্চিত @else {{ $booking->status }} @endif
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="px-6 py-8 text-center text-gray-500">কোনো চেক-ইন নেই</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Today's Checkouts -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4"><i class="fas fa-arrow-left mr-2 text-primary-600"></i>আজকের চেক-আউট</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-primary-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">ক্রম</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">গ্রাহক</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">মোবাইল</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">রুম</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">চেক-ইন</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">চেক-আউট</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">মোট বিল</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">জমা</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">বাকি</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($todayCheckouts as $index => $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $booking->customer_name }}</td>
                        <td class="px-4 py-3">{{ $booking->customer_phone }}</td>
                        <td class="px-4 py-3 font-semibold text-primary-700">{{ $booking->room->room_number ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-right font-semibold">৳{{ number_format($booking->total_amount, 0) }}</td>
                        <td class="px-4 py-3 text-right text-primary-600">৳{{ number_format($booking->advance_payment, 0) }}</td>
                        <td class="px-4 py-3 text-right text-red-600">৳{{ number_format($booking->remaining_payment, 0) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-6 py-8 text-center text-gray-500">কোনো চেক-আউট নেই</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Currently Staying -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4"><i class="fas fa-bed mr-2 text-primary-600"></i>বর্তমানে অবস্থানরত</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-primary-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">ক্রম</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">গ্রাহক</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">মোবাইল</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">রুম</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">চেক-ইন</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">চেক-আউট</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">মোট বিল</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">জমা</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">বাকি</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">স্ট্যাটাস</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-700">একশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($currentlyStaying as $index => $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $booking->customer_name }}</td>
                        <td class="px-4 py-3">{{ $booking->customer_phone }}</td>
                        <td class="px-4 py-3 font-semibold text-primary-700">{{ $booking->room->room_number ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-right font-semibold">৳{{ number_format($booking->total_amount, 0) }}</td>
                        <td class="px-4 py-3 text-right text-primary-600">৳{{ number_format($booking->advance_payment, 0) }}</td>
                        <td class="px-4 py-3 text-right text-red-600">৳{{ number_format($booking->remaining_payment, 0) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($booking->status == 'checked_in') bg-green-100 text-green-800
                                @elseif($booking->status == 'confirmed') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($booking->status == 'checked_in') চেক-ইন @elseif($booking->status == 'confirmed') নিশ্চিত @else {{ $booking->status }} @endif
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <form action="{{ route('admin.bookings.update-status', $booking) }}" method="POST" class="inline-flex items-center gap-2">
                                @csrf
                                <select name="status" class="text-xs border border-gray-300 rounded px-2 py-1 focus:ring-1 focus:ring-primary-500">
                                    <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>নিশ্চিত</option>
                                    <option value="checked_in" {{ $booking->status == 'checked_in' ? 'selected' : '' }}>চেক-ইন</option>
                                    <option value="checked_out" {{ $booking->status == 'checked_out' ? 'selected' : '' }}>চেক-আউট</option>
                                </select>
                                <button type="submit" class="bg-primary-600 text-white px-2 py-1 rounded text-xs hover:bg-primary-700"><i class="fas fa-save"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="11" class="px-6 py-8 text-center text-gray-500">কেউ বর্তমানে অবস্থানরত নেই</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Today's Conventions -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4"><i class="fas fa-calendar-day mr-2 text-primary-600"></i>আজকের কনভেনশন</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-bold text-gray-600">গ্রাহক</th><th class="px-6 py-3 text-left text-xs font-bold text-gray-600">হল</th><th class="px-6 py-3 text-left text-xs font-bold text-gray-600">সময়</th><th class="px-6 py-3 text-left text-xs font-bold text-gray-600">স্ট্যাটাস</th></tr></thead>
                <tbody>
                    @forelse($todayConventions as $booking)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $booking->customer_name }}</td>
                        <td class="px-6 py-4">{{ $booking->conventionHall->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $booking->time_slot }}</td>
                        <td class="px-6 py-4"><span class="px-3 py-1 rounded-full text-xs bg-primary-100 text-primary-800">{{ $booking->status }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">কোনো কনভেনশন নেই</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
