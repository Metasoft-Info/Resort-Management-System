@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">আজকের সারাংশ</h1>
        <p class="text-gray-600 mt-2">{{ date('d F Y') }} - দৈনিক কার্যক্রম</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div><p class="text-green-100 text-sm">আজকের চেক-ইন</p><p class="text-3xl font-bold mt-2">{{ $stats['checkins_count'] }}</p></div>
                <div class="bg-white bg-opacity-20 rounded-full p-3"><i class="fas fa-sign-in-alt text-2xl"></i></div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div><p class="text-orange-100 text-sm">আজকের চেক-আউট</p><p class="text-3xl font-bold mt-2">{{ $stats['checkouts_count'] }}</p></div>
                <div class="bg-white bg-opacity-20 rounded-full p-3"><i class="fas fa-sign-out-alt text-2xl"></i></div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div><p class="text-purple-100 text-sm">বর্তমানে অবস্থানরত</p><p class="text-3xl font-bold mt-2">{{ $stats['staying_count'] }}</p></div>
                <div class="bg-white bg-opacity-20 rounded-full p-3"><i class="fas fa-bed text-2xl"></i></div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div><p class="text-blue-100 text-sm">আজকের কনভেনশন</p><p class="text-3xl font-bold mt-2">{{ $stats['conventions_count'] }}</p></div>
                <div class="bg-white bg-opacity-20 rounded-full p-3"><i class="fas fa-building text-2xl"></i></div>
            </div>
        </div>
    </div>

    <!-- Today's Checkins -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4"><i class="fas fa-arrow-right mr-2 text-green-600"></i>আজকের চেক-ইন</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-bold text-gray-600">গ্রাহক</th><th class="px-6 py-3 text-left text-xs font-bold text-gray-600">রুম</th><th class="px-6 py-3 text-left text-xs font-bold text-gray-600">চেক-আউট</th><th class="px-6 py-3 text-left text-xs font-bold text-gray-600">স্ট্যাটাস</th></tr></thead>
                <tbody>
                    @forelse($todayCheckins as $booking)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $booking->guest_name }}</td>
                        <td class="px-6 py-4">{{ $booking->room->room_number ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $booking->check_out_date }}</td>
                        <td class="px-6 py-4"><span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-800">{{ $booking->status }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">কোনো চেক-ইন নেই</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Today's Checkouts -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4"><i class="fas fa-arrow-left mr-2 text-orange-600"></i>আজকের চেক-আউট</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-bold text-gray-600">গ্রাহক</th><th class="px-6 py-3 text-left text-xs font-bold text-gray-600">রুম</th><th class="px-6 py-3 text-left text-xs font-bold text-gray-600">চেক-ইন</th></tr></thead>
                <tbody>
                    @forelse($todayCheckouts as $booking)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $booking->guest_name }}</td>
                        <td class="px-6 py-4">{{ $booking->room->room_number ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $booking->check_in_date }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-6 py-8 text-center text-gray-500">কোনো চেক-আউট নেই</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Today's Conventions -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4"><i class="fas fa-calendar-day mr-2 text-blue-600"></i>আজকের কনভেনশন</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-bold text-gray-600">গ্রাহক</th><th class="px-6 py-3 text-left text-xs font-bold text-gray-600">হল</th><th class="px-6 py-3 text-left text-xs font-bold text-gray-600">সময়</th><th class="px-6 py-3 text-left text-xs font-bold text-gray-600">স্ট্যাটাস</th></tr></thead>
                <tbody>
                    @forelse($todayConventions as $booking)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $booking->customer_name }}</td>
                        <td class="px-6 py-4">{{ $booking->conventionHall->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $booking->time_slot }}</td>
                        <td class="px-6 py-4"><span class="px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-800">{{ $booking->status }}</span></td>
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
