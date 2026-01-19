@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">প্রিমিয়াম কনভেনশন বুকিং</h1>
        <p class="text-gray-600 mt-2">উন্নত কনভেনশন হল বুকিং সিস্টেম</p>
    </div>

    <!-- Search Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">হল খোঁজ করুন</h2>
        <form id="searchForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">তারিখ</label>
                    <input type="date" id="eventDate" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">সময়</label>
                    <select id="timeSlot" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">সময় নির্বাচন করুন</option>
                        <option value="morning">সকাল (৮টা - ১২টা)</option>
                        <option value="afternoon">দুপুর (১২টা - ৫টা)</option>
                        <option value="evening">সন্ধ্যা (৫টা - ১০টা)</option>
                        <option value="full_day">সারাদিন</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="mt-4 bg-gradient-to-r from-green-600 to-green-700 text-white px-8 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition shadow-lg">
                <i class="fas fa-search mr-2"></i>খোঁজ করুন
            </button>
        </form>
    </div>

    <!-- Results Section -->
    <div id="resultsSection" class="hidden">
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">উপলব্ধ হল</h2>
            <div id="availableHalls" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>
        </div>
    </div>

    <!-- Booking Form -->
    <div id="bookingForm" class="hidden bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">বুকিং তথ্য</h2>
        <form id="bookForm">
            <input type="hidden" id="selectedHallId">
            <input type="hidden" id="bookEventDate">
            <input type="hidden" id="bookTimeSlot">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">গ্রাহকের নাম *</label>
                    <input type="text" id="customerName" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ফোন নম্বর *</label>
                    <input type="tel" id="customerPhone" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ইমেইল</label>
                    <input type="email" id="customerEmail" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">অতিথি সংখ্যা *</label>
                    <input type="number" id="numberOfGuests" min="1" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ইভেন্টের ধরন</label>
                    <input type="text" id="eventType" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="বিয়ে, কনফারেন্স, ইত্যাদি">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">বিশেষ অনুরোধ</label>
                    <textarea id="specialRequests" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">মোট টাকা (৳) *</label>
                    <input type="number" id="totalAmount" step="0.01" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">অগ্রিম পেমেন্ট (৳)</label>
                    <input type="number" id="advancePayment" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
            </div>

            <button type="submit" class="mt-6 bg-gradient-to-r from-green-600 to-green-700 text-white px-8 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition shadow-lg">
                <i class="fas fa-check mr-2"></i>বুকিং নিশ্চিত করুন
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('searchForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const date = document.getElementById('eventDate').value;
    const slot = document.getElementById('timeSlot').value;

    const response = await fetch('{{ route("admin.premium-convention.search") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({date, slot})
    });

    const data = await response.json();
    const container = document.getElementById('availableHalls');
    container.innerHTML = '';

    if(data.availableHalls.length === 0) {
        container.innerHTML = '<p class="text-gray-500 col-span-3 text-center py-8">কোনো হল উপলব্ধ নেই</p>';
    } else {
        data.availableHalls.forEach(hall => {
            container.innerHTML += `
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition">
                    <h3 class="font-bold text-lg">${hall.name}</h3>
                    <p class="text-gray-600">ধারণক্ষমতা: ${hall.capacity} জন</p>
                    <p class="text-green-600 font-semibold">৳${hall.price_per_day || 0}</p>
                    <button onclick="selectHall(${hall.id}, ${hall.price_per_day || 0})" 
                        class="mt-3 w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        নির্বাচন করুন
                    </button>
                </div>
            `;
        });
    }
    
    document.getElementById('resultsSection').classList.remove('hidden');
    document.getElementById('bookEventDate').value = date;
    document.getElementById('bookTimeSlot').value = slot;
});

function selectHall(hallId, price) {
    document.getElementById('selectedHallId').value = hallId;
    document.getElementById('totalAmount').value = price;
    document.getElementById('bookingForm').classList.remove('hidden');
    document.getElementById('bookingForm').scrollIntoView({behavior: 'smooth'});
}

document.getElementById('bookForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = {
        convention_hall_id: document.getElementById('selectedHallId').value,
        event_date: document.getElementById('bookEventDate').value,
        time_slot: document.getElementById('bookTimeSlot').value,
        customer_name: document.getElementById('customerName').value,
        customer_phone: document.getElementById('customerPhone').value,
        customer_email: document.getElementById('customerEmail').value,
        number_of_guests: document.getElementById('numberOfGuests').value,
        event_type: document.getElementById('eventType').value,
        special_requests: document.getElementById('specialRequests').value,
        total_amount: document.getElementById('totalAmount').value,
        advance_payment: document.getElementById('advancePayment').value,
    };

    const response = await fetch('{{ route("admin.premium-convention.book") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify(formData)
    });

    const data = await response.json();
    if(data.success) {
        alert('বুকিং সফল হয়েছে!');
        window.location.href = '{{ route("admin.convention-bookings.index") }}';
    }
});
</script>
@endsection
