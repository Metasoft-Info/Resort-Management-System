@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">🎪 প্রিমিয়াম কনভেনশন বুকিং</h1>
        <p class="text-gray-600 mt-2">সম্পূর্ণ কনভেনশন হল বুকিং - খাবার প্যাকেজ ও অ্যাডঅন সার্ভিস সহ</p>
    </div>

    <!-- Progress Steps -->
    <div class="flex justify-between mb-8 max-w-4xl mx-auto">
        <div class="flex items-center flex-1">
            <div class="flex flex-col items-center flex-1">
                <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg bg-purple-600 text-white" id="step1-circle">1</div>
                <span class="text-sm mt-2 font-semibold">হল ও ইভেন্ট</span>
            </div>
        </div>
        <div class="flex items-center flex-1">
            <div class="flex flex-col items-center flex-1">
                <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg bg-gray-300 text-gray-600" id="step2-circle">2</div>
                <span class="text-sm mt-2 font-semibold">খাবার ও সার্ভিস</span>
            </div>
        </div>
        <div class="flex items-center flex-1">
            <div class="flex flex-col items-center flex-1">
                <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg bg-gray-300 text-gray-600" id="step3-circle">3</div>
                <span class="text-sm mt-2 font-semibold">পেমেন্ট</span>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.convention-bookings.store') }}" method="POST" id="bookingForm" class="max-w-6xl mx-auto">
        @csrf
        
        <!-- Step 1: Hall & Event Details -->
        <div id="step1" class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-bold mb-6 text-purple-600 flex items-center">
                <i class="fas fa-building mr-3"></i>হল ও ইভেন্ট বিবরণ
            </h2>
            
            <!-- Date & Time First -->
            <div class="bg-purple-50 border-2 border-purple-300 rounded-xl p-6 mb-6">
                <h3 class="text-lg font-bold text-purple-800 mb-4 flex items-center">
                    <i class="fas fa-calendar mr-2"></i>প্রথমে তারিখ ও সময় নির্বাচন করুন
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">ইভেন্টের তারিখ *</label>
                        <input type="date" id="eventDate" name="event_date" required min="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-3 border-2 border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-600"
                            onchange="checkAvailability()">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">সময় স্লট *</label>
                        <select id="timeSlot" name="time_slot" required
                            class="w-full px-4 py-3 border-2 border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-600"
                            onchange="checkAvailability()">
                            <option value="">সময় নির্বাচন করুন</option>
                            <option value="morning">🌅 সকাল (৮টা - ১২টা)</option>
                            <option value="afternoon">☀️ দুপুর (১২টা - ৫টা)</option>
                            <option value="evening">🌙 সন্ধ্যা (৫টা - ১০টা)</option>
                            <option value="fullday">🌞 সারাদিন</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Available Halls -->
            <div id="hallsContainer" class="hidden mb-6">
                <label class="block text-gray-700 font-semibold mb-4">
                    উপলব্ধ কনভেনশন হল *
                    <span class="ml-2 text-sm text-green-600 font-normal">✅ নির্বাচিত তারিখ ও সময়ের জন্য উপলব্ধ হল দেখাচ্ছে</span>
                </label>
                <div id="hallsList" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
                <input type="hidden" name="hall_id" id="selectedHallId">
                <input type="hidden" name="hall_rent" id="hallRent" value="0">
            </div>

            <!-- Customer Info -->
            <div class="space-y-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        📱 ফোন নম্বর *
                        <span id="customerFound" class="ml-2 text-green-600 text-sm font-bold hidden">✅ গ্রাহক খুঁজে পাওয়া গেছে!</span>
                    </label>
                    <input type="tel" name="customer_phone" id="customerPhone" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600"
                        placeholder="ফোন নম্বর লিখুন (বিদ্যমান গ্রাহক হলে তথ্য স্বয়ংক্রিয়ভাবে পূরণ হবে)"
                        onblur="searchCustomer(this.value)">
                    <p class="text-sm text-gray-500 mt-1">💡 ফোন নম্বর লিখে Tab চাপুন - গ্রাহক থাকলে তথ্য স্বয়ংক্রিয়ভাবে পূরণ হবে</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">গ্রাহকের নাম *</label>
                        <input type="text" name="customer_name" id="customerName" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">প্রতিষ্ঠানের নাম</label>
                        <input type="text" name="organization_name" id="organizationName"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">ইমেইল</label>
                        <input type="email" name="customer_email" id="customerEmail"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">হোয়াটসঅ্যাপ</label>
                        <input type="tel" name="customer_whatsapp" id="customerWhatsapp"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">এনআইডি নম্বর</label>
                        <input type="text" name="customer_nid" id="customerNid"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">ইভেন্টের ধরন *</label>
                        <select name="event_type" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600">
                            <option value="conference">কনফারেন্স</option>
                            <option value="wedding">বিয়ে</option>
                            <option value="meeting">মিটিং</option>
                            <option value="seminar">সেমিনার</option>
                            <option value="party">পার্টি</option>
                            <option value="other">অন্যান্য</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">অতিথি সংখ্যা *</label>
                        <input type="number" name="number_of_guests" id="numberOfGuests" min="1" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600"
                            onchange="updateFoodCost()">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-semibold mb-2">ঠিকানা</label>
                        <textarea name="customer_address" id="customerAddress" rows="2"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600"></textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-8">
                <button type="button" onclick="nextStep(2)" class="bg-purple-600 text-white px-8 py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                    পরবর্তী ধাপ <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 2: Food & Addon Services -->
        <div id="step2" class="bg-white rounded-2xl shadow-xl p-8 hidden">
            <h2 class="text-2xl font-bold mb-6 text-orange-600 flex items-center">
                <i class="fas fa-utensils mr-3"></i>খাবার ও অ্যাডঅন সার্ভিস
            </h2>

            <!-- Food Packages -->
            <div class="mb-8">
                <h3 class="text-xl font-bold mb-4">খাবার প্যাকেজ</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="border-2 border-gray-300 rounded-lg p-4 cursor-pointer hover:border-orange-500 transition"
                        onclick="selectFoodPackage(0, 'নিজস্ব', 0)">
                        <input type="radio" name="selected_food_package_id" value="" class="mr-3">
                        <span class="font-semibold">নিজস্ব খাবার</span>
                        <p class="text-sm text-gray-600">নিজস্ব খাবার ব্যবস্থা</p>
                    </div>
                    @foreach($foodPackages as $package)
                    <div class="border-2 border-gray-300 rounded-lg p-4 cursor-pointer hover:border-orange-500 transition"
                        onclick="selectFoodPackage({{ $package->id }}, '{{ $package->name }}', {{ $package->price_per_person }})">
                        <input type="radio" name="selected_food_package_id" value="{{ $package->id }}" class="mr-3">
                        <div>
                            <div class="font-semibold">{{ $package->name }}</div>
                            <div class="text-orange-600 font-bold">৳{{ number_format($package->price_per_person, 0) }}/person</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <input type="hidden" name="food_cost" id="foodCost" value="0">
            </div>

            <!-- Addon Services -->
            <div class="mb-8">
                <h3 class="text-xl font-bold mb-4">অ্যাডঅন সার্ভিস</h3>
                <div class="flex flex-wrap gap-2 mb-4">
                    <button type="button" class="px-4 py-2 rounded-lg bg-purple-600 text-white" onclick="filterAddons('all')">সব</button>
                    <button type="button" class="px-4 py-2 rounded-lg bg-gray-200" onclick="filterAddons('decoration')">সাজসজ্জা</button>
                    <button type="button" class="px-4 py-2 rounded-lg bg-gray-200" onclick="filterAddons('sound_system')">সাউন্ড</button>
                    <button type="button" class="px-4 py-2 rounded-lg bg-gray-200" onclick="filterAddons('photography')">ফটোগ্রাফি</button>
                    <button type="button" class="px-4 py-2 rounded-lg bg-gray-200" onclick="filterAddons('catering')">ক্যাটারিং</button>
                    <button type="button" class="px-4 py-2 rounded-lg bg-gray-200" onclick="filterAddons('transport')">পরিবহন</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="addonsList">
                    @foreach($addonServices as $addon)
                    <div class="border-2 border-gray-200 rounded-lg p-4 addon-item" data-category="{{ $addon->category }}">
                        <div class="flex items-start mb-2">
                            <input type="checkbox" name="selected_addons[]" value="{{ $addon->id }}" class="mr-3 w-5 h-5"
                                data-price="{{ $addon->price }}" onchange="toggleAddonQuantity({{ $addon->id }}, this.checked)">
                            <div class="flex-1">
                                <div class="font-semibold">{{ $addon->name }}</div>
                                <div class="text-purple-600 font-bold">৳{{ number_format($addon->price, 0) }}</div>
                            </div>
                        </div>
                        <div class="addon-quantity hidden" id="quantity-{{ $addon->id }}">
                            <label class="text-xs">পরিমাণ:</label>
                            <input type="number" name="addon_quantities[{{ $addon->id }}]" value="1" min="1"
                                class="w-20 px-2 py-1 border rounded text-sm" onchange="updateAddonsCost()">
                        </div>
                    </div>
                    @endforeach
                </div>
                <input type="hidden" name="addons_cost" id="addonsCost" value="0">
            </div>

            <div class="flex justify-between mt-8">
                <button type="button" onclick="nextStep(1)" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition font-semibold">
                    <i class="fas fa-arrow-left mr-2"></i>পূর্ববর্তী
                </button>
                <button type="button" onclick="nextStep(3)" class="bg-purple-600 text-white px-8 py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                    পরবর্তী ধাপ <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 3: Payment & Summary -->
        <div id="step3" class="bg-white rounded-2xl shadow-xl p-8 hidden">
            <h2 class="text-2xl font-bold mb-6 text-indigo-600 flex items-center">
                <i class="fas fa-calculator mr-3"></i>পেমেন্ট ও সারসংক্ষেপ
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left: Pricing Details -->
                <div class="space-y-6">
                    <!-- Discount -->
                    <div>
                        <h3 class="text-lg font-bold mb-3">ছাড় (Discount)</h3>
                        <div class="flex gap-4 mb-3">
                            <label class="flex items-center">
                                <input type="radio" name="discount_type" value="flat" checked onclick="calculateTotal()" class="mr-2">
                                <span>ফ্ল্যাট</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="discount_type" value="percentage" onclick="calculateTotal()" class="mr-2">
                                <span>পার্সেন্টেজ</span>
                            </label>
                        </div>
                        <input type="number" name="discount_value" id="discountValue" value="0" step="0.01"
                            class="w-full px-4 py-3 border rounded-lg" onchange="calculateTotal()"
                            placeholder="ছাড়ের পরিমাণ লিখুন">
                        <input type="hidden" name="discount" id="discountAmount" value="0">
                    </div>

                    <!-- VAT -->
                    <div>
                        <h3 class="text-lg font-bold mb-3">ভ্যাট (VAT)</h3>
                        <label class="flex items-center mb-3">
                            <input type="checkbox" id="vatEnabled" onchange="calculateTotal()" class="mr-2">
                            <span>ভ্যাট যোগ করুন</span>
                        </label>
                        <div id="vatSection" class="hidden">
                            <label class="block mb-2">ভ্যাট পার্সেন্টেজ (%)</label>
                            <input type="number" name="vat_percentage" id="vatPercentage" value="15" step="0.01"
                                class="w-full px-4 py-3 border rounded-lg" onchange="calculateTotal()">
                        </div>
                        <input type="hidden" name="vat_amount" id="vatAmount" value="0">
                    </div>

                    <!-- Advance Payment -->
                    <div>
                        <h3 class="text-lg font-bold mb-3">অগ্রিম পেমেন্ট</h3>
                        <input type="number" name="advance_payment" id="advancePayment" value="0" step="0.01"
                            class="w-full px-4 py-3 border rounded-lg" onchange="calculateTotal()">
                    </div>

                    <div>
                        <h3 class="text-lg font-bold mb-3">পেমেন্ট পদ্ধতি</h3>
                        <select name="payment_method" class="w-full px-4 py-3 border rounded-lg">
                            <option value="cash">ক্যাশ</option>
                            <option value="card">কার্ড</option>
                            <option value="mfs">মোবাইল ব্যাংকিং</option>
                        </select>
                    </div>
                </div>

                <!-- Right: Summary -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-4">বুকিং সারসংক্ষেপ</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span>হল ভাড়া:</span>
                            <span class="font-semibold" id="displayHallRent">৳0</span>
                        </div>
                        <div class="flex justify-between">
                            <span>খাবার খরচ:</span>
                            <span class="font-semibold" id="displayFoodCost">৳0</span>
                        </div>
                        <div class="flex justify-between">
                            <span>অ্যাডঅন খরচ:</span>
                            <span class="font-semibold" id="displayAddonsCost">৳0</span>
                        </div>
                        <div class="flex justify-between text-red-600">
                            <span>ছাড়:</span>
                            <span class="font-semibold" id="displayDiscount">-৳0</span>
                        </div>
                        <div class="flex justify-between">
                            <span>ভ্যাট:</span>
                            <span class="font-semibold" id="displayVat">৳0</span>
                        </div>
                        <div class="border-t pt-3">
                            <div class="flex justify-between text-lg font-bold text-indigo-600">
                                <span>মোট টাকা:</span>
                                <span id="displayTotal">৳0</span>
                            </div>
                        </div>
                        <div class="flex justify-between text-green-600">
                            <span>অগ্রিম পেমেন্ট:</span>
                            <span class="font-semibold" id="displayAdvance">৳0</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-red-600">
                            <span>বাকি:</span>
                            <span id="displayRemaining">৳0</span>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="total_amount" id="totalAmount" value="0">
            <input type="hidden" name="status" value="confirmed">

            <div class="flex justify-between mt-8">
                <button type="button" onclick="nextStep(2)" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition font-semibold">
                    <i class="fas fa-arrow-left mr-2"></i>পূর্ববর্তী
                </button>
                <button type="submit" class="bg-gradient-to-r from-green-600 to-green-700 text-white px-8 py-4 rounded-lg hover:from-green-700 hover:to-green-800 transition font-semibold text-lg">
                    <i class="fas fa-check mr-2"></i>বুকিং নিশ্চিত করুন
                </button>
            </div>
        </div>
    </form>
