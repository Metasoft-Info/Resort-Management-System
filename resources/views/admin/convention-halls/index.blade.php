@extends('layouts.admin')

@section('title', 'Convention Halls')
@section('header', 'Convention Halls Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-lg font-semibold text-gray-700">Total Halls: <span class="text-primary-600">{{ $halls->total() }}</span></h3>
    </div>
    <a href="{{ route('admin.convention-halls.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-primary-600 to-accent-600 text-white rounded-xl hover:from-primary-700 hover:to-accent-700 transition font-semibold shadow-lg hover:shadow-xl">
        <i class="fas fa-plus mr-2"></i>Add New Hall
    </a>
</div>

<div class="bg-white rounded-2xl shadow-xl overflow-hidden">
    <table class="w-full">
        <thead class="bg-gradient-to-r from-primary-50 to-accent-50">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-building mr-2 text-primary-600"></i>Hall Name
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-ruler-combined mr-2 text-primary-600"></i>Dimensions
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-users mr-2 text-primary-600"></i>Capacity
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-bangladeshi-taka-sign mr-2 text-primary-600"></i>Price/Day
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
            @forelse($halls as $hall)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $hall->name }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ number_format($hall->dimensions) }} sq ft</td>
                    <td class="px-6 py-4 text-gray-700">{{ $hall->max_capacity }} guests</td>
                    <td class="px-6 py-4 font-bold text-gray-700">৳{{ number_format($hall->price_per_day, 0) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-xs font-bold rounded-full inline-flex items-center
                            @if($hall->status == 'available') bg-green-100 text-green-700
                            @elseif($hall->status == 'booked') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700
                            @endif">
                            @if($hall->status == 'available')
                                <i class="fas fa-check-circle mr-1"></i>
                            @elseif($hall->status == 'booked')
                                <i class="fas fa-clock mr-1"></i>
                            @else
                                <i class="fas fa-tools mr-1"></i>
                            @endif
                            {{ ucfirst($hall->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.convention-halls.edit', $hall) }}" class="px-3 py-1.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-xs font-semibold inline-flex items-center">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            <form action="{{ route('admin.convention-halls.destroy', $hall) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ $hall->name }}?')">
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
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="text-gray-400 text-6xl mb-3">
                            <i class="fas fa-building"></i>
                        </div>
                        <p class="text-gray-500 text-lg font-semibold">No convention halls found</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $halls->links() }}
</div>
@endsection
