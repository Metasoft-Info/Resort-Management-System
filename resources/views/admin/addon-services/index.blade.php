@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Addon Services</h1>
            <p class="text-gray-600 mt-2">Manage addon services for Convention Hall bookings</p>
        </div>
        <a href="{{ route('admin.addon-services.create') }}" class="bg-gradient-to-r from-violet-600 to-purple-600 text-white px-6 py-3 rounded-xl hover:from-violet-700 hover:to-purple-700 transition shadow-lg">
            <i class="fas fa-plus mr-2"></i>Add New Service
        </a>
    </div>

    <!-- Category Filter -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('admin.addon-services.index') }}" class="px-4 py-2 rounded-lg font-semibold transition {{ !request('category') ? 'bg-violet-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            <i class="fas fa-list mr-2"></i>All ({{ \App\Models\AddonService::count() }})
        </a>
        <a href="{{ route('admin.addon-services.index', ['category' => 'decoration']) }}" class="px-4 py-2 rounded-lg font-semibold transition {{ request('category') == 'decoration' ? 'bg-violet-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            🎨 Decoration
        </a>
        <a href="{{ route('admin.addon-services.index', ['category' => 'sound_system']) }}" class="px-4 py-2 rounded-lg font-semibold transition {{ request('category') == 'sound_system' ? 'bg-violet-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            🔊 Sound System
        </a>
        <a href="{{ route('admin.addon-services.index', ['category' => 'photography']) }}" class="px-4 py-2 rounded-lg font-semibold transition {{ request('category') == 'photography' ? 'bg-violet-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            📷 Photography
        </a>
        <a href="{{ route('admin.addon-services.index', ['category' => 'catering']) }}" class="px-4 py-2 rounded-lg font-semibold transition {{ request('category') == 'catering' ? 'bg-violet-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            🍽️ Catering
        </a>
        <a href="{{ route('admin.addon-services.index', ['category' => 'transport']) }}" class="px-4 py-2 rounded-lg font-semibold transition {{ request('category') == 'transport' ? 'bg-violet-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            🚗 Transport
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gradient-to-r from-violet-50 to-purple-50">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-bold text-violet-700">Name</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-violet-700">Category</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-violet-700">Price</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-violet-700">Unit</th>
                    <th class="px-6 py-4 text-center text-sm font-bold text-violet-700">Status</th>
                    <th class="px-6 py-4 text-right text-sm font-bold text-violet-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($addonServices as $service)
                <tr class="hover:bg-violet-50/50 transition">
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-800">{{ $service->name }}</div>
                        @if($service->description)
                        <div class="text-xs text-gray-500">{{ Str::limit($service->description, 40) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $categoryIcons = [
                                'decoration' => '🎨',
                                'sound_system' => '🔊',
                                'photography' => '📷',
                                'catering' => '🍽️',
                                'transport' => '🚗',
                                'lighting' => '💡',
                                'stage' => '🎭',
                                'other' => '📦',
                            ];
                            $categoryLabels = [
                                'decoration' => 'Decoration',
                                'sound_system' => 'Sound System',
                                'photography' => 'Photography',
                                'catering' => 'Catering',
                                'transport' => 'Transport',
                                'lighting' => 'Lighting',
                                'stage' => 'Stage Setup',
                                'other' => 'Other',
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-violet-100 text-violet-800">
                            {{ $categoryIcons[$service->category] ?? '📦' }} {{ $categoryLabels[$service->category] ?? ucfirst($service->category) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-800 font-semibold">৳{{ number_format($service->price, 0) }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $service->unit ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($service->is_active)
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Active</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.addon-services.edit', $service) }}" class="text-violet-600 hover:text-violet-800 mr-3" title="Edit"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.addon-services.destroy', $service) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="event.preventDefault(); confirmDelete(this.form, 'Are you sure you want to delete this service?')" class="text-red-600 hover:text-red-800" title="Delete"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="text-gray-400 text-5xl mb-4"><i class="fas fa-concierge-bell"></i></div>
                        <p class="text-gray-500 font-semibold">No addon services found</p>
                        <a href="{{ route('admin.addon-services.create') }}" class="inline-block mt-4 px-6 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition">
                            <i class="fas fa-plus mr-2"></i>Add First Service
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($addonServices->hasPages())
    <div class="mt-6">
        {{ $addonServices->links() }}
    </div>
    @endif
</div>
@endsection
