@extends('layouts.admin')

@section('title', 'Addon Services')
@section('header', 'Addon Services')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-violet-600 to-purple-600 rounded-2xl p-6 shadow-xl text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-concierge-bell text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Addon Services</h1>
                    <p class="text-violet-100 text-sm">Manage convention hall extra services</p>
                </div>
            </div>
            <a href="{{ route('admin.addon-services.create') }}" class="inline-flex items-center px-5 py-2.5 bg-white text-violet-700 rounded-xl hover:bg-violet-50 transition font-semibold shadow-lg">
                <i class="fas fa-plus mr-2"></i>Add New Service
            </a>
        </div>
    </div>

    <!-- Category Filter -->
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.addon-services.index') }}" class="px-4 py-2 rounded-xl font-semibold text-sm transition {{ !request('category') ? 'bg-violet-600 text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
            <i class="fas fa-list mr-2"></i>All ({{ \App\Models\AddonService::count() }})
        </a>
        <a href="{{ route('admin.addon-services.index', ['category' => 'decoration']) }}" class="px-4 py-2 rounded-xl font-semibold text-sm transition {{ request('category') == 'decoration' ? 'bg-violet-600 text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
            🎨 Decoration
        </a>
        <a href="{{ route('admin.addon-services.index', ['category' => 'sound_system']) }}" class="px-4 py-2 rounded-xl font-semibold text-sm transition {{ request('category') == 'sound_system' ? 'bg-violet-600 text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
            🔊 Sound System
        </a>
        <a href="{{ route('admin.addon-services.index', ['category' => 'photography']) }}" class="px-4 py-2 rounded-xl font-semibold text-sm transition {{ request('category') == 'photography' ? 'bg-violet-600 text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
            📷 Photography
        </a>
        <a href="{{ route('admin.addon-services.index', ['category' => 'catering']) }}" class="px-4 py-2 rounded-xl font-semibold text-sm transition {{ request('category') == 'catering' ? 'bg-violet-600 text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
            🍽️ Catering
        </a>
        <a href="{{ route('admin.addon-services.index', ['category' => 'transport']) }}" class="px-4 py-2 rounded-xl font-semibold text-sm transition {{ request('category') == 'transport' ? 'bg-violet-600 text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
            🚗 Transport
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($addonServices as $service)
                    <tr class="hover:bg-violet-50/20 transition group">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $service->name }}</div>
                            @if($service->description)
                            <div class="text-xs text-gray-400 mt-0.5">{{ Str::limit($service->description, 40) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                            $categoryIcons = [
                                'decoration' => '🎨', 'sound_system' => '🔊', 'photography' => '📷',
                                'catering' => '🍽️', 'transport' => '🚗', 'lighting' => '💡', 'stage' => '🎭', 'other' => '📦',
                            ];
                            $categoryLabels = [
                                'decoration' => 'Decoration', 'sound_system' => 'Sound System', 'photography' => 'Photography',
                                'catering' => 'Catering', 'transport' => 'Transport', 'lighting' => 'Lighting', 'stage' => 'Stage Setup', 'other' => 'Other',
                            ];
                            @endphp
                            <span class="px-3 py-1 rounded-lg text-xs font-bold bg-violet-50 text-violet-700 border border-violet-100">
                                {{ $categoryIcons[$service->category] ?? '📦' }} {{ $categoryLabels[$service->category] ?? ucfirst($service->category) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold text-violet-600">{{ number_format($service->price, 0) }}</td>
                        <td class="px-6 py-4 text-gray-500 text-sm">{{ $service->unit ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($service->is_active)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5"></span>Active
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500">
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>Inactive
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition">
                                <a href="{{ route('admin.addon-services.edit', $service) }}" class="w-8 h-8 bg-violet-500 text-white rounded-lg hover:bg-violet-600 transition flex items-center justify-center" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('admin.addon-services.destroy', $service) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete(this.form, 'Are you sure you want to delete this service?')">
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
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-concierge-bell text-3xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No addon services found</p>
                            <a href="{{ route('admin.addon-services.create') }}" class="inline-flex items-center mt-3 px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition text-sm">
                                <i class="fas fa-plus mr-2"></i>Add First Service
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($addonServices->hasPages())
    <div class="flex justify-end">
        {{ $addonServices->links() }}
    </div>
    @endif
</div>
@endsection
