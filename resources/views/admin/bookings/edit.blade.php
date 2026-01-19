@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8"><h1 class="text-3xl font-bold text-gray-800">বুকিং সম্পাদনা</h1></div>
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">রুম *</label>
                    <select name="room_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @foreach(\App\Models\Room::all() as $room)
                        <option value="{{ $room->id }}" {{ $booking->room_id == $room->id ? 'selected' : '' }}>{{ $room->room_number }} - {{ $room->roomType->name ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">চেক-ইন তারিখ *</label>
                    <input type="date" name="check_in_date" value="{{ old('check_in_date', $booking->check_in_date) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">চেক-আউট তারিখ *</label>
                    <input type="date" name="check_out_date" value="{{ old('check_out_date', $booking->check_out_date) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">গ্রাহকের নাম *</label>
                    <input type="text" name="guest_name" value="{{ old('guest_name', $booking->guest_name) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ফোন নম্বর *</label>
                    <input type="tel" name="guest_phone" value="{{ old('guest_phone', $booking->guest_phone) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ইমেইল</label>
                    <input type="email" name="guest_email" value="{{ old('guest_email', $booking->guest_email) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">অতিথি সংখ্যা *</label>
                    <input type="number" name="number_of_guests" value="{{ old('number_of_guests', $booking->number_of_guests) }}" min="1" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">মোট টাকা (৳) *</label>
                    <input type="number" name="total_amount" value="{{ old('total_amount', $booking->total_amount) }}" step="0.01" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">অগ্রিম পেমেন্ট (৳)</label>
                    <input type="number" name="advance_payment" value="{{ old('advance_payment', $booking->advance_payment) }}" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">স্ট্যাটাস *</label>
                    <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>পেন্ডিং</option>
                        <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>নিশ্চিত</option>
                        <option value="checked_in" {{ $booking->status == 'checked_in' ? 'selected' : '' }}>চেক-ইন</option>
                        <option value="checked_out" {{ $booking->status == 'checked_out' ? 'selected' : '' }}>চেক-আউট</option>
                        <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>বাতিল</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ঠিকানা</label>
                    <textarea name="guest_address" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('guest_address', $booking->guest_address) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">বিশেষ অনুরোধ</label>
                    <textarea name="special_requests" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('special_requests', $booking->special_requests) }}</textarea>
                </div>
            </div>
            <div class="flex gap-4 mt-8">
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-lg"><i class="fas fa-save mr-2"></i>আপডেট করুন</button>
                <a href="{{ route('admin.bookings.index') }}" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition"><i class="fas fa-times mr-2"></i>বাতিল</a>
            </div>
        </form>
    </div>
</div>
@endsection
