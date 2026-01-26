@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">অ্যাডঅন সার্ভিস</h1>
            <p class="text-gray-600 mt-2">রুম ও কনভেনশন বুকিং এর অতিরিক্ত সেবা পরিচালনা করুন</p>
        </div>
        <a href="{{ route('admin.addon-services.create') }}" class="bg-gradient-to-r from-purple-600 to-purple-700 text-white px-6 py-3 rounded-lg hover:from-purple-700 hover:to-purple-800 transition shadow-lg">
            <i class="fas fa-plus mr-2"></i>নতুন সার্ভিস যোগ করুন
        </a>
    </div>

    <!-- Filter Tabs -->
    <div class="flex gap-2 mb-6">
        <a href="{{ route('admin.addon-services.index') }}" class="px-4 py-2 rounded-lg font-semibold transition {{ !request('type') ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            <i class="fas fa-list mr-2"></i>সব ({{ \App\Models\AddonService::count() }})
        </a>
        <a href="{{ route('admin.addon-services.index', ['type' => 'room']) }}" class="px-4 py-2 rounded-lg font-semibold transition {{ request('type') == 'room' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            <i class="fas fa-bed mr-2"></i>রুম সার্ভিস ({{ \App\Models\AddonService::where('service_type', 'room')->count() }})
        </a>
        <a href="{{ route('admin.addon-services.index', ['type' => 'convention']) }}" class="px-4 py-2 rounded-lg font-semibold transition {{ request('type') == 'convention' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            <i class="fas fa-building mr-2"></i>কনভেনশন সার্ভিস ({{ \App\Models\AddonService::where('service_type', 'convention')->count() }})
        </a>
        <a href="{{ route('admin.addon-services.index', ['type' => 'both']) }}" class="px-4 py-2 rounded-lg font-semibold transition {{ request('type') == 'both' ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            <i class="fas fa-sync mr-2"></i>উভয় ({{ \App\Models\AddonService::where('service_type', 'both')->count() }})
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">নাম</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">ধরন</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">ক্যাটাগরি</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">মূল্য</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">ইউনিট</th>
                    <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">স্ট্যাটাস</th>
                    <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($addonServices as $service)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-800">{{ $service->name }}</div>
                        @if($service->description)
                        <div class="text-xs text-gray-500">{{ Str::limit($service->description, 40) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($service->service_type == 'room')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                <i class="fas fa-bed mr-1"></i>রুম
                            </span>
                        @elseif($service->service_type == 'convention')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <i class="fas fa-building mr-1"></i>কনভেনশন
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                <i class="fas fa-sync mr-1"></i>উভয়
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        @php
                            $categories = [
                                'decoration' => 'সাজসজ্জা',
                                'sound_system' => 'সাউন্ড সিস্টেম',
                                'photography' => 'ফটোগ্রাফি',
                                'catering' => 'ক্যাটারিং',
                                'transport' => 'পরিবহন',
                                'room_service' => 'রুম সার্ভিস',
                                'laundry' => 'লন্ড্রি',
                                'parking' => 'পার্কিং',
                                'other' => 'অন্যান্য',
                            ];
                        @endphp
                        {{ $categories[$service->category] ?? $service->category }}
                    </td>
                    <td class="px-6 py-4 text-gray-800 font-semibold">৳{{ number_format($service->price, 0) }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $service->unit ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($service->is_active)
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">সক্রিয়</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">নিষ্ক্রিয়</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.addon-services.edit', $service) }}" class="text-blue-600 hover:text-blue-800 mr-3"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.addon-services.destroy', $service) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="event.preventDefault(); confirmDelete(this.form, 'আপনি কি নিশ্চিত?')" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">কোনো সার্ভিস পাওয়া যায়নি</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $addonServices->links() }}</div>
</div>
@endsection