</div>

<script>
let selectedFoodPrice = 0;

function nextStep(step) {
    // Hide all steps
    document.getElementById('step1').classList.add('hidden');
    document.getElementById('step2').classList.add('hidden');
    document.getElementById('step3').classList.add('hidden');
    
    // Reset step circles
    document.getElementById('step1-circle').classList.remove('bg-purple-600', 'text-white');
    document.getElementById('step1-circle').classList.add('bg-gray-300', 'text-gray-600');
    document.getElementById('step2-circle').classList.remove('bg-purple-600', 'text-white');
    document.getElementById('step2-circle').classList.add('bg-gray-300', 'text-gray-600');
    document.getElementById('step3-circle').classList.remove('bg-purple-600', 'text-white');
    document.getElementById('step3-circle').classList.add('bg-gray-300', 'text-gray-600');
    
    // Show selected step
    document.getElementById('step' + step).classList.remove('hidden');
    document.getElementById('step' + step + '-circle').classList.remove('bg-gray-300', 'text-gray-600');
    document.getElementById('step' + step + '-circle').classList.add('bg-purple-600', 'text-white');
    
    if (step === 3) {
        calculateTotal();
    }
    window.scrollTo(0, 0);
}

async function checkAvailability() {
    const date = document.getElementById('eventDate').value;
    const slot = document.getElementById('timeSlot').value;
    
    if (!date || !slot) return;
    
    try {
        const response = await fetch('{{ route("admin.premium-convention.search") }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify({date, slot})
        });
        
        const data = await response.json();
        const container = document.getElementById('hallsList');
        container.innerHTML = '';
        
        if (data.availableHalls.length === 0) {
            container.innerHTML = '<div class="col-span-2 bg-red-50 border-2 border-red-300 rounded-lg p-6 text-center"><p class="text-red-800 font-semibold">❌ এই তারিখ ও সময়ের জন্য কোনো হল উপলব্ধ নেই</p></div>';
        } else {
            data.availableHalls.forEach(hall => {
                container.innerHTML += `
                    <div class="border-2 border-gray-300 rounded-lg p-4 cursor-pointer hover:border-purple-400 transition hall-card"
                        data-hall-id="${hall.id}" data-price="${hall.price_per_day}"
                        onclick="selectHall(${hall.id}, ${hall.price_per_day}, '${slot}')">
                        <h4 class="font-bold text-lg">${hall.name}</h4>
                        <p class="text-sm text-gray-600">ধারণক্ষমতা: ${hall.capacity} জন</p>
                        <p class="text-lg font-bold text-purple-600">৳${hall.price_per_day.toLocaleString()}/day</p>
                        <span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full">✅ উপলব্ধ</span>
                    </div>
                `;
            });
        }
        
        document.getElementById('hallsContainer').classList.remove('hidden');
    } catch (error) {
        console.error('Error:', error);
    }
}

