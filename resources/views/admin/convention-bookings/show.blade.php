@extends('layouts.admin')
@section('content')
<div class="p-6 max-w-7xl mx-auto print:p-0">
    <!-- Premium Header -->
    <div class="bg-gradient-to-r from-violet-600 to-purple-700 rounded-2xl p-8 mb-6 text-white shadow-xl print:hidden">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 rounded-full text-sm font-bold
                        @if($booking->status == 'confirmed') bg-emerald-500
                        @elseif($booking->status == 'pending') bg-yellow-500
                        @elseif($booking->status == 'completed') bg-blue-500
                        @else bg-red-500
                        @endif">
                        {{ ucfirst($booking->status) }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-sm font-bold
                        @if($booking->payment_status == 'paid') bg-emerald-500
                        @elseif($booking->payment_status == 'partial') bg-yellow-500
                        @else bg-red-500
                        @endif">
                        @if($booking->payment_status == 'paid') Fully Paid
                        @elseif($booking->payment_status == 'partial') Partially Paid
                        @else Unpaid
                        @endif
                    </span>
                </div>
                <h1 class="text-3xl font-bold mb-1">Convention Booking #{{ $booking->id }}</h1>
                <p class="text-violet-200">{{ $booking->customer_name }} • {{ $booking->conventionHall->name }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button onclick="printConventionInvoice()" class="px-5 py-2.5 bg-emerald-500 text-white rounded-xl font-bold hover:bg-emerald-600 transition shadow-lg">
                    <i class="fas fa-print mr-2"></i>Print Invoice
                </button>
                <a href="{{ route('admin.convention-bookings.edit', $booking) }}" class="px-5 py-2.5 bg-white text-violet-600 rounded-xl font-bold hover:bg-violet-50 transition shadow-lg">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('admin.convention-bookings.index') }}" class="px-5 py-2.5 bg-white/20 text-white rounded-xl font-bold hover:bg-white/30 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 print:hidden">
        <div class="bg-white rounded-xl shadow-lg p-5 border-l-4 border-violet-500">
            <p class="text-gray-500 text-sm">Event Date</p>
            <p class="text-xl font-bold text-gray-800">{{ \Carbon\Carbon::parse($booking->event_date)->format('d M, Y') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm">Total Amount</p>
            <p class="text-xl font-bold text-gray-800">৳{{ number_format($booking->total_amount, 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-l-4 border-emerald-500">
            <p class="text-gray-500 text-sm">Paid</p>
            <p class="text-xl font-bold text-emerald-600">৳{{ number_format($booking->advance_payment, 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-l-4 border-red-500">
            <p class="text-gray-500 text-sm">Due</p>
            <p class="text-xl font-bold text-red-600">৳{{ number_format($booking->remaining_payment, 0) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Customer Information -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-violet-50 to-purple-50 px-6 py-4 border-b">
                    <h2 class="text-xl font-bold text-violet-700 flex items-center gap-2">
                        <i class="fas fa-user-circle"></i> Customer Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-violet-100 flex items-center justify-center">
                                <i class="fas fa-user text-violet-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Name</p>
                                <p class="font-semibold text-gray-800">{{ $booking->customer_name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                <i class="fas fa-phone text-emerald-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Phone</p>
                                <p class="font-semibold text-gray-800">{{ $booking->customer_phone }}</p>
                            </div>
                        </div>
                        @if($booking->customer_whatsapp)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fab fa-whatsapp text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">WhatsApp</p>
                                <p class="font-semibold text-gray-800">{{ $booking->customer_whatsapp }}</p>
                            </div>
                        </div>
                        @endif
                        @if($booking->customer_email)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-envelope text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Email</p>
                                <p class="font-semibold text-gray-800">{{ $booking->customer_email }}</p>
                            </div>
                        </div>
                        @endif
                        @if($booking->customer_nid)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-id-card text-gray-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">NID</p>
                                <p class="font-semibold text-gray-800">{{ $booking->customer_nid }}</p>
                            </div>
                        </div>
                        @endif
                        @if($booking->organization_name)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                                <i class="fas fa-building text-orange-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Organization</p>
                                <p class="font-semibold text-gray-800">{{ $booking->organization_name }}</p>
                            </div>
                        </div>
                        @endif
                        @if($booking->customer_address)
                        <div class="flex items-center gap-3 md:col-span-2">
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-red-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Address</p>
                                <p class="font-semibold text-gray-800">{{ $booking->customer_address }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Event Details -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-violet-50 to-purple-50 px-6 py-4 border-b">
                    <h2 class="text-xl font-bold text-violet-700 flex items-center gap-2">
                        <i class="fas fa-calendar-star"></i> Event Details
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="bg-gradient-to-br from-violet-50 to-purple-50 rounded-xl p-4 text-center">
                            <i class="fas fa-hotel text-3xl text-violet-500 mb-2"></i>
                            <p class="text-xs text-gray-500">Convention Hall</p>
                            <p class="font-bold text-gray-800">{{ $booking->conventionHall->name }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-4 text-center">
                            <i class="fas fa-calendar-day text-3xl text-blue-500 mb-2"></i>
                            <p class="text-xs text-gray-500">Event Date</p>
                            <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($booking->event_date)->format('d M, Y') }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl p-4 text-center">
                            <i class="fas fa-clock text-3xl text-amber-500 mb-2"></i>
                            <p class="text-xs text-gray-500">Time Slot</p>
                            <p class="font-bold text-gray-800">
                                @if($booking->time_slot == 'morning') Morning (8AM - 2PM)
                                @elseif($booking->time_slot == 'night') Night (6PM - 11PM)
                                @else Full Day (8AM - 11PM)
                                @endif
                            </p>
                        </div>
                        <div class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-xl p-4 text-center">
                            <i class="fas fa-champagne-glasses text-3xl text-pink-500 mb-2"></i>
                            <p class="text-xs text-gray-500">Event Type</p>
                            <p class="font-bold text-gray-800">{{ $booking->event_type }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-xl p-4 text-center">
                            <i class="fas fa-users text-3xl text-emerald-500 mb-2"></i>
                            <p class="text-xs text-gray-500">Number of Guests</p>
                            <p class="font-bold text-gray-800">{{ $booking->number_of_guests }} persons</p>
                        </div>
                        @if($booking->event_description)
                        <div class="bg-gradient-to-br from-gray-50 to-slate-50 rounded-xl p-4 text-center">
                            <i class="fas fa-info-circle text-3xl text-gray-500 mb-2"></i>
                            <p class="text-xs text-gray-500">Description</p>
                            <p class="font-bold text-gray-800">{{ $booking->event_description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Food Package -->
            @if($booking->foodPackage)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-violet-50 to-purple-50 px-6 py-4 border-b">
                    <h2 class="text-xl font-bold text-violet-700 flex items-center gap-2">
                        <i class="fas fa-utensils"></i> Food Package
                    </h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-full bg-amber-100 flex items-center justify-center">
                                <i class="fas fa-bowl-food text-2xl text-amber-600"></i>
                            </div>
                            <div>
                                <p class="font-bold text-lg text-gray-800">{{ $booking->foodPackage->name }}</p>
                                <p class="text-sm text-gray-600">{{ $booking->number_of_guests }} guests × ৳{{ number_format($booking->foodPackage->price_per_person, 0) }}/person</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-amber-600">৳{{ number_format($booking->food_cost, 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Addon Services Section -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-violet-50 to-purple-50 px-6 py-4 border-b flex justify-between items-center">
                    <h2 class="text-xl font-bold text-violet-700 flex items-center gap-2">
                        <i class="fas fa-puzzle-piece"></i> Addon Services
                    </h2>
                    @if($booking->status != 'cancelled' && $booking->status != 'completed')
                    <button onclick="openAddonModal()" class="px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-semibold hover:bg-violet-700 transition">
                        <i class="fas fa-plus mr-1"></i> Manage Addons
                    </button>
                    @endif
                </div>
                <div class="p-6">
                    @php
                        $addons = is_array($booking->selected_addons) ? $booking->selected_addons : json_decode($booking->selected_addons, true);
                        $quantities = is_array($booking->addon_quantities) ? $booking->addon_quantities : json_decode($booking->addon_quantities, true);
                        $categoryIcons = [
                            'decoration' => '🎨',
                            'sound_system' => '🔊',
                            'photography' => '📷',
                            'catering' => '🍽️',
                            'transport' => '🚗',
                            'lighting' => '💡',
                            'stage' => '🎭',
                            'other' => '📦',
                        ];
                    @endphp
                    
                    @if($addons && count($addons) > 0)
                    <div class="space-y-3">
                        @php $totalAddonCost = 0; @endphp
                        @foreach($addons as $addonId)
                            @php
                                $addon = \App\Models\AddonService::find($addonId);
                                $qty = $quantities[$addonId] ?? 1;
                                $addonTotal = $addon ? $addon->price * $qty : 0;
                                $totalAddonCost += $addonTotal;
                            @endphp
                            @if($addon)
                            <div class="flex items-center justify-between bg-gray-50 hover:bg-violet-50 transition rounded-xl p-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-full bg-violet-100 flex items-center justify-center text-xl">
                                        {{ $categoryIcons[$addon->category] ?? '📦' }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $addon->name }}</p>
                                        <p class="text-sm text-gray-500">
                                            <span class="text-violet-600">৳{{ number_format($addon->price, 0) }}</span> × {{ $qty }} {{ $addon->unit ?? 'unit' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-lg text-violet-600">৳{{ number_format($addonTotal, 0) }}</p>
                                </div>
                            </div>
                            @endif
                        @endforeach
                        
                        <div class="flex justify-between items-center pt-4 mt-4 border-t-2 border-violet-100">
                            <span class="font-bold text-gray-700">Total Addon Cost</span>
                            <span class="text-2xl font-bold text-violet-600">৳{{ number_format($booking->addons_cost, 0) }}</span>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <div class="text-gray-300 text-5xl mb-4"><i class="fas fa-puzzle-piece"></i></div>
                        <p class="text-gray-500">No addon services added</p>
                        @if($booking->status != 'cancelled' && $booking->status != 'completed')
                        <button onclick="openAddonModal()" class="mt-4 px-6 py-2 bg-violet-100 text-violet-700 rounded-lg font-semibold hover:bg-violet-200 transition">
                            <i class="fas fa-plus mr-2"></i>Add Addons
                        </button>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment History -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-violet-50 to-purple-50 px-6 py-4 border-b">
                    <h2 class="text-xl font-bold text-violet-700 flex items-center gap-2">
                        <i class="fas fa-history"></i> Payment History
                    </h2>
                </div>
                <div class="p-6">
                    @if($booking->payments && $booking->payments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="text-left text-gray-500 text-sm border-b">
                                    <th class="pb-3 font-semibold">#</th>
                                    <th class="pb-3 font-semibold">Date</th>
                                    <th class="pb-3 font-semibold">Method</th>
                                    <th class="pb-3 font-semibold">Amount</th>
                                    <th class="pb-3 font-semibold">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($booking->payments as $index => $payment)
                                <tr class="hover:bg-violet-50 transition">
                                    <td class="py-3 text-gray-600">{{ $index + 1 }}</td>
                                    <td class="py-3 text-gray-800 font-medium">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M, Y') }}</td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                                            @if($payment->payment_method == 'cash') bg-emerald-100 text-emerald-700
                                            @elseif($payment->payment_method == 'card') bg-blue-100 text-blue-700
                                            @else bg-purple-100 text-purple-700
                                            @endif">
                                            {{ ucfirst($payment->payment_method) }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-emerald-600 font-bold">৳{{ number_format($payment->amount, 0) }}</td>
                                    <td class="py-3 text-gray-500 text-sm">{{ $payment->notes ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 bg-emerald-50">
                                    <td colspan="3" class="py-3 font-bold text-gray-700">Total Paid</td>
                                    <td class="py-3 text-emerald-600 font-bold text-lg">৳{{ number_format($booking->payments->sum('amount'), 0) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <div class="text-gray-300 text-5xl mb-4"><i class="fas fa-wallet"></i></div>
                        <p class="text-gray-500">No payment records yet</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Notes -->
            @if($booking->notes)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-violet-50 to-purple-50 px-6 py-4 border-b">
                    <h2 class="text-xl font-bold text-violet-700 flex items-center gap-2">
                        <i class="fas fa-sticky-note"></i> Notes
                    </h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 whitespace-pre-line">{{ $booking->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Payment Summary & Actions -->
        <div class="space-y-6">
            <!-- Payment Summary Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden sticky top-6">
                <div class="bg-gradient-to-r from-violet-600 to-purple-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-receipt"></i> Payment Summary
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Hall Rent</span>
                        <span class="font-semibold">৳{{ number_format($booking->hall_rent, 0) }}</span>
                    </div>
                    @if($booking->food_cost > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Food Cost</span>
                        <span class="font-semibold">৳{{ number_format($booking->food_cost, 0) }}</span>
                    </div>
                    @endif
                    @if($booking->addons_cost > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Addon Cost</span>
                        <span class="font-semibold">৳{{ number_format($booking->addons_cost, 0) }}</span>
                    </div>
                    @endif
                    
                    @if($booking->discount > 0)
                    <div class="flex justify-between items-center text-red-600">
                        <span>Discount</span>
                        <span class="font-semibold">-৳{{ number_format($booking->discount, 0) }}</span>
                    </div>
                    @endif
                    
                    @if($booking->vat_amount > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">VAT ({{ $booking->vat_percentage ?? 0 }}%)</span>
                        <span class="font-semibold">৳{{ number_format($booking->vat_amount, 0) }}</span>
                    </div>
                    @endif
                    
                    <div class="border-t-2 border-dashed pt-3 mt-3">
                        <div class="flex justify-between items-center text-lg">
                            <span class="font-bold text-gray-800">Grand Total</span>
                            <span class="font-bold text-violet-600">৳{{ number_format($booking->total_amount, 0) }}</span>
                        </div>
                    </div>
                    
                    <div class="bg-emerald-50 rounded-lg p-3">
                        <div class="flex justify-between items-center">
                            <span class="text-emerald-700 font-semibold">Total Paid</span>
                            <span class="font-bold text-emerald-600">৳{{ number_format($booking->advance_payment, 0) }}</span>
                        </div>
                    </div>
                    
                    <div class="bg-red-50 rounded-lg p-3">
                        <div class="flex justify-between items-center">
                            <span class="text-red-700 font-semibold">Due Amount</span>
                            <span class="font-bold text-red-600 text-xl">৳{{ number_format($booking->remaining_payment, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Payment Form -->
            @if($booking->remaining_payment > 0 && $booking->status != 'cancelled')
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-green-500 px-6 py-4">
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fas fa-plus-circle"></i> Add Payment
                    </h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.convention-bookings.add-payment', $booking) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (৳)</label>
                                <input type="number" name="amount" step="1" max="{{ $booking->remaining_payment }}" required 
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition"
                                    placeholder="Enter amount">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
                                <select name="method" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition">
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="mfs">Mobile Banking</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Note (Optional)</label>
                                <textarea name="note" rows="2" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition" placeholder="Payment note..."></textarea>
                            </div>
                            <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-green-500 text-white px-6 py-3 rounded-xl hover:from-emerald-600 hover:to-green-600 transition font-bold shadow-lg">
                                <i class="fas fa-check mr-2"></i>Add Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            @if($booking->status != 'cancelled' && $booking->status != 'completed')
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-gray-100 to-gray-200 px-6 py-4">
                    <h2 class="text-lg font-bold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    @if($booking->status == 'pending')
                    <form action="{{ route('admin.convention-bookings.update-status', $booking) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" class="w-full px-4 py-3 bg-emerald-100 text-emerald-700 rounded-xl font-semibold hover:bg-emerald-200 transition">
                            <i class="fas fa-check mr-2"></i>Confirm Booking
                        </button>
                    </form>
                    @endif
                    @if($booking->payment_status == 'paid')
                    <form action="{{ route('admin.convention-bookings.update-status', $booking) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="w-full px-4 py-3 bg-blue-100 text-blue-700 rounded-xl font-semibold hover:bg-blue-200 transition">
                            <i class="fas fa-flag-checkered mr-2"></i>Mark as Completed
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('admin.convention-bookings.update-status', $booking) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                        @csrf
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="w-full px-4 py-3 bg-red-100 text-red-700 rounded-xl font-semibold hover:bg-red-200 transition">
                            <i class="fas fa-times mr-2"></i>Cancel Booking
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Print Invoice Template -->
    @include('admin.convention-bookings.invoice-template')
</div>

<!-- Addon Management Modal -->
<div id="addonModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden">
        <div class="bg-gradient-to-r from-violet-600 to-purple-600 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-bold text-white"><i class="fas fa-puzzle-piece mr-2"></i>Manage Addon Services</h3>
            <button onclick="closeAddonModal()" class="text-white hover:text-violet-200 text-xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('admin.convention-bookings.update-addons', $booking) }}" method="POST" id="addonForm">
            @csrf
            <div class="p-6 max-h-[60vh] overflow-y-auto">
                @php
                    $allAddons = \App\Models\AddonService::forConvention()->active()->orderBy('category')->orderBy('name')->get();
                    $groupedAddons = $allAddons->groupBy('category');
                    $currentAddons = $addons ?? [];
                    $currentQty = $quantities ?? [];
                    $categoryLabels = [
                        'decoration' => 'Decoration',
                        'sound_system' => 'Sound System',
                        'photography' => 'Photography',
                        'catering' => 'Catering',
                        'transport' => 'Transport',
                        'lighting' => 'Lighting',
                        'stage' => 'Stage Setup',
                        'other' => 'Other',
                    ];
                @endphp
                
                @forelse($groupedAddons as $category => $categoryAddons)
                <div class="mb-6">
                    <h4 class="text-lg font-bold text-violet-700 mb-3 flex items-center gap-2">
                        {{ $categoryIcons[$category] ?? '📦' }} {{ $categoryLabels[$category] ?? ucfirst($category) }}
                    </h4>
                    <div class="space-y-2">
                        @foreach($categoryAddons as $addon)
                        <div class="flex items-center justify-between bg-gray-50 hover:bg-violet-50 rounded-lg p-3 transition">
                            <label class="flex items-center gap-3 cursor-pointer flex-1">
                                <input type="checkbox" name="selected_addons[]" value="{{ $addon->id }}" 
                                    {{ in_array($addon->id, $currentAddons ?? []) ? 'checked' : '' }}
                                    class="w-5 h-5 text-violet-600 rounded focus:ring-violet-500 addon-checkbox"
                                    onchange="toggleQuantity({{ $addon->id }})">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $addon->name }}</p>
                                    <p class="text-sm text-violet-600">৳{{ number_format($addon->price, 0) }} / {{ $addon->unit ?? 'unit' }}</p>
                                </div>
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="addon_quantities[{{ $addon->id }}]" 
                                    value="{{ $currentQty[$addon->id] ?? 1 }}" min="1" max="999"
                                    class="w-20 px-3 py-2 border-2 rounded-lg text-center addon-qty addon-qty-{{ $addon->id }} {{ in_array($addon->id, $currentAddons ?? []) ? '' : 'hidden' }}"
                                    data-addon-id="{{ $addon->id }}">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <p class="text-gray-500">No addon services available</p>
                </div>
                @endforelse
            </div>
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-between items-center">
                <div>
                    <span class="text-gray-600">Selected: </span>
                    <span id="selectedCount" class="font-bold text-violet-600">0</span> addons
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeAddonModal()" class="px-6 py-2 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-100 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-violet-600 text-white rounded-xl font-semibold hover:bg-violet-700 transition">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openAddonModal() {
    document.getElementById('addonModal').classList.remove('hidden');
    document.getElementById('addonModal').classList.add('flex');
    updateSelectedCount();
}

function closeAddonModal() {
    document.getElementById('addonModal').classList.add('hidden');
    document.getElementById('addonModal').classList.remove('flex');
}

function toggleQuantity(addonId) {
    const checkbox = document.querySelector(`input[value="${addonId}"]`);
    const qtyInput = document.querySelector(`.addon-qty-${addonId}`);
    if (checkbox.checked) {
        qtyInput.classList.remove('hidden');
    } else {
        qtyInput.classList.add('hidden');
    }
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.addon-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = checked;
}

// Initialize count on load
document.addEventListener('DOMContentLoaded', updateSelectedCount);

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeAddonModal();
});

// Close modal on backdrop click
document.getElementById('addonModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddonModal();
});

// Print Convention Invoice
function printConventionInvoice() {
    var printArea = document.getElementById('convention-invoice-print-area');
    
    // Show the invoice area and add class to body
    document.body.classList.add('print-convention-invoice');
    printArea.style.display = 'block';
    printArea.style.visibility = 'visible';
    
    // Setup afterprint handler
    window.onafterprint = function() {
        document.body.classList.remove('print-convention-invoice');
        printArea.style.display = 'none';
        printArea.style.visibility = '';
    };
    
    // Print after a short delay
    setTimeout(function() {
        window.print();
    }, 200);
}
</script>
@endsection
