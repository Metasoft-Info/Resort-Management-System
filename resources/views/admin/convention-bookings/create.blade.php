@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8"><h1 class="text-3xl font-bold text-gray-800">নতুন কনভেনশন বুকিং</h1></div>
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('admin.convention-bookings.store') }}" method="POST">
            @csrf
            
            <!-- Customer Information -->
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user text-blue-600 mr-3"></i>গ্রাহক তথ্য
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">গ্রাহকের নাম *</label>
                        <input type="text" name="customer_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">ফোন নম্বর *</label>
                        <input type="tel" name="customer_phone" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">হোয়াটসঅ্যাপ</label>
                        <input type="tel" name="customer_whatsapp" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">এনআইডি নম্বর</label>
                        <input type="text" name="customer_nid" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">ইমেইল</label>
                        <input type="email" name="customer_email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">প্রতিষ্ঠানের নাম</label>
                        <input type="text" name="organization_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">ঠিকানা</label>
                        <textarea name="customer_address" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                </div>
            </div>

            <!-- Event Information -->
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-calendar-alt text-green-600 mr-3"></i>ইভেন্ট তথ্য
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">কনভেনশন হল *</label>
                        <select name="hall_id" id="hall_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" onchange="updateHallRent()">
                            <option value="">হল নির্বাচন করুন</option>
                            @foreach($halls as $hall)
                            <option value="{{ $hall->id }}" data-price="{{ $hall->price_per_day }}">{{ $hall->name }} (ধারণক্ষমতা: {{ $hall->capacity }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">ইভেন্টের তারিখ *</label>
                        <input type="date" name="event_date" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">সময় স্লট *</label>
                        <select name="time_slot" id="time_slot" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" onchange="updateHallRent()">
                            <option value="">সময় নির্বাচন করুন</option>
                            <option value="morning">সকাল (৮টা - ১২টা)</option>
                            <option value="afternoon">দুপুর (১২টা - ৫টা)</option>
                            <option value="evening">সন্ধ্যা (৫টা - ১০টা)</option>
                            <option value="fullday">সারাদিন</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">ইভেন্টের ধরন *</label>
                        <input type="text" name="event_type" required placeholder="বিয়ে, কনফারেন্স, সেমিনার" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">অতিথি সংখ্যা *</label>
                        <input type="number" name="number_of_guests" value="1" min="1" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">ইভেন্ট বিবরণ</label>
                        <textarea name="event_description" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-calculator text-indigo-600 mr-3"></i>মূল্য তথ্য
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">হল ভাড়া (৳) *</label>
                        <input type="number" name="hall_rent" id="hall_rent" value="0" step="0.01" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">অগ্রিম পেমেন্ট (৳)</label>
                        <input type="number" name="advance_payment" value="0" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">পেমেন্ট পদ্ধতি *</label>
                        <select name="payment_method" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="cash">ক্যাশ</option>
                            <option value="card">কার্ড</option>
                            <option value="mfs">মোবাইল ব্যাংকিং</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Status & Notes -->
            <div class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">স্ট্যাটাস *</label>
                        <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500">
                            <option value="pending">পেন্ডিং</option>
                            <option value="confirmed" selected>নিশ্চিত</option>
                            <option value="completed">সম্পন্ন</option>
                            <option value="cancelled">বাতিল</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">বিশেষ নোট</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500"></textarea>
                    </div>
                </div>
            </div>

            <input type="hidden" name="food_cost" value="0">
            <input type="hidden" name="addons_cost" value="0">
            <input type="hidden" name="total_amount" id="total_amount" value="0">

            <div class="flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-green-600 to-green-700 text-white px-8 py-4 rounded-lg hover:from-green-700 hover:to-green-800 transition shadow-lg font-semibold text-lg">
                    <i class="fas fa-save mr-2"></i>বুকিং সংরক্ষণ করুন
                </button>
                <a href="{{ route('admin.convention-bookings.index') }}" class="bg-gray-500 text-white px-8 py-4 rounded-lg hover:bg-gray-600 transition font-semibold text-lg">
                    <i class="fas fa-times mr-2"></i>বাতিল
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function updateHallRent() {
    const hallSelect = document.getElementById('hall_id');
    const timeSlot = document.getElementById('time_slot').value;
    const selectedOption = hallSelect.options[hallSelect.selectedIndex];
    
    if (selectedOption && selectedOption.dataset.price) {
        let price = parseFloat(selectedOption.dataset.price);
        
        // Apply time slot pricing
        if (timeSlot === 'morning' || timeSlot === 'afternoon' || timeSlot === 'evening') {
            price = price * 0.4; // 40% for half day slots
        }
        
        document.getElementById('hall_rent').value = price.toFixed(2);
        document.getElementById('total_amount').value = price.toFixed(2);
    }
}
</script>
@endsection