function selectHall(hallId, price, timeSlot) {
    document.querySelectorAll('.hall-card').forEach(card => {
        card.classList.remove('border-purple-600', 'bg-purple-50');
        card.classList.add('border-gray-300');
    });
    
    event.target.closest('.hall-card').classList.remove('border-gray-300');
    event.target.closest('.hall-card').classList.add('border-purple-600', 'bg-purple-50');
    
    let finalPrice = price;
    if (timeSlot === 'morning' || timeSlot === 'afternoon' || timeSlot === 'evening') {
        finalPrice = price * 0.4;
    }
    
    document.getElementById('selectedHallId').value = hallId;
    document.getElementById('hallRent').value = finalPrice;
    document.getElementById('displayHallRent').textContent = '৳' + finalPrice.toFixed(2);
}

async function searchCustomer(phone) {
    if (!phone || phone.length < 10) return;
    
    try {
        const response = await fetch(`{{ url('/admin/convention-bookings/customer') }}/${phone}`);
        if (response.ok) {
            const data = await response.json();
            document.getElementById('customerName').value = data.customerName || '';
            document.getElementById('customerEmail').value = data.customerEmail || '';
            document.getElementById('customerWhatsapp').value = data.customerWhatsapp || '';
            document.getElementById('customerNid').value = data.customerNid || '';
            document.getElementById('customerAddress').value = data.customerAddress || '';
            document.getElementById('organizationName').value = data.organizationName || '';
            document.getElementById('customerFound').classList.remove('hidden');
        }
    } catch (error) {
        console.log('Customer not found');
    }
}

function selectFoodPackage(id, name, pricePerPerson) {
    selectedFoodPrice = pricePerPerson;
    document.querySelectorAll('[name="selected_food_package_id"]').forEach(r => r.checked = false);
    if (id > 0) {
        document.querySelector(`[name="selected_food_package_id"][value="${id}"]`).checked = true;
    } else {
        document.querySelector(`[name="selected_food_package_id"][value=""]`).checked = true;
    }
    updateFoodCost();
}

function updateFoodCost() {
    const guests = parseInt(document.getElementById('numberOfGuests').value) || 0;
    const foodCost = guests * selectedFoodPrice;
    document.getElementById('foodCost').value = foodCost;
    document.getElementById('displayFoodCost').textContent = '৳' + foodCost.toFixed(2);
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
    
    // Remaining
    const advance = parseFloat(document.getElementById('advancePayment').value) || 0;
    document.getElementById('displayAdvance').textContent = '৳' + advance.toFixed(2);
    const remaining = Math.max(0, total - advance);
    document.getElementById('displayRemaining').textContent = '৳' + remaining.toFixed(2);
}
</script>
@endsection
