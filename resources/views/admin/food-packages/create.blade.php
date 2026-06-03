@extends('layouts.admin')
@section('content')
<div class="p-6">
 <div class="mb-8"><h1 class="text-3xl font-bold text-gray-800">New Food Package</h1></div>
 <div class="bg-white rounded-xl shadow-lg p-8">
 <form action="{{ route('admin.food-packages.store') }}" method="POST">
 @csrf
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div><label class="block text-sm font-semibold text-gray-700 mb-2">Name *</label><input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"></div>
 <div><label class="block text-sm font-semibold text-gray-700 mb-2">Price () *</label><input type="number" name="price" value="{{ old('price') }}" step="0.01" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"></div>
 <div class="md:col-span-2"><label class="block text-sm font-semibold text-gray-700 mb-2">Description</label><textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">{{ old('description') }}</textarea></div>
 <div class="md:col-span-2"><label class="block text-sm font-semibold text-gray-700 mb-2">Items</label><textarea name="items" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500" placeholder=", , All">{{ old('items') }}</textarea></div>
 </div>
 <div class="flex gap-4 mt-8">
 <button type="submit" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-8 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg"><i class="fas fa-save mr-2"></i>Save</button>
 <a href="{{ route('admin.food-packages.index') }}" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition"><i class="fas fa-times mr-2"></i>Cancelled</a>
 </div>
 </form>
 </div>
</div>
@endsection