@extends('layouts.admin')
@section('content')
<div class="p-6 max-w-7xl mx-auto print:p-0">
    <!-- Header -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-600 rounded-2xl p-8 mb-6 text-white print:hidden">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">কনভেনশন বুকিং #{{ $booking->id }}</h1>
                <p class="text-primary-50">সম্পূর্ণ বুকিং বিবরণ ও ম্যানেজমেন্ট</p>
            </div>
            <div class="flex gap-3">
                <button onclick="window.print()" class="px-6 py-3 bg-green-500 text-white rounded-lg font-bold hover:bg-green-600 transition">
                    <i class="fas fa-print mr-2"></i>ইনভয়েস প্রিন্ট
                </button>
                <a href="{{ route('admin.convention-bookings.edit', $booking) }}" class="px-6 py-3 bg-white text-primary-600 rounded-lg font-bold hover:bg-primary-50 transition">
                    <i class="fas fa-edit mr-2"></i>এডিট
                </a>
                <a href="{{ route('admin.convention-bookings.index') }}" class="px-6 py-3 bg-white/20 text-white rounded-lg font-bold hover:bg-white/30 transition">
                    <i class="fas fa-arrow-left mr-2"></i>ফিরে যান
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Customer Information -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-primary-600 mb-4 flex items-center gap-2">
                    <i class="fas fa-user"></i> গ্রাহক তথ্য
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">নাম</p>
                        <p class="font-semibold">{{ $booking->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">ফোন</p>
                        <p class="font-semibold">{{ $booking->customer_phone }}</p>
                    </div>
                    @if($booking->customer_whatsapp)
                    <div>
                        <p class="text-sm text-gray-600">হোয়াটসঅ্যাপ</p>
                        <p class="font-semibold">{{ $booking->customer_whatsapp }}</p>
                    </div>
                    @endif
                    @if($booking->customer_email)
                    <div>
                        <p class="text-sm text-gray-600">ইমেইল</p>
                        <p class="font-semibold">{{ $booking->customer_email }}</p>
                    </div>
                    @endif
                    @if($booking->customer_nid)
                    <div>
                        <p class="text-sm text-gray-600">এনআইডি</p>
                        <p class="font-semibold">{{ $booking->customer_nid }}</p>
                    </div>
                    @endif
                    @if($booking->organization_name)
                    <div>
                        <p class="text-sm text-gray-600">প্রতিষ্ঠান</p>
                        <p class="font-semibold">{{ $booking->organization_name }}</p>
                    </div>
                    @endif
                    @if($booking->customer_address)
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600">ঠিকানা</p>
                        <p class="font-semibold">{{ $booking->customer_address }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Event Details -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-primary-600 mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar-alt"></i> ইভেন্ট বিবরণ
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">হলের নাম</p>
                        <p class="font-semibold text-lg">{{ $booking->conventionHall->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">তারিখ</p>
                        <p class="font-semibold">{{ \Carbon\Carbon::parse($booking->event_date)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">সময়</p>
                        <p class="font-semibold">
                            @if($booking->time_slot == 'morning') সকাল (৮টা - ২টা)
                            @elseif($booking->time_slot == 'night') রাত (৬টা - ১১টা)
                            @else সারাদিন (৮টা - ১১টা)
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">ইভেন্টের ধরন</p>
                        <p class="font-semibold">{{ $booking->event_type }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">অতিথি সংখ্যা</p>
                        <p class="font-semibold">{{ $booking->number_of_guests }} জন</p>
                    </div>
                    @if($booking->event_description)
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600">বিবরণ</p>
                        <p class="font-semibold">{{ $booking->event_description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Food Package & Addons -->
            @if($booking->foodPackage || $booking->selected_addons)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-primary-600 mb-4 flex items-center gap-2">
                    <i class="fas fa-utensils"></i> খাবার ও অ্যাডঅন সার্ভিস
                </h2>
                
                @if($booking->foodPackage)
                <div class="mb-4 pb-4 border-b">
                    <h3 class="font-bold text-lg mb-2">খাবার প্যাকেজ</h3>
                    <p class="text-gray-700">{{ $booking->foodPackage->name }}</p>
                    <p class="text-sm text-gray-600">৳{{ number_format($booking->food_cost, 2) }}</p>
                </div>
                @endif

                @if($booking->selected_addons)
                <div>
                    <h3 class="font-bold text-lg mb-2">অ্যাডঅন সার্ভিস</h3>
                    <div class="space-y-2">
                        @php
                            $addons = is_array($booking->selected_addons) ? $booking->selected_addons : json_decode($booking->selected_addons, true);
                            $quantities = is_array($booking->addon_quantities) ? $booking->addon_quantities : json_decode($booking->addon_quantities, true);
                        @endphp
                        @if($addons)
                            @foreach($addons as $addonId)
                                @php
                                    $addon = \App\Models\AddonService::find($addonId);
                                    $qty = $quantities[$addonId] ?? 1;
                                @endphp
                                @if($addon)
                                <div class="flex justify-between items-center bg-gray-50 p-3 rounded">
                                    <div>
                                        <p class="font-semibold">{{ $addon->name }}</p>
                                        <p class="text-sm text-gray-600">পরিমাণ: {{ $qty }}</p>
                                    </div>
                                    <p class="font-bold text-primary-600">৳{{ number_format($addon->price * $qty, 2) }}</p>
                                </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                    <div class="mt-3 pt-3 border-t">
                        <div class="flex justify-between font-bold">
                            <span>মোট অ্যাডঅন খরচ:</span>
                            <span class="text-primary-600">৳{{ number_format($booking->addons_cost, 2) }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Payment History -->
            @if($booking->payments && $booking->payments->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-primary-600 mb-4 flex items-center gap-2">
                    <i class="fas fa-history"></i> পেমেন্ট ইতিহাস
                </h2>
                <div class="space-y-3">
                    @foreach($booking->payments as $payment)
                    <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                        <div>
                            <p class="font-semibold">৳{{ number_format($payment->amount, 2) }}</p>
                            <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->payment_method }}</p>
                        </div>
                        @if($payment->notes)
                        <p class="text-sm text-gray-600 max-w-xs">{{ $payment->notes }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Payment & Status -->
        <div class="space-y-6">
            <!-- Status Cards -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">স্ট্যাটাস</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">বুকিং স্ট্যাটাস</p>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            @if($booking->status == 'confirmed') bg-primary-100 text-primary-800
                            @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($booking->status == 'completed') bg-primary-100 text-primary-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">পেমেন্ট স্ট্যাটাস</p>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            @if($booking->payment_status == 'paid') bg-primary-100 text-primary-800
                            @elseif($booking->payment_status == 'partial') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            @if($booking->payment_status == 'paid') সম্পূর্ণ পরিশোধিত
                            @elseif($booking->payment_status == 'partial') আংশিক পরিশোধিত
                            @else অপরিশোধিত
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">পেমেন্ট সারসংক্ষেপ</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">হল ভাড়া</span>
                        <span class="font-semibold">৳{{ number_format($booking->hall_rent, 2) }}</span>
                    </div>
                    @if($booking->food_cost > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">খাবার খরচ</span>
                        <span class="font-semibold">৳{{ number_format($booking->food_cost, 2) }}</span>
                    </div>
                    @endif
                    @if($booking->addons_cost > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">অ্যাডঅন খরচ</span>
                        <span class="font-semibold">৳{{ number_format($booking->addons_cost, 2) }}</span>
                    </div>
                    @endif
                    @if($booking->discount > 0)
                    <div class="flex justify-between text-red-600">
                        <span>ছাড়</span>
                        <span class="font-semibold">-৳{{ number_format($booking->discount, 2) }}</span>
                    </div>
                    @endif
                    @if($booking->vat_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">ভ্যাট ({{ $booking->vat_percentage }}%)</span>
                        <span class="font-semibold">৳{{ number_format($booking->vat_amount, 2) }}</span>
                    </div>
                    @endif
                    <div class="border-t pt-3">
                        <div class="flex justify-between text-lg font-bold text-primary-600">
                            <span>মোট টাকা</span>
                            <span>৳{{ number_format($booking->total_amount, 2) }}</span>
                        </div>
                    </div>
                    <div class="flex justify-between text-primary-600">
                        <span class="font-semibold">অগ্রিম পেমেন্ট</span>
                        <span class="font-semibold">৳{{ number_format($booking->advance_payment, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-red-600">
                        <span>বাকি পেমেন্ট</span>
                        <span>৳{{ number_format($booking->remaining_payment, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Add Payment Form -->
            @if($booking->remaining_payment > 0 && $booking->status != 'cancelled')
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">পেমেন্ট যোগ করুন</h2>
                <form action="{{ route('admin.convention-bookings.add-payment', $booking) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">পরিমাণ (৳)</label>
                            <input type="number" name="amount" step="0.01" max="{{ $booking->remaining_payment }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">পদ্ধতি</label>
                            <select name="method" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                <option value="cash">ক্যাশ</option>
                                <option value="card">কার্ড</option>
                                <option value="mfs">মোবাইল ব্যাংকিং</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">নোট</label>
                            <textarea name="note" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition font-semibold">
                            <i class="fas fa-plus mr-2"></i>পেমেন্ট যোগ করুন
                        </button>
                    </div>
                </form>
            </div>
            @endif

            @if($booking->notes)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">নোট</h2>
                <p class="text-gray-700">{{ $booking->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Print Invoice Template -->
    @include('admin.convention-bookings.invoice-template')
</div>
@endsection
