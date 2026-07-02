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

    <!-- Danger Zone -->
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-trash-alt text-red-500"></i>
            </div>
            <div>
                <h3 class="font-bold text-red-700 text-sm">Clear All Convention Addons</h3>
                <p class="text-red-500 text-xs">Permanently delete all convention addon services. This cannot be undone.</p>
            </div>
        </div>
        <button onclick="document.getElementById('clearConventionModal').classList.remove('hidden')"
            class="px-4 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition font-semibold text-sm shadow">
            <i class="fas fa-broom mr-1"></i>Clear All
        </button>
    </div>

    <!-- Clear Convention Modal -->
    <div id="clearConventionModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Clear All Convention Addons?</h3>
                    <p class="text-sm text-gray-500">This will permanently delete all convention addon services.</p>
                </div>
            </div>
            <form action="{{ route('admin.addon-services.clear-convention') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Type <span class="font-mono bg-gray-100 px-2 py-0.5 rounded text-red-600">CLEAR</span> to confirm</label>
                    <input type="text" name="confirm" required placeholder="CLEAR"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-2 focus:ring-red-200 transition uppercase">
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('clearConventionModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-xl hover:bg-red-600 transition font-semibold">
                        <i class="fas fa-trash mr-1"></i>Delete All
                    </button>
                </div>
            </form>
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
                                <form action="{{ route('admin.addon-services.destroy', $service) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete(this, 'Are you sure you want to delete this service?')">
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
