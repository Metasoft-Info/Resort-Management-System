@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8"><h1 class="text-3xl font-bold text-gray-800">হিরো স্লাইড সম্পাদনা</h1></div>
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('admin.hero-slides.update', $heroSlide) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="block text-sm font-semibold text-gray-700 mb-2">শিরোনাম *</label><input type="text" name="title" value="{{ old('title', $heroSlide->title) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></div>
                <div><label class="block text-sm font-semibold text-gray-700 mb-2">অর্ডার *</label><input type="number" name="order" value="{{ old('order', $heroSlide->order) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></div>
                <div class="md:col-span-2"><label class="block text-sm font-semibold text-gray-700 mb-2">সাবটাইটেল</label><input type="text" name="subtitle" value="{{ old('subtitle', $heroSlide->subtitle) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></div>
                <div class="md:col-span-2"><label class="block text-sm font-semibold text-gray-700 mb-2">ছবি URL</label><input type="text" name="image" value="{{ old('image', $heroSlide->image) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></div>
            </div>
            <div class="flex gap-4 mt-8">
                <button type="submit" class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-8 py-3 rounded-lg hover:from-indigo-700 hover:to-indigo-800 transition shadow-lg"><i class="fas fa-save mr-2"></i>আপডেট করুন</button>
                <a href="{{ route('admin.hero-slides.index') }}" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition"><i class="fas fa-times mr-2"></i>বাতিল</a>
            </div>
        </form>
    </div>
</div>
@endsection
