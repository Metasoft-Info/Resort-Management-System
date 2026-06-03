@extends('layouts.admin')

@section('title', 'Add New Extra Charge Category')

@section('content')
<div class="container mx-auto px-4 py-6">
 <div class="max-w-2xl mx-auto">
 <div class="flex items-center mb-6">
 <a href="{{ route('admin.extra-charge-categories.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
 <i class="fas fa-arrow-left"></i>
 </a>
 <h1 class="text-2xl font-bold text-gray-800">
 <i class="fas fa-plus-circle text-primary-600 mr-2"></i>
 Add New Extra Charge Category
 </h1>
 </div>

 @if($errors->any())
 <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
 <ul class="list-disc list-inside">
 @foreach($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 </div>
 @endif

 @if(session('success'))
 <div class="bg-primary-100 border-l-4 border-primary-500 text-primary-700 p-4 mb-6 rounded">
 {{ session('success') }}
 </div>
 @endif

 <div class="bg-white rounded-xl shadow-lg p-6">
 <form action="{{ route('admin.extra-charge-categories.store') }}" method="POST">
 @csrf

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div class="md:col-span-2">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Name *</label>
 <input type="text" name="name" value="{{ old('name') }}" required 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
 placeholder="E.g: Mineral Water, Food Package Order">
 @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
 </div>

 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Price () *</label>
 <input type="number" name="price" value="{{ old('price', 0) }}" step="0.01" min="0" required
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 @error('price')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
 </div>

 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Unit</label>
 <input type="text" name="unit" value="{{ old('unit') }}"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
 placeholder="E.g: Per piece, Per liter, Per person">
 @error('unit')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
 </div>

 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Order</label>
 <input type="number" name="order" value="{{ old('order', 0) }}" min="0"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>

 <div class="flex items-center">
 <input type="checkbox" name="is_active" id="is_active" checked
 class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
 <label for="is_active" class="ml-2 text-sm font-semibold text-gray-700">Active</label>
 </div>

 <div class="md:col-span-2">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
 <textarea name="description" rows="3"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
 placeholder="Optional Description">{{ old('description') }}</textarea>
 </div>
 </div>

 <div class="mt-6 flex justify-end gap-4">
 <a href="{{ route('admin.extra-charge-categories.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
 Cancelled
 </a>
 <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
 <i class="fas fa-save mr-2"></i>Save
 </button>
 </div>
 </form>
 </div>
 </div>
</div>
@endsection
