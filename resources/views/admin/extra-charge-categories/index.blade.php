@extends('layouts.admin')

@section('title', 'Extra Charge Categories')
@section('header', 'Extra Charge Categories')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl p-6 shadow-xl text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-tags text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Extra Charge Categories</h1>
                    <p class="text-amber-100 text-sm">Manage additional service charges</p>
                </div>
            </div>
            <a href="{{ route('admin.extra-charge-categories.create') }}" class="inline-flex items-center px-5 py-2.5 bg-white text-amber-700 rounded-xl hover:bg-amber-50 transition font-semibold shadow-lg">
                <i class="fas fa-plus mr-2"></i>New Category
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-r-xl flex items-center">
        <i class="fas fa-check-circle mr-3"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($categories as $category)
                    <tr class="hover:bg-amber-50/20 transition group">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center justify-center w-7 h-7 bg-gray-100 rounded-lg text-xs font-bold text-gray-600">
                                {{ $category->order }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $category->name }}</div>
                            @if($category->description)
                            <div class="text-xs text-gray-400 mt-0.5">{{ Str::limit($category->description, 40) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-amber-600">{{ number_format($category->price, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-sm">{{ $category->unit ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($category->is_active)
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
                                <a href="{{ route('admin.extra-charge-categories.edit', $category) }}" class="w-8 h-8 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition flex items-center justify-center" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('admin.extra-charge-categories.destroy', $category) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete(this.form, 'Are you sure you want to delete this category?')">
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
                                <i class="fas fa-tags text-3xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No categories found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex justify-end">
        {{ $categories->links() }}
    </div>
</div>
@endsection
