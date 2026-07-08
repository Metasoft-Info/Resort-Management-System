@extends('layouts.admin')

@section('title', 'Room Types')
@section('header', 'Room Types')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl p-6 shadow-xl text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-bed text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Room Types</h1>
                    <p class="text-primary-100 text-sm">Manage room categories and pricing</p>
                </div>
            </div>
            <a href="{{ route('admin.room-types.create') }}" class="inline-flex items-center px-5 py-2.5 bg-white text-primary-700 rounded-xl hover:bg-primary-50 transition font-semibold shadow-lg">
                <i class="fas fa-plus mr-2"></i>Add New Type
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Type Name</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Base Price</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Occupancy</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Rooms</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($roomTypes as $roomType)
                    <tr class="hover:bg-primary-50/30 transition group">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-door-open text-primary-600 text-sm"></i>
                                </div>
                                <span class="font-semibold text-gray-800">{{ $roomType->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-sm max-w-xs">{{ Str::limit($roomType->description, 50) }}</td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-primary-600">{{ number_format($roomType->base_price, 0) }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 text-sm">
                            <i class="fas fa-users text-gray-400 mr-1"></i>{{ $roomType->max_occupancy }} people
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700">
                                {{ $roomType->rooms_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition">
                                <a href="{{ route('admin.room-types.edit', $roomType) }}" class="w-8 h-8 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition flex items-center justify-center" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('admin.room-types.destroy', $roomType) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="event.preventDefault(); confirmDelete(this.form, 'Are you sure you want to delete this room type?')" class="w-8 h-8 bg-red-500 text-white rounded-lg hover:bg-red-600 transition flex items-center justify-center" title="Delete">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-bed text-3xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No room types found</p>
                            <a href="{{ route('admin.room-types.create') }}" class="inline-flex items-center mt-3 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition text-sm">
                                <i class="fas fa-plus mr-2"></i>Create First Type
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex justify-end">
        {{ $roomTypes->links() }}
    </div>
</div>
@endsection
