@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8"><h1 class="text-3xl font-bold text-gray-800">নতুন বুকিং</h1></div>
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('admin.bookings.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">রুম *</label>
                    <select name="room_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">রুম নির্বাচন করুন</option>
                        @foreach(\App\Models\Room::where('status', 'available')->get() as $room)
                        <option value="{{ $room->id }}">{{ $room->room_number }} - {{ $room->roomType->name ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">চেক-ইন তারিখ *</label>
                    <input type="date" name="check_in_date" value="{{ old('check_in_date') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">চেক-আউট তারিখ *</label>
                    <input type="date" name="check_out_date" value="{{ old('check_out_date') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">গ্রাহকের নাম *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ফোন নম্বর *</label>
                    <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ইমেইল *</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">NID নম্বর *</label>
                    <input type="text" name="customer_nid" value="{{ old('customer_nid') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">অতিথি সংখ্যা *</label>
                    <input type="number" name="number_of_guests" value="{{ old('number_of_guests', 1) }}" min="1" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">মোট টাকা (৳) *</label>
                    <input type="number" name="total_amount" value="{{ old('total_amount') }}" step="0.01" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">অগ্রিম পেমেন্ট (৳) *</label>
                    <input type="number" name="advance_payment" value="{{ old('advance_payment', 0) }}" step="0.01" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">পেমেন্ট মেথড *</label>
                    <select name="payment_method" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="cash">নগদ</option>
                        <option value="card">কার্ড</option>
                        <option value="mfs">মোবাইল ব্যাংকিং</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">স্ট্যাটাস *</label>
                    <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="pending">পেন্ডিং</option>
                        <option value="confirmed" selected>নিশ্চিত</option>
                        <option value="checked_in">চেক-ইন</option>
                        <option value="checked_out">চেক-আউট</option>
                        <option value="cancelled">বাতিল</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ঠিকানা</label>
                    <textarea name="customer_address" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('customer_address') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">বিশেষ অনুরোধ</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="flex gap-4 mt-8">
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-lg"><i class="fas fa-save mr-2"></i>সংরক্ষণ করুন</button>
                <a href="{{ route('admin.bookings.index') }}" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition"><i class="fas fa-times mr-2"></i>বাতিল</a>
            </div>
        </form>
    </div>
</div>
@endsection
