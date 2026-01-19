@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8"><h1 class="text-3xl font-bold text-gray-800">নতুন অ্যাডঅন সার্ভিস</h1></div>
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('admin.addon-services.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">নাম *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">মূল্য (৳) *</label>
                    <input type="number" name="price" value="{{ old('price') }}" step="0.01" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ইউনিট</label>
                    <input type="text" name="unit" value="{{ old('unit') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="প্রতি ঘণ্টা, প্রতি দিন">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">বর্ণনা</label>
                    <textarea name="description" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">{{ old('description') }}</textarea>
                </div>
            </div>
            <div class="flex gap-4 mt-8">
                <button type="submit" class="bg-gradient-to-r from-purple-600 to-purple-700 text-white px-8 py-3 rounded-lg hover:from-purple-700 hover:to-purple-800 transition shadow-lg"><i class="fas fa-save mr-2"></i>সংরক্ষণ করুন</button>
                <a href="{{ route('admin.addon-services.index') }}" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition"><i class="fas fa-times mr-2"></i>বাতিল</a>
            </div>
        </form>
    </div>
</div>
@endsection
