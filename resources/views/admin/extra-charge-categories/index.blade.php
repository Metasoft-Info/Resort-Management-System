@extends('layouts.admin')

@section('title', 'Extra Charge Categories')

@section('content')
<div class="container mx-auto px-4 py-6">
 <div class="flex justify-between items-center mb-6">
 <h1 class="text-2xl font-bold text-gray-800">
 <i class="fas fa-tags text-primary-600 mr-2"></i>
 Extra Charge Categories
 </h1>
 <a href="{{ route('admin.extra-charge-categories.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
 <i class="fas fa-plus mr-2"></i>New Category
 </a>
 </div>

 @if(session('success'))
 <div class="bg-primary-100 border-l-4 border-primary-500 text-primary-700 p-4 mb-6 rounded">
 {{ session('success') }}
 </div>
 @endif

 <div class="bg-white rounded-xl shadow-lg overflow-hidden">
 <table class="min-w-full divide-y divide-gray-200">
 <thead class="bg-gray-50">
 <tr>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price ()</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
 <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200">
 @forelse($categories as $category)
 <tr class="hover:bg-gray-50">
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->order }}</td>
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
 @if($category->description)
 <div class="text-xs text-gray-500">{{ Str::limit($category->description, 50) }}</div>
 @endif
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-primary-600">{{ number_format($category->price, 2) }}</td>
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->unit ?? '-' }}</td>
 <td class="px-6 py-4 whitespace-nowrap">
 @if($category->is_active)
 <span class="px-2 py-1 text-xs font-semibold rounded-full bg-primary-100 text-primary-800">Active</span>
 @else
 <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
 @endif
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
 <a href="{{ route('admin.extra-charge-categories.edit', $category) }}" class="text-primary-600 hover:text-primary-900 mr-3">
 <i class="fas fa-edit"></i>
 </a>
 <form action="{{ route('admin.extra-charge-categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
 @csrf
 @method('DELETE')
 <button type="submit" class="text-red-600 hover:text-red-900">
 <i class="fas fa-trash"></i>
 </button>
 </form>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="6" class="px-6 py-4 text-center text-gray-500">No categories found</td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>

 <div class="mt-4">
 {{ $categories->links() }}
 </div>
</div>
@endsection
