@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8"><h1 class="text-3xl font-bold text-gray-800">নতুন ব্যবহারকারী</h1></div>
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="block text-sm font-semibold text-gray-700 mb-2">নাম *</label><input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></div>
                <div><label class="block text-sm font-semibold text-gray-700 mb-2">ইমেইল *</label><input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></div>
                <div><label class="block text-sm font-semibold text-gray-700 mb-2">পাসওয়ার্ড *</label><input type="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></div>
                <div><label class="block text-sm font-semibold text-gray-700 mb-2">রোল *</label><select name="role" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"><option value="admin">অ্যাডমিন</option><option value="manager">ম্যানেজার</option><option value="receptionist">রিসেপশনিস্ট</option></select></div>
            </div>
            <div class="flex gap-4 mt-8">
                <button type="submit" class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-8 py-3 rounded-lg hover:from-indigo-700 hover:to-indigo-800 transition shadow-lg"><i class="fas fa-save mr-2"></i>সংরক্ষণ করুন</button>
                <a href="{{ route('admin.users.index') }}" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition"><i class="fas fa-times mr-2"></i>বাতিল</a>
            </div>
        </form>
    </div>
</div>
@endsection
