@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">🎪 কনভেনশন বুকিং সম্পাদনা</h1>
        <p class="text-gray-600 mt-2">বুকিং তথ্য, অ্যাডঅন, ডিসকাউন্ট এবং পেমেন্ট আপডেট করুন</p>
    </div>

    <form action="{{ route('admin.convention-bookings.update', $conventionBooking) }}" method="POST" id="editForm">
        @csrf
        @method('PUT')
        
        <!-- Customer & Event Details -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-purple-600 flex items-center">
                <i class="fas fa-user mr-3"></i>গ্রাহক ও ইভেন্ট বিবরণ
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">ইভেন্টের তারিখ *</label>
                    <input type="date" name="event_date" value="{{ $conventionBooking->event_date->format('Y-m-d') }}" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">সময় স্লট *</label>
                    <select name="time_slot" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                        <option value="morning" {{ $conventionBooking->time_slot == 'morning' ? 'selected' : '' }}>🌅 সকাল</option>
                        <option value="afternoon" {{ $conventionBooking->time_slot == 'afternoon' ? 'selected' : '' }}>☀️ দুপুর</option>
                        <option value="evening" {{ $conventionBooking->time_slot == 'evening' ? 'selected' : '' }}>🌙 সন্ধ্যা</option>
                        <option value="fullday" {{ $conventionBooking->time_slot == 'fullday' ? 'selected' : '' }}>🌞 সারাদিন</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">কনভেনশন হল *</label>
                    <select name="hall_id" id="hallId" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600"
                        onchange="updateHallRent()">
                        @foreach($halls as $hall)
                        <option value="{{ $hall->id }}" data-price="{{ $hall->price_per_day }}" {{ $conventionBooking->hall_id == $hall->id ? 'selected' : '' }}>
                            {{ $hall->name }} - ৳{{ number_format($hall->price_per_day, 0) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">হল ভাড়া (৳) *</label>
                    <input type="number" name="hall_rent" id="hallRent" value="{{ $conventionBooking->hall_rent }}" required step="0.01"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600"
                        onchange="calculateTotal()">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">গ্রাহকের নাম *</label>
                    <input type="text" name="customer_name" value="{{ $conventionBooking->customer_name }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">ফোন নম্বর *</label>
                    <input type="tel" name="customer_phone" value="{{ $conventionBooking->customer_phone }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">ইমেইল</label>
                    <input type="email" name="customer_email" value="{{ $conventionBooking->customer_email }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">হোয়াটসঅ্যাপ</label>
                    <input type="tel" name="customer_whatsapp" value="{{ $conventionBooking->customer_whatsapp }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">এনআইডি</label>
                    <input type="text" name="customer_nid" value="{{ $conventionBooking->customer_nid }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">প্রতিষ্ঠানের নাম</label>
                    <input type="text" name="organization_name" value="{{ $conventionBooking->organization_name }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">ইভেন্টের ধরন *</label>
                    <select name="event_type" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                        <option value="conference" {{ $conventionBooking->event_type == 'conference' ? 'selected' : '' }}>কনফারেন্স</option>
                        <option value="wedding" {{ $conventionBooking->event_type == 'wedding' ? 'selected' : '' }}>বিয়ে</option>
                        <option value="meeting" {{ $conventionBooking->event_type == 'meeting' ? 'selected' : '' }}>মিটিং</option>
                        <option value="seminar" {{ $conventionBooking->event_type == 'seminar' ? 'selected' : '' }}>সেমিনার</option>
                        <option value="party" {{ $conventionBooking->event_type == 'party' ? 'selected' : '' }}>পার্টি</option>
                        <option value="other" {{ $conventionBooking->event_type == 'other' ? 'selected' : '' }}>অন্যান্য</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">অতিথি সংখ্যা *</label>
                    <input type="number" name="number_of_guests" id="numberOfGuests" value="{{ $conventionBooking->number_of_guests }}" min="1" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600"
                        onchange="updateFoodCost()">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">ঠিকানা</label>
                    <textarea name="customer_address" rows="2"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">{{ $conventionBooking->customer_address }}</textarea>
                </div>
            </div>
        </div>

        <!-- Food Packages -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-orange-600 flex items-center">
                <i class="fas fa-utensils mr-3"></i>খাবার প্যাকেজ
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="border-2 border-gray-300 rounded-lg p-4 cursor-pointer hover:border-orange-500 transition {{ !$conventionBooking->food_package_id ? 'border-orange-600 bg-orange-50' : '' }}"
                    onclick="selectFoodPackage(0, 'নিজস্ব', 0)">
                    <input type="radio" name="food_package_id" value="" {{ !$conventionBooking->food_package_id ? 'checked' : '' }} class="mr-3">
                    <span class="font-semibold">নিজস্ব খাবার</span>
                </div>
                @foreach($foodPackages as $package)
                <div class="border-2 border-gray-300 rounded-lg p-4 cursor-pointer hover:border-orange-500 transition {{ $conventionBooking->food_package_id == $package->id ? 'border-orange-600 bg-orange-50' : '' }}"
                    onclick="selectFoodPackage({{ $package->id }}, '{{ $package->name }}', {{ $package->price_per_person }})">
                    <input type="radio" name="food_package_id" value="{{ $package->id }}" {{ $conventionBooking->food_package_id == $package->id ? 'checked' : '' }} class="mr-3">
                    <div>
                        <div class="font-semibold">{{ $package->name }}</div>
                        <div class="text-orange-600 font-bold">৳{{ number_format($package->price_per_person, 0) }}/person</div>
                    </div>
                </div>
                @endforeach
            </div>
            <input type="hidden" name="food_cost" id="foodCost" value="{{ $conventionBooking->food_cost }}">
        </div>

        <!-- Addon Services -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-purple-600 flex items-center">
                <i class="fas fa-plus-circle mr-3"></i>অ্যাডঅন সার্ভিস
            </h2>
            <div class="flex flex-wrap gap-2 mb-4">
                <button type="button" class="px-4 py-2 rounded-lg bg-purple-600 text-white" onclick="filterAddons('all')">সব</button>
                <button type="button" class="px-4 py-2 rounded-lg bg-gray-200" onclick="filterAddons('decoration')">সাজসজ্জা</button>
                <button type="button" class="px-4 py-2 rounded-lg bg-gray-200" onclick="filterAddons('sound_system')">সাউন্ড</button>
                <button type="button" class="px-4 py-2 rounded-lg bg-gray-200" onclick="filterAddons('photography')">ফটোগ্রাফি</button>
                <button type="button" class="px-4 py-2 rounded-lg bg-gray-200" onclick="filterAddons('catering')">ক্যাটারিং</button>
                <button type="button" class="px-4 py-2 rounded-lg bg-gray-200" onclick="filterAddons('transport')">পরিবহন</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                    $selectedAddons = $conventionBooking->selected_addons ?? [];
                    $addonQuantities = $conventionBooking->addon_quantities ?? [];
                @endphp
                @foreach($addonServices as $addon)
                <div class="border-2 border-gray-200 rounded-lg p-4 addon-item" data-category="{{ $addon->category }}">
                    <div class="flex items-start mb-2">
                        <input type="checkbox" name="selected_addons[]" value="{{ $addon->id }}" 
                            {{ in_array($addon->id, $selectedAddons) ? 'checked' : '' }}
                            class="mr-3 w-5 h-5" data-price="{{ $addon->price }}" 
                            onchange="toggleAddonQuantity({{ $addon->id }}, this.checked)">
                        <div class="flex-1">
                            <div class="font-semibold">{{ $addon->name }}</div>
                            <div class="text-purple-600 font-bold">৳{{ number_format($addon->price, 0) }}</div>
                        </div>
                    </div>
                    <div class="addon-quantity {{ in_array($addon->id, $selectedAddons) ? '' : 'hidden' }}" id="quantity-{{ $addon->id }}">
                        <label class="text-xs">পরিমাণ:</label>
                        <input type="number" name="addon_quantities[{{ $addon->id }}]" 
                            value="{{ $addonQuantities[$addon->id] ?? 1 }}" min="1"
                            class="w-20 px-2 py-1 border rounded text-sm" onchange="updateAddonsCost()">
                    </div>
                </div>
                @endforeach
            </div>
            <input type="hidden" name="addons_cost" id="addonsCost" value="{{ $conventionBooking->addons_cost }}">
        </div>

        <!-- Discount & VAT -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-indigo-600 flex items-center">
                <i class="fas fa-calculator mr-3"></i>ছাড় ও ভ্যাট
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-bold mb-3">ছাড় (Discount)</h3>
                    <div class="flex gap-4 mb-3">
                        <label class="flex items-center">
                            <input type="radio" name="discount_type" value="flat" {{ $conventionBooking->discount_type == 'flat' ? 'checked' : '' }} onclick="calculateTotal()" class="mr-2">
                            <span>ফ্ল্যাট</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="discount_type" value="percentage" {{ $conventionBooking->discount_type == 'percentage' ? 'checked' : '' }} onclick="calculateTotal()" class="mr-2">
                            <span>পার্সেন্টেজ</span>
                        </label>
                    </div>
                    <input type="number" name="discount_value" id="discountValue" value="{{ $conventionBooking->discount_value }}" step="0.01"
                        class="w-full px-4 py-3 border rounded-lg" onchange="calculateTotal()"
                        placeholder="ছাড়ের পরিমাণ">
                    <input type="hidden" name="discount" id="discountAmount" value="{{ $conventionBooking->discount }}">
                </div>

                <div>
                    <h3 class="text-lg font-bold mb-3">ভ্যাট (VAT)</h3>
                    <label class="flex items-center mb-3">
                        <input type="checkbox" id="vatEnabled" {{ $conventionBooking->vat_amount > 0 ? 'checked' : '' }} onchange="calculateTotal()" class="mr-2">
                        <span>ভ্যাট যোগ করুন</span>
                    </label>
                    <div id="vatSection" class="{{ $conventionBooking->vat_amount > 0 ? '' : 'hidden' }}">
                        <label class="block mb-2">ভ্যাট পার্সেন্টেজ (%)</label>
                        <input type="number" name="vat_percentage" id="vatPercentage" value="{{ $conventionBooking->vat_percentage }}" step="0.01"
                            class="w-full px-4 py-3 border rounded-lg" onchange="calculateTotal()">
                    </div>
                    <input type="hidden" name="vat_amount" id="vatAmount" value="{{ $conventionBooking->vat_amount }}">
                </div>
            </div>
        </div>

        <!-- Summary & Payments -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-green-600 flex items-center">
                <i class="fas fa-money-bill-wave mr-3"></i>পেমেন্ট সারসংক্ষেপ
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-4">খরচ বিবরণ</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span>হল ভাড়া:</span>
                            <span class="font-semibold" id="displayHallRent">৳{{ number_format($conventionBooking->hall_rent, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>খাবার খরচ:</span>
                            <span class="font-semibold" id="displayFoodCost">৳{{ number_format($conventionBooking->food_cost, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>অ্যাডঅন খরচ:</span>
                            <span class="font-semibold" id="displayAddonsCost">৳{{ number_format($conventionBooking->addons_cost, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-red-600">
                            <span>ছাড়:</span>
                            <span class="font-semibold" id="displayDiscount">-৳{{ number_format($conventionBooking->discount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>ভ্যাট:</span>
                            <span class="font-semibold" id="displayVat">৳{{ number_format($conventionBooking->vat_amount, 2) }}</span>
                        </div>
                        <div class="border-t pt-3">
                            <div class="flex justify-between text-lg font-bold text-indigo-600">
                                <span>মোট টাকা:</span>
                                <span id="displayTotal">৳{{ number_format($conventionBooking->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="total_amount" id="totalAmount" value="{{ $conventionBooking->total_amount }}">
                </div>

                <div class="bg-blue-50 p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-4">পেমেন্ট স্ট্যাটাস</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span>মোট টাকা:</span>
                            <span class="font-semibold">৳{{ number_format($conventionBooking->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-green-600">
                            <span>পরিশোধিত:</span>
                            <span class="font-semibold">৳{{ number_format($conventionBooking->advance_payment, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-red-600 text-lg font-bold">
                            <span>বাকি:</span>
                            <span>৳{{ number_format($conventionBooking->remaining_payment, 2) }}</span>
                        </div>
                        <div class="mt-4">
                            <span class="px-4 py-2 rounded-full text-sm font-bold
                                @if($conventionBooking->payment_status == 'paid') bg-green-100 text-green-800
                                @elseif($conventionBooking->payment_status == 'partial') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $conventionBooking->payment_status == 'paid' ? '✅ সম্পূর্ণ পরিশোধিত' : ($conventionBooking->payment_status == 'partial' ? '🟡 আংশিক পরিশোধিত' : '❌ অপরিশোধিত') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
            <h2 class="text-2xl font-bold mb-4 text-gray-700 flex items-center">
                <i class="fas fa-sticky-note mr-3"></i>নোট
            </h2>
            <textarea name="notes" rows="3"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600"
                placeholder="বিশেষ নোট বা মন্তব্য লিখুন...">{{ $conventionBooking->notes }}</textarea>
        </div>

        <input type="hidden" name="payment_method" value="{{ $conventionBooking->payment_method }}">
        <input type="hidden" name="advance_payment" value="{{ $conventionBooking->advance_payment }}">

        <div class="flex gap-4">
            <a href="{{ route('admin.convention-bookings.index') }}" 
                class="flex-1 bg-gray-500 text-white px-8 py-4 rounded-lg hover:bg-gray-600 transition font-semibold text-lg text-center">
                <i class="fas fa-times mr-2"></i>বাতিল
            </a>
            <button type="submit" 
                class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white px-8 py-4 rounded-lg hover:from-green-700 hover:to-green-800 transition font-semibold text-lg">
                <i class="fas fa-save mr-2"></i>পরিবর্তন সংরক্ষণ করুন
            </button>
        </div>
    </form>
</div>

<script>
let selectedFoodPrice = {{ $conventionBooking->food_package_id && $conventionBooking->foodPackage ? $conventionBooking->foodPackage->price_per_person : 0 }};

function updateHallRent() {
    const select = document.getElementById('hallId');
    const selectedOption = select.options[select.selectedIndex];
    const price = parseFloat(selectedOption.dataset.price) || 0;
    document.getElementById('hallRent').value = price;
    document.getElementById('displayHallRent').textContent = '৳' + price.toFixed(2);
    calculateTotal();
}

function selectFoodPackage(id, name, pricePerPerson) {
    selectedFoodPrice = pricePerPerson;
    document.querySelectorAll('[name="food_package_id"]').forEach(r => r.checked = false);
    if (id > 0) {
        document.querySelector(`[name="food_package_id"][value="${id}"]`).checked = true;
    } else {
        document.querySelector(`[name="food_package_id"][value=""]`).checked = true;
    }
    updateFoodCost();
}

function updateFoodCost() {
    const guests = parseInt(document.getElementById('numberOfGuests').value) || 0;
    const foodCost = guests * selectedFoodPrice;
    document.getElementById('foodCost').value = foodCost;
    document.getElementById('displayFoodCost').textContent = '৳' + foodCost.toFixed(2);
    calculateTotal();
}

function toggleAddonQuantity(addonId, isChecked) {
    const quantityDiv = document.getElementById(`quantity-${addonId}`);
    if (isChecked) {
        quantityDiv.classList.remove('hidden');
    } else {
        quantityDiv.classList.add('hidden');
    }
    updateAddonsCost();
}

function updateAddonsCost() {
    let total = 0;
    document.querySelectorAll('[name="selected_addons[]"]:checked').forEach(checkbox => {
        const price = parseFloat(checkbox.dataset.price) || 0;
        const addonId = checkbox.value;
        const quantityInput = document.querySelector(`[name="addon_quantities[${addonId}]"]`);
        const quantity = quantityInput ? parseInt(quantityInput.value) || 1 : 1;
        total += price * quantity;
    });
    document.getElementById('addonsCost').value = total;
    document.getElementById('displayAddonsCost').textContent = '৳' + total.toFixed(2);
    calculateTotal();
}

function filterAddons(category) {
    const addons = document.querySelectorAll('.addon-item');
    addons.forEach(addon => {
        if (category === 'all' || addon.dataset.category === category) {
            addon.classList.remove('hidden');
        } else {
            addon.classList.add('hidden');
        }
    });
}

function calculateTotal() {
    const hallRent = parseFloat(document.getElementById('hallRent').value) || 0;
    const foodCost = parseFloat(document.getElementById('foodCost').value) || 0;
    const addonsCost = parseFloat(document.getElementById('addonsCost').value) || 0;
    
    const subtotal = hallRent + foodCost + addonsCost;
    
    // Discount
    const discountType = document.querySelector('[name="discount_type"]:checked').value;
    const discountValue = parseFloat(document.getElementById('discountValue').value) || 0;
    let discount = 0;
    if (discountType === 'percentage') {
        discount = (subtotal * discountValue) / 100;
    } else {
        discount = discountValue;
    }
    document.getElementById('discountAmount').value = discount;
    document.getElementById('displayDiscount').textContent = '-৳' + discount.toFixed(2);
    
    const afterDiscount = subtotal - discount;
    
    // VAT
    const vatEnabled = document.getElementById('vatEnabled').checked;
    document.getElementById('vatSection').classList.toggle('hidden', !vatEnabled);
    let vatAmount = 0;
    if (vatEnabled) {
        const vatPercentage = parseFloat(document.getElementById('vatPercentage').value) || 0;
        vatAmount = (afterDiscount * vatPercentage) / 100;
    }
    document.getElementById('vatAmount').value = vatAmount;
    document.getElementById('displayVat').textContent = '৳' + vatAmount.toFixed(2);
    
    // Total
    const total = afterDiscount + vatAmount;
    document.getElementById('totalAmount').value = total;
    document.getElementById('displayTotal').textContent = '৳' + total.toFixed(2);
    
    // Update display values
    document.getElementById('displayHallRent').textContent = '৳' + hallRent.toFixed(2);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateAddonsCost();
    calculateTotal();
});
</script>
@endsection
