@extends('layouts.admin')

@section('title', 'Rooms')
@section('header', 'Rooms Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-lg font-semibold text-gray-700">Total Rooms: <span class="text-primary-600">{{ $rooms->total() }}</span></h3>
    </div>
    <a href="{{ route('admin.rooms.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-primary-600 to-accent-600 text-white rounded-xl hover:from-primary-700 hover:to-accent-700 transition font-semibold shadow-lg hover:shadow-xl">
        <i class="fas fa-plus mr-2"></i>Add New Room
    </a>
</div>

<div class="bg-white rounded-2xl shadow-xl overflow-hidden">
    <table class="w-full">
        <thead class="bg-gradient-to-r from-primary-50 to-accent-50">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-image mr-2 text-primary-600"></i>Image
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-hashtag mr-2 text-primary-600"></i>Room #
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-door-open mr-2 text-primary-600"></i>Name
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-tag mr-2 text-primary-600"></i>Type
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-bangladeshi-taka-sign mr-2 text-primary-600"></i>Price/Night
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-users mr-2 text-primary-600"></i>Capacity
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-info-circle mr-2 text-primary-600"></i>Status
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-cog mr-2 text-primary-600"></i>Actions
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($rooms as $room)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        @php
                            $images = $room->images;
                            $firstImage = is_array($images) && count($images) > 0 ? $images[0] : null;
                        @endphp
                        @if($firstImage)
                            <div class="relative">
                                <img src="{{ asset('storage/' . $firstImage) }}" alt="{{ $room->name }}" class="w-16 h-16 object-cover rounded-lg shadow">
                                @if(count($images) > 1)
                                    <span class="absolute -top-1 -right-1 bg-primary-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">+{{ count($images) - 1 }}</span>
                                @endif
                            </div>
                        @else
                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-bold text-primary-600">{{ $room->room_number }}</td>
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $room->name }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 bg-primary-100 text-primary-700 rounded-lg text-xs font-bold">
                            {{ $room->roomType->name ?? ucfirst($room->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-700">৳{{ number_format($room->price_per_night, 0) }}</td>
                    <td class="px-6 py-4 text-gray-600">
                        <i class="fas fa-user mr-1"></i>{{ $room->max_guests }} | 
                        <i class="fas fa-bed ml-2 mr-1"></i>{{ $room->number_of_beds }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-xs font-bold rounded-full inline-flex items-center
                            @if($room->status == 'available') bg-green-100 text-green-700
                            @elseif($room->status == 'booked') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700
                            @endif">
                            @if($room->status == 'available')
                                <i class="fas fa-check-circle mr-1"></i>
                            @elseif($room->status == 'booked')
                                <i class="fas fa-clock mr-1"></i>
                            @else
                                <i class="fas fa-tools mr-1"></i>
                            @endif
                            {{ ucfirst($room->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.rooms.edit', $room) }}" class="px-3 py-1.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-xs font-semibold inline-flex items-center">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" class="inline" onsubmit="return confirmDelete(this, 'রুম {{ $room->name }} মুছে ফেলতে চান?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-xs font-semibold inline-flex items-center">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">No rooms found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $rooms->links() }}
</div>
@endsection
