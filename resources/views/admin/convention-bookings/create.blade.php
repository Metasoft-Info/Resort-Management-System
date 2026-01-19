@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8"><h1 class="text-3xl font-bold text-gray-800">নতুন কনভেনশন বুকিং</h1></div>
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('admin.convention-bookings.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">কনভেনশন হল *</label>
                    <select name="convention_hall_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">হল নির্বাচন করুন</option>
                        @foreach(\App\Models\ConventionHall::all() as $hall)
                        <option value="{{ $hall->id }}">{{ $hall->name }} (ধারণক্ষমতা: {{ $hall->capacity }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ইভেন্টের তারিখ *</label>
                    <input type="date" name="event_date" value="{{ old('event_date') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">সময় *</label>
                    <select name="time_slot" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="morning">সকাল (৮টা - ১২টা)</option>
                        <option value="afternoon">দুপুর (১২টা - ৫টা)</option>
                        <option value="evening">সন্ধ্যা (৫টা - ১০টা)</option>
                        <option value="full_day">সারাদিন</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">গ্রাহকের নাম *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ফোন নম্বর *</label>
                    <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ইমেইল</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">অতিথি সংখ্যা *</label>
                    <input type="number" name="number_of_guests" value="{{ old('number_of_guests', 1) }}" min="1" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ইভেন্টের ধরন</label>
                    <input type="text" name="event_type" value="{{ old('event_type') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="বিয়ে, কনফারেন্স">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">মোট টাকা (৳) *</label>
                    <input type="number" name="total_amount" value="{{ old('total_amount') }}" step="0.01" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">অগ্রিম পেমেন্ট (৳)</label>
                    <input type="number" name="advance_payment" value="{{ old('advance_payment', 0) }}" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">স্ট্যাটাস *</label>
                    <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="pending">পেন্ডিং</option>
                        <option value="confirmed" selected>নিশ্চিত</option>
                        <option value="completed">সম্পন্ন</option>
                        <option value="cancelled">বাতিল</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">বিশেষ অনুরোধ</label>
                    <textarea name="special_requests" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">{{ old('special_requests') }}</textarea>
                </div>
            </div>
            <div class="flex gap-4 mt-8">
                <button type="submit" class="bg-gradient-to-r from-green-600 to-green-700 text-white px-8 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition shadow-lg"><i class="fas fa-save mr-2"></i>সংরক্ষণ করুন</button>
                <a href="{{ route('admin.convention-bookings.index') }}" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition"><i class="fas fa-times mr-2"></i>বাতিল</a>
            </div>
        </form>
    </div>
</div>
@endsection
