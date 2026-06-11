@extends('layouts.admin')

@section('title', 'Rooms')
@section('header', 'Rooms Management')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl p-6 shadow-xl text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-hotel text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Rooms Management</h1>
                    <p class="text-emerald-100 text-sm">Total {{ $rooms->total() }} rooms in the system</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.rooms.print') }}" target="_blank" class="inline-flex items-center px-5 py-2.5 bg-white/20 text-white rounded-xl hover:bg-white/30 transition font-semibold shadow-lg border border-white/30">
                    <i class="fas fa-print mr-2"></i>Print Rooms
                </a>
                <a href="{{ route('admin.rooms.create') }}" class="inline-flex items-center px-5 py-2.5 bg-white text-emerald-700 rounded-xl hover:bg-emerald-50 transition font-semibold shadow-lg">
                    <i class="fas fa-plus mr-2"></i>Add New Room
                </a>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Image</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Room #</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Price/Night</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Capacity</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rooms as $room)
                    <tr class="hover:bg-emerald-50/20 transition group">
                        <td class="px-6 py-4">
                            @php
                            $images = $room->images;
                            $firstImage = is_array($images) && count($images) > 0 ? $images[0] : null;
                            @endphp
                            @if($firstImage)
                            <div class="relative">
                                <img src="{{ asset('storage/' . $firstImage) }}" alt="{{ $room->name }}" class="w-14 h-14 object-cover rounded-lg shadow-sm">
                                @if(count($images) > 1)
                                <span class="absolute -top-1 -right-1 bg-emerald-500 text-white text-[10px] rounded-full w-5 h-5 flex items-center justify-center font-bold">+{{ count($images) - 1 }}</span>
                                @endif
                            </div>
                            @else
                            <div class="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-gray-300"></i>
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-emerald-600 text-sm">{{ $room->room_number }}</span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $room->name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold border border-emerald-100">
                                {{ $room->roomType->name ?? ucfirst($room->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-700">{{ number_format($room->price_per_night, 0) }}</td>
                        <td class="px-6 py-4 text-gray-500 text-sm">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center"><i class="fas fa-user text-gray-300 mr-1 text-xs"></i>{{ $room->max_guests }}</span>
                                <span class="flex items-center"><i class="fas fa-bed text-gray-300 mr-1 text-xs"></i>{{ $room->number_of_beds }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                            $statusConfig = [
                                'available' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500', 'icon' => 'fa-check-circle'],
                                'booked' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'dot' => 'bg-amber-500', 'icon' => 'fa-clock'],
                                'maintenance' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'dot' => 'bg-red-500', 'icon' => 'fa-tools'],
                            ];
                            $cfg = $statusConfig[$room->status] ?? $statusConfig['available'];
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full inline-flex items-center border {{ $cfg['bg'] }} {{ $cfg['text'] }} {{ $cfg['border'] }}">
                                <span class="w-1.5 h-1.5 {{ $cfg['dot'] }} rounded-full mr-1.5"></span>
                                {{ ucfirst($room->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition">
                                <a href="{{ route('admin.rooms.edit', $room) }}" class="w-8 h-8 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition flex items-center justify-center" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete(this.form, 'Room {{ $room->name }} - Do you want to delete?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 bg-red-500 text-white rounded-lg hover:bg-red-600 transition flex items-center justify-center" title="Delete">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-bed text-3xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No rooms found</p>
                            <a href="{{ route('admin.rooms.create') }}" class="inline-flex items-center mt-3 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm">
                                <i class="fas fa-plus mr-2"></i>Add First Room
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex justify-end">
        {{ $rooms->links() }}
    </div>
</div>
@endsection
