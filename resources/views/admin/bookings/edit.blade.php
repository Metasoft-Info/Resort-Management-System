@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8"><h1 class="text-3xl font-bold text-gray-800">বুকিং সম্পাদনা #{{ $booking->id }}</h1></div>
    
    @if(session('success'))
    <div class="bg-primary-100 border border-primary-400 text-primary-700 px-4 py-3 rounded-lg mb-6">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">{{ session('error') }}</div>
    @endif
    
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
            @csrf @method('PUT')
            
            <!-- Room & Dates -->
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fas fa-bed mr-2 text-primary-600"></i>রুম ও তারিখ</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">রুম *</label>
                    <select name="room_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        @foreach(\App\Models\Room::with('roomType')->get() as $room)
                        <option value="{{ $room->id }}" {{ $booking->room_id == $room->id ? 'selected' : '' }}>{{ $room->room_number }} - {{ $room->roomType->name ?? 'N/A' }} (৳{{ number_format($room->roomType->price_per_night ?? 0) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">চেক-ইন তারিখ *</label>
                    <input type="date" name="check_in_date" value="{{ old('check_in_date', $booking->check_in_date ? $booking->check_in_date->format('Y-m-d') : '') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">চেক-আউট তারিখ *</label>
                    <input type="date" name="check_out_date" value="{{ old('check_out_date', $booking->check_out_date ? $booking->check_out_date->format('Y-m-d') : '') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <!-- Customer Info -->
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fas fa-user mr-2 text-primary-600"></i>গ্রাহক তথ্য</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">গ্রাহকের নাম *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', $booking->customer_name) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ফোন নম্বর *</label>
                    <input type="tel" name="customer_phone" value="{{ old('customer_phone', $booking->customer_phone) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ইমেইল</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email', $booking->customer_email) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">NID নম্বর</label>
                    <input type="text" name="customer_nid" value="{{ old('customer_nid', $booking->customer_nid) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">কোম্পানী</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $booking->company_name) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">অতিথি সংখ্যা</label>
                    <input type="number" name="number_of_guests" value="{{ old('number_of_guests', $booking->number_of_guests) }}" min="1" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ঠিকানা</label>
                    <textarea name="customer_address" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('customer_address', $booking->customer_address) }}</textarea>
                </div>
            </div>

            <!-- Reference Info -->
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fas fa-user-tag mr-2 text-primary-600"></i>রেফারেন্স তথ্য</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">রেফারেন্স নাম</label>
                    <input type="text" name="reference_name" value="{{ old('reference_name', $booking->reference_name) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">রেফারেন্স ফোন</label>
                    <input type="tel" name="reference_phone" value="{{ old('reference_phone', $booking->reference_phone) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <!-- Payment Info -->
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fas fa-money-bill mr-2 text-primary-600"></i>পেমেন্ট তথ্য</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">মোট টাকা (৳)</label>
                    <input type="number" name="total_amount" value="{{ old('total_amount', $booking->total_amount) }}" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">অগ্রিম জমা (৳)</label>
                    <input type="number" name="advance_payment" value="{{ old('advance_payment', $booking->advance_payment) }}" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">বাকি (৳)</label>
                    <input type="number" name="remaining_payment" value="{{ old('remaining_payment', $booking->remaining_payment) }}" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 bg-gray-100" readonly>
                </div>
            </div>

            <!-- Status -->
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fas fa-cog mr-2 text-primary-600"></i>স্ট্যাটাস</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">বুকিং স্ট্যাটাস *</label>
                    <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>পেন্ডিং</option>
                        <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>নিশ্চিত</option>
                        <option value="checked_in" {{ $booking->status == 'checked_in' ? 'selected' : '' }}>চেক-ইন</option>
                        <option value="checked_out" {{ $booking->status == 'checked_out' ? 'selected' : '' }}>চেক-আউট</option>
                        <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>বাতিল</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">পেমেন্ট স্ট্যাটাস</label>
                    <select name="payment_status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="pending" {{ $booking->payment_status == 'pending' ? 'selected' : '' }}>পেন্ডিং</option>
                        <option value="partial" {{ $booking->payment_status == 'partial' ? 'selected' : '' }}>আংশিক</option>
                        <option value="paid" {{ $booking->payment_status == 'paid' ? 'selected' : '' }}>পরিশোধিত</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">নোট</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('notes', $booking->notes) }}</textarea>
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <button type="submit" class="bg-primary-600 text-white px-8 py-3 rounded-lg hover:bg-primary-700 transition shadow-lg"><i class="fas fa-save mr-2"></i>আপডেট করুন</button>
                <a href="{{ route('admin.bookings.show', $booking) }}" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition"><i class="fas fa-eye mr-2"></i>বিস্তারিত</a>
                <a href="{{ route('admin.bookings.index') }}" class="bg-gray-400 text-white px-8 py-3 rounded-lg hover:bg-gray-500 transition"><i class="fas fa-list mr-2"></i>তালিকা</a>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelector('input[name="total_amount"]').addEventListener('input', updateRemaining);
document.querySelector('input[name="advance_payment"]').addEventListener('input', updateRemaining);

function updateRemaining() {
    const total = parseFloat(document.querySelector('input[name="total_amount"]').value) || 0;
    const advance = parseFloat(document.querySelector('input[name="advance_payment"]').value) || 0;
    document.querySelector('input[name="remaining_payment"]').value = (total - advance).toFixed(2);
}
</script>
@endsection
