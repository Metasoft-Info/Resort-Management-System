@extends('layouts.admin')
@section('content')
<div class="p-6">
    @include('admin.reports.partials.shared-header', [
        'title' => 'Room Booking Report'
    ])
    @include('admin.reports.partials.shared-styles')

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 print:hidden">
        <form method="GET" action="{{ route('admin.reports.room-bookings') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">All</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Check-In</option>
                        <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Check-Out</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Status</label>
                    <select name="payment_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">All</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Status</label>
                    <select name="discount_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">All</option>
                        <option value="has_discount" {{ request('discount_status') == 'has_discount' ? 'selected' : '' }}>Has Discount</option>
                        <option value="pending" {{ request('discount_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('discount_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('discount_status') == 'rejected' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name / Phone / Room" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.reports.room-bookings', ['due_only' => 1]) }}" 
                       class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition whitespace-nowrap {{ request('due_only') ? 'ring-2 ring-red-300 ring-offset-1' : '' }}"
                       title="Show all bookings with due payment">
                        <i class="fas fa-exclamation-circle mr-1"></i>Due Only
                    </a>
                    <a href="{{ route('admin.reports.room-bookings') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Stats -->
    @php
        $summaryRoomRent = $bookings->sum(fn($b) => $b->getCalculatedTotal());
        $summaryExtra = $bookings->sum('extra_charges');
        $summaryDiscount = $bookings->sum('discount_amount');
    @endphp

    <!-- Booking Count Summary -->
    <div class="bg-white rounded-xl shadow-lg p-5 mb-4 print:shadow-none print:border print:border-gray-400 print:rounded-none">
        <h3 class="text-sm font-bold text-gray-700 mb-3 print:text-xs">Booking Summary</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 print:grid-cols-4 print:gap-2 print:text-xs">
            <div class="bg-green-50 rounded-lg p-3 text-center border border-green-200 print:p-1 print:border-gray-400">
                <p class="text-gray-500 text-xs">Old Guest</p>
                <p class="text-2xl font-bold text-green-700 print:text-base">{{ $oldGuestCount }}</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-3 text-center border border-blue-200 print:p-1 print:border-gray-400">
                <p class="text-gray-500 text-xs">In Guest</p>
                <p class="text-2xl font-bold text-blue-700 print:text-base">{{ $inGuestCount }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3 text-center border border-gray-300 print:p-1 print:border-gray-400">
                <p class="text-gray-500 text-xs">Checkout</p>
                <p class="text-2xl font-bold text-gray-700 print:text-base">{{ $checkoutCount }}</p>
            </div>
            <div class="bg-orange-50 rounded-lg p-3 text-center border border-orange-200 print:p-1 print:border-gray-400">
                <p class="text-gray-500 text-xs">Due Clear</p>
                <p class="text-2xl font-bold text-orange-700 print:text-base">{{ $dueClearCount }}</p>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="bg-white rounded-xl shadow-lg p-5 mb-6 print:shadow-none print:border print:border-gray-400 print:rounded-none">
        <h3 class="text-sm font-bold text-gray-700 mb-3 print:text-xs">Financial Summary</h3>
        <div class="grid grid-cols-2 md:grid-cols-7 gap-3 print:grid-cols-7 print:gap-2 print:text-xs">
            <div class="bg-blue-50 rounded-lg p-3 text-center border border-blue-200 print:p-1 print:border-gray-400">
                <p class="text-gray-500 text-xs">Room Rent</p>
                <p class="text-lg font-bold text-blue-700 print:text-sm">BDT {{ number_format($summaryRoomRent, 0) }}</p>
            </div>
            <div class="bg-orange-50 rounded-lg p-3 text-center border border-orange-200 print:p-1 print:border-gray-400">
                <p class="text-gray-500 text-xs">Discount</p>
                <p class="text-lg font-bold text-orange-600 print:text-sm">BDT {{ number_format($summaryDiscount, 0) }}</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-3 text-center border border-purple-200 print:p-1 print:border-gray-400">
                <p class="text-gray-500 text-xs">Extra Charges</p>
                <p class="text-lg font-bold text-purple-700 print:text-sm">BDT {{ number_format($summaryExtra, 0) }}</p>
            </div>
            <div class="bg-primary-50 rounded-lg p-3 text-center border border-primary-200 print:p-1 print:border-gray-400">
                <p class="text-gray-500 text-xs">Total Bill</p>
                <p class="text-lg font-bold text-primary-700 print:text-sm">BDT {{ number_format($totalRevenue, 0) }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-3 text-center border border-green-200 print:p-1 print:border-gray-400">
                <p class="text-gray-500 text-xs">Advance</p>
                <p class="text-lg font-bold text-green-600 print:text-sm">BDT {{ number_format($totalAdvance, 0) }}</p>
            </div>
            <div class="bg-emerald-50 rounded-lg p-3 text-center border border-emerald-200 print:p-1 print:border-gray-400">
                <p class="text-gray-500 text-xs">Total Deposited</p>
                <p class="text-lg font-bold text-emerald-700 print:text-sm">BDT {{ number_format($totalDeposited, 0) }}</p>
            </div>
            <div class="bg-red-50 rounded-lg p-3 text-center border border-red-200 print:p-1 print:border-gray-400">
                <p class="text-gray-500 text-xs">Remaining</p>
                <p class="text-lg font-bold text-red-600 print:text-sm">BDT {{ number_format($totalRemaining, 0) }}</p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-2 mb-4 print:hidden">
        <button onclick="window.print()" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
            <i class="fas fa-print mr-2"></i>Print
        </button>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden print:shadow-none print:rounded-none print:overflow-visible">
        <div class="report-table-container print:overflow-visible">
            <table class="report-table text-sm border border-gray-400">
                <thead>
                    <tr class="bg-gray-200 print:bg-gray-300">
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">Date</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">Mobile</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800">Name</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800">Company</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">Room</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right whitespace-nowrap">Room Rent</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right whitespace-nowrap">Total Rent</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right whitespace-nowrap">Discount</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right whitespace-nowrap">Extra</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right whitespace-nowrap">Total</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right whitespace-nowrap">Advance</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right whitespace-nowrap">Total Deposited</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right whitespace-nowrap">Remaining</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">In</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">Out</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-center whitespace-nowrap">Nights</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-center whitespace-nowrap">Status</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-center whitespace-nowrap">Discount Status</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 print:hidden whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalNights = 0; $sumGrandTotal = 0; $sumDeposited = 0; $sumRemaining = 0; @endphp
                    @forelse($bookings as $booking)
                    @php
                        $nights = \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date));
                        $totalNights += $nights;
                        $roomRent = $booking->getCalculatedTotal();

                        // Build individual room rent display
                        $roomRentDisplay = '-';
                        if ($booking->bookingRooms->count() > 0) {
                            $roomRentDisplay = $booking->bookingRooms->map(function($br) {
                                $rent = $br->price_per_night ?? 0;
                                $roomNum = e($br->room->room_number ?? '?');
                                return '<div class="whitespace-nowrap">' . $roomNum . ': ' . number_format($rent, 0) . '</div>';
                            })->join('');
                        } elseif ($booking->room) {
                            $roomRentDisplay = number_format($booking->room->room_type->base_price ?? $booking->room->price_per_night ?? 0, 0);
                        }

                        $grandTotal = $booking->getGrandTotal();
                        $sumGrandTotal += $grandTotal;
                        $totalDeposited = $booking->getTotalDepositedInRange($filterStartDate, $filterEndDate);
                        $sumDeposited += $totalDeposited;
                        // Remaining must reflect the true outstanding balance (all payments up to end date),
                        // not just the payments made within the filtered range.
                        $calculatedRemaining = $booking->getGrandTotal() - $booking->getTotalDepositedUpToDate($filterEndDate);
                        $sumRemaining += $calculatedRemaining;
                        $pointInTimeStatus = $booking->getStatusAsOfDate($filterEndDate);
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($booking->created_at)->format('d-m-Y') }}</td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap">{{ $booking->customer_phone }}</td>
                        <td class="border border-gray-400 px-2 py-1 font-medium">{{ $booking->customer_name }}</td>
                        <td class="border border-gray-400 px-2 py-1">{{ $booking->company_name ?? '-' }}</td>
                        <td class="border border-gray-400 px-2 py-1 font-semibold text-primary-700 whitespace-nowrap">{{ $booking->bookingRooms->count() > 0 ? $booking->bookingRooms->map(fn($br) => $br->room->room_number)->join(', ') : ($booking->room ? $booking->room->room_number : 'N/A') }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-right text-gray-600 text-[10px]">{!! $roomRentDisplay !!}</td>
                        <td class="border border-gray-400 px-2 py-1 text-right text-blue-600 whitespace-nowrap">{{ number_format($roomRent, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-right text-orange-600 whitespace-nowrap">{{ ($booking->discount_amount ?? 0) > 0 ? number_format($booking->discount_amount, 0) : '-' }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-right text-purple-600 whitespace-nowrap">{{ ($booking->extra_charges ?? 0) > 0 ? number_format($booking->extra_charges, 0) : '-' }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-right font-semibold whitespace-nowrap">{{ number_format($grandTotal, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-right text-green-600 whitespace-nowrap">{{ number_format($booking->getAdvanceDepositedInRange($filterStartDate, $filterEndDate), 0) }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-right text-emerald-700 whitespace-nowrap">{{ number_format($totalDeposited, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-right text-red-600 font-semibold whitespace-nowrap">{{ number_format($calculatedRemaining, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d-m') }}</td>
                        <td class="border border-gray-400 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d-m') }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-center whitespace-nowrap">{{ $nights }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-center whitespace-nowrap">
                            @if($pointInTimeStatus == 'checked_in')
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700">Check-In</span>
                            @elseif($pointInTimeStatus == 'checked_out')
                                @php $checkoutDateStr = \Carbon\Carbon::parse($booking->check_out_date)->format('Y-m-d'); @endphp
                                @if($checkoutDateStr == $filterEndDate)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700">Check-Out</span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700">Due Clear</span>
                                @endif
                            @elseif($pointInTimeStatus == 'confirmed')
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">Confirmed</span>
                            @elseif($pointInTimeStatus == 'pending')
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-yellow-100 text-yellow-700">Pending</span>
                            @elseif($pointInTimeStatus == 'cancelled')
                                @php $refundAmount = $booking->payments->where('type', 'refund')->sum('amount'); @endphp
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">Cancelled</span>
                                @if($refundAmount > 0)
                                <div class="text-[9px] text-red-500 mt-0.5">Refund: {{ number_format($refundAmount) }}</div>
                                @endif
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600">{{ ucfirst($pointInTimeStatus) }}</span>
                            @endif
                        </td>
                        <td class="border border-gray-400 px-2 py-1 text-center whitespace-nowrap">
                            @if(($booking->discount_amount ?? 0) > 0)
                                @if($booking->discount_status === 'approved')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">Approved</span>
                                @elseif($booking->discount_status === 'pending')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700">Pending</span>
                                @elseif($booking->discount_status === 'rejected')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">Cancelled</span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600">-</span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="border border-gray-400 px-2 py-1 text-center print:hidden whitespace-nowrap">
                            <button onclick="showGuestInfo({{ $booking->id }})" class="bg-primary-600 text-white px-2 py-1 rounded text-xs hover:bg-primary-700">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="19" class="border border-gray-400 px-4 py-8 text-center text-gray-500">No bookings found</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    @php
                        $sumRoomRent = $bookings->sum(fn($b) => $b->getCalculatedTotal());
                        $sumExtra = $bookings->sum('extra_charges');
                        $sumDiscount = $bookings->sum('discount_amount');
                        $sumAdvance = $bookings->sum(fn($b) => $b->getAdvanceDepositedInRange($filterStartDate, $filterEndDate));
                    @endphp
                    <tr class="bg-gray-200 font-bold">
                        <td colspan="5" class="border border-gray-400 px-2 py-2 text-right">Total:</td>
                        <td class="border border-gray-400 px-2 py-2"></td>
                        <td class="border border-gray-400 px-2 py-2 text-right text-blue-600 whitespace-nowrap">{{ number_format($sumRoomRent, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-2 text-right text-orange-600 whitespace-nowrap">{{ number_format($sumDiscount, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-2 text-right text-purple-600 whitespace-nowrap">{{ number_format($sumExtra, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-2 text-right whitespace-nowrap">{{ number_format($sumGrandTotal, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-2 text-right text-green-600 whitespace-nowrap">{{ number_format($sumAdvance, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-2 text-right text-emerald-700 whitespace-nowrap">{{ number_format($sumDeposited, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-2 text-right text-red-600 whitespace-nowrap">{{ number_format($sumRemaining, 0) }}</td>
                        <td colspan="2" class="border border-gray-400 px-2 py-2"></td>
                        <td class="border border-gray-400 px-2 py-2 text-center">{{ $totalNights }}</td>
                        <td class="border border-gray-400 px-2 py-2"></td>
                        <td class="border border-gray-400 px-2 py-2"></td>
                        <td class="border border-gray-400 px-2 py-2 print:hidden"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="mt-6 print:hidden">{{ $bookings->links() }}</div>

    @include('admin.reports.partials.shared-footer')
</div>

<!-- Guest Info Modal -->
<div id="guestInfoModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl my-8 mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
            <h3 class="text-xl font-bold text-gray-800"><i class="fas fa-file-invoice mr-2 text-primary-600"></i>Booking Details / Invoice</h3>
            <div class="flex gap-2">
                <button onclick="printInvoice()" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">
                    <i class="fas fa-print mr-1"></i>Invoice Print
                </button>
                <button onclick="closeGuestInfoModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div id="guestInfoContent" class="p-6">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-2xl text-primary-600"></i>
                <p class="text-gray-600 mt-2">Loading...</p>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Print Area for Guest Info -->
<div id="guestPrintArea" class="hidden print:block"></div>

<style>
@media print {
    /* Report-specific print overrides */
    .report-table {
        font-size: 8.5px !important;
    }
    
    .report-table th,
    .report-table td {
        padding: 1px 3px !important;
    }
    
    /* Allow name and company columns to wrap */
    .report-table td:nth-child(3),
    .report-table td:nth-child(4) {
        white-space: normal !important;
        max-width: 90px !important;
    }
    
    /* Compact summary stats for print */
    .grid.grid-cols-2.md\:grid-cols-8 {
        gap: 1.5mm !important;
        margin-bottom: 2mm !important;
    }
    
    .grid.grid-cols-2.md\:grid-cols-8 > div {
        padding: 1.5mm !important;
    }
    
    .grid.grid-cols-2.md\:grid-cols-8 .text-xl {
        font-size: 10px !important;
    }
    
    .grid.grid-cols-2.md\:grid-cols-8 .text-xs {
        font-size: 7px !important;
    }
}

@media print {
    body.print-guest-info * {
        visibility: hidden;
    }
    body.print-guest-info #guestPrintArea,
    body.print-guest-info #guestPrintArea * {
        visibility: visible;
    }
    body.print-guest-info #guestPrintArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 15mm;
    }
    body.print-guest-info .invoice-print {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 12pt;
    }
    body.print-guest-info .invoice-print table {
        font-size: 10pt !important;
    }
    body.print-guest-info .invoice-print th,
    body.print-guest-info .invoice-print td {
        padding: 6px 10px !important;
    }
}
</style>

<script>
// Store booking data for quick access
const bookingsData = @json($bookings->keyBy('id'));

// Store current booking ID for printing
let currentBookingId = null;

// Helper function to format date
function formatDate(dateStr) {
    if (!dateStr) return 'N/A';
    const date = new Date(dateStr);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
}

// Helper function to format currency
function formatTaka(amount) {
    return 'BDT ' + parseFloat(amount || 0).toLocaleString('en-GB', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

// Helper function to get status badge
function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pending</span>',
        'confirmed': '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Confirmed</span>',
        'checked_in': '<span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Check-In</span>',
        'checked_out': '<span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">Check-Out</span>',
        'cancelled': '<span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Cancelled</span>',
        'paid': '<span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Paid</span>',
        'partial': '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Partial</span>'
    };
    return badges[status] || `<span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">${status}</span>`;
}

function showGuestInfo(bookingId) {
    const modal = document.getElementById('guestInfoModal');
    const content = document.getElementById('guestInfoContent');
    
    // Store current booking ID for printing
    currentBookingId = bookingId;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Fetch booking details
    fetch(`/admin/bookings/${bookingId}?ajax=1`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.booking) {
            const b = data.booking;
            
            // Calculate nights
            const checkIn = new Date(b.check_in_date);
            const checkOut = new Date(b.check_out_date);
            const nights = Math.max(1, Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24)));
            
            // Build rooms list with prices
            let roomsHtml = '';
            let roomDetailsHtml = '';
            let baseAmount = 0;
            
            if (b.booking_rooms && b.booking_rooms.length > 0) {
                roomsHtml = b.booking_rooms.map(br => br.room?.room_number || 'N/A').join(', ');
                roomDetailsHtml = b.booking_rooms.map(br => {
                    const roomPrice = parseFloat(br.price_per_night) || 0;
                    const roomTotal = roomPrice * nights;
                    baseAmount += roomTotal;
                    const roomType = br.room?.room_type?.name || '';
                    return `
                        <tr class="border-b">
                            <td class="py-2 px-2">${br.room?.room_number || 'N/A'} ${roomType ? '('+roomType+')' : ''}</td>
                            <td class="py-2 px-2 text-right">${formatTaka(roomPrice)}</td>
                            <td class="py-2 px-2 text-center">${nights}</td>
                            <td class="py-2 px-2 text-right font-semibold">${formatTaka(roomTotal)}</td>
                        </tr>
                    `;
                }).join('');
            } else if (b.room) {
                roomsHtml = b.room.room_number;
                const roomPrice = parseFloat(b.room?.room_type?.base_price || b.room?.price_per_night || 0);
                baseAmount = roomPrice * nights;
                roomDetailsHtml = `
                    <tr class="border-b">
                        <td class="py-2 px-2">${b.room.room_number} ${b.room.room_type?.name ? '('+b.room.room_type.name+')' : ''}</td>
                        <td class="py-2 px-2 text-right">${formatTaka(roomPrice)}</td>
                        <td class="py-2 px-2 text-center">${nights}</td>
                        <td class="py-2 px-2 text-right font-semibold">${formatTaka(baseAmount)}</td>
                    </tr>
                `;
            }
            
            // Calculate totals
            const discountAmount = parseFloat(b.discount_amount) || 0;
            const discountPercent = parseFloat(b.discount_percentage) || 0;
            let discount = 0;
            if (b.discount_type === 'percentage' && discountPercent > 0) {
                discount = (baseAmount * discountPercent) / 100;
            } else if (b.discount_type === 'flat' && discountAmount > 0) {
                discount = discountAmount;
            }
            
            const afterDiscount = baseAmount - discount;
            const extraCharges = parseFloat(b.extra_charges) || 0;
            const vatAmount = b.vat_enabled ? (afterDiscount * 0.15) : 0;
            const grandTotal = afterDiscount + extraCharges + vatAmount;
            const advancePayment = parseFloat(b.advance_payment) || 0;
            const remaining = grandTotal - advancePayment;
            
            // Additional guests
            let additionalGuestsHtml = '';
            if (b.additional_guests && b.additional_guests.length > 0) {
                additionalGuestsHtml = `
                    <div class="mt-4 border-t pt-4">
                        <h4 class="font-bold text-gray-800 mb-3"><i class="fas fa-users mr-2"></i>Extra Guests (${b.additional_guests.length})</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            ${b.additional_guests.map((g, i) => `
                                <div class="bg-gray-50 p-2 rounded border text-sm">
                                    <p class="font-semibold">${i+1}. ${g.name || 'N/A'}</p>
                                    <p class="text-gray-600">NID: ${g.nid || 'N/A'} | Phone: ${g.phone || 'N/A'}</p>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }
            
            // Payment history
            let paymentHistoryHtml = '';
            if (b.payments && b.payments.length > 0) {
                paymentHistoryHtml = `
                    <div class="mt-4 border-t pt-4">
                        <h4 class="font-bold text-gray-800 mb-3"><i class="fas fa-history mr-2"></i>Payment History</h4>
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-2 text-left">Date</th>
                                    <th class="py-2 px-2 text-right">Amount</th>
                                    <th class="py-2 px-2 text-left">Method</th>
                                    <th class="py-2 px-2 text-left">Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${b.payments.map(p => `
                                    <tr class="border-b">
                                        <td class="py-2 px-2">${formatDate(p.created_at)}</td>
                                        <td class="py-2 px-2 text-right font-semibold text-green-600">${formatTaka(p.amount)}</td>
                                        <td class="py-2 px-2">${p.method || 'N/A'}</td>
                                        <td class="py-2 px-2 text-xs text-gray-600">${p.note || '-'}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            }
            
            content.innerHTML = `
                <div class="space-y-4" id="guestInfoPrintContent">
                    <!-- Header -->
                    <div class="text-center border-b-2 border-gray-300 pb-4 mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Tufan Convention & Resort</h2>
                        <p class="text-sm text-gray-600">Invoice / Booking Details</p>
                        <p class="text-lg font-bold text-primary-600 mt-2">Booking #${String(b.id).padStart(5, '0')}</p>
                    </div>
                    
                    <!-- Customer & Booking Info Side by Side -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Customer Info -->
                        <div class="bg-primary-50 p-4 rounded-lg border border-primary-200">
                            <h4 class="font-bold text-primary-800 mb-3 border-b pb-2"><i class="fas fa-user mr-2"></i>Customer Info</h4>
                            <div class="space-y-1 text-sm">
                                <p><span class="font-semibold w-20 inline-block">Name:</span> ${b.customer_name || 'N/A'}</p>
                                <p><span class="font-semibold w-20 inline-block">Phone:</span> ${b.customer_phone || 'N/A'}</p>
                                <p><span class="font-semibold w-20 inline-block">NID:</span> ${b.customer_nid || 'N/A'}</p>
                                <p><span class="font-semibold w-20 inline-block">Company:</span> ${b.company_name || 'N/A'}</p>
                                <p><span class="font-semibold w-20 inline-block">Address:</span> ${b.customer_address || 'N/A'}</p>
                            </div>
                        </div>
                        
                        <!-- Booking Info -->
                        <div class="bg-gray-50 p-4 rounded-lg border">
                            <h4 class="font-bold text-gray-800 mb-3 border-b pb-2"><i class="fas fa-calendar-alt mr-2"></i>Booking Info</h4>
                            <div class="space-y-1 text-sm">
                                <p><span class="font-semibold">Check-In:</span> ${formatDate(b.check_in_date)} ${b.check_in_time ? 'Time: ' + b.check_in_time : ''}</p>
                                <p><span class="font-semibold">Check-Out:</span> ${formatDate(b.check_out_date)} ${b.check_out_time ? 'Time: ' + b.check_out_time : ''}</p>
                                <p><span class="font-semibold">Total Nights:</span> ${nights} Nights</p>
                                <p><span class="font-semibold">Guests:</span> ${b.number_of_guests || 1} guests</p>
                                <p><span class="font-semibold">Status:</span> ${getStatusBadge(b.status)}</p>
                                <p><span class="font-semibold">Payment:</span> ${getStatusBadge(b.payment_status)}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Room Details Table -->
                    <div class="mt-4">
                        <h4 class="font-bold text-gray-800 mb-2"><i class="fas fa-bed mr-2"></i>Room Details</h4>
                        <table class="w-full text-sm border">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-2 px-2 text-left border">Room</th>
                                    <th class="py-2 px-2 text-right border">Per Night</th>
                                    <th class="py-2 px-2 text-center border">Nights</th>
                                    <th class="py-2 px-2 text-right border">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${roomDetailsHtml}
                            </tbody>
                            <tfoot class="bg-gray-100 font-bold">
                                <tr>
                                    <td colspan="3" class="py-2 px-2 text-right border">Subtotal:</td>
                                    <td class="py-2 px-2 text-right border">${formatTaka(baseAmount)}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Payment Summary -->
                    <div class="mt-4 bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <h4 class="font-bold text-gray-800 mb-3 border-b pb-2"><i class="fas fa-calculator mr-2"></i>Payment Summary</h4>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>Room Charge:</div>
                            <div class="text-right">${formatTaka(baseAmount)}</div>
                            
                            ${discount > 0 ? `
                            <div class="text-green-600">Discount ${b.discount_type === 'percentage' ? '('+discountPercent+'%)' : ''}:</div>
                            <div class="text-right text-green-600">- ${formatTaka(discount)}</div>
                            ` : ''}
                            
                            ${extraCharges > 0 ? `
                            <div>Extra Charges:</div>
                            <div class="text-right">+ ${formatTaka(extraCharges)}</div>
                            ` : ''}
                            
                            ${b.vat_enabled ? `
                            <div>VAT (15%):</div>
                            <div class="text-right">+ ${formatTaka(vatAmount)}</div>
                            ` : ''}
                            
                            <div class="font-bold text-lg border-t pt-2 mt-2">Grand Total:</div>
                            <div class="text-right font-bold text-lg border-t pt-2 mt-2">${formatTaka(grandTotal)}</div>
                            
                            <div class="text-green-700">Advance:</div>
                            <div class="text-right text-green-700">${formatTaka(advancePayment)}</div>
                            
                            <div class="font-bold ${remaining > 0 ? 'text-red-600' : 'text-green-600'}">Remaining:</div>
                            <div class="text-right font-bold ${remaining > 0 ? 'text-red-600' : 'text-green-600'}">${formatTaka(remaining)}</div>
                        </div>
                    </div>
                    
                    ${additionalGuestsHtml}
                    
                    ${paymentHistoryHtml}
                    
                    <!-- Reference Info -->
                    ${b.reference_name || b.reference_phone ? `
                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-200 mt-4">
                        <p class="text-sm"><i class="fas fa-phone-alt mr-2"></i><span class="font-semibold">Reference:</span> ${b.reference_name || 'N/A'} | Phone: ${b.reference_phone || 'N/A'}</p>
                    </div>
                    ` : ''}
                    
                    <!-- Notes -->
                    ${b.notes ? `
                    <div class="bg-gray-100 p-3 rounded-lg mt-4">
                        <p class="text-sm"><i class="fas fa-sticky-note mr-2"></i><span class="font-semibold">Note:</span> ${b.notes}</p>
                    </div>
                    ` : ''}
                    
                    <!-- Signature Section -->
                    <div class="mt-8 pt-4 border-t-2 border-gray-800">
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; text-align: center;">
                            <div>
                                <div style="height: 48px; border-bottom: 1px solid #666; margin-bottom: 8px;"></div>
                                <p style="font-size: 12px; font-weight: bold; margin: 2px 0;">Manager</p>
                                <p style="font-size: 10px; color: #666;">Signature</p>
                            </div>
                            <div>
                                <div style="height: 48px; border-bottom: 1px solid #666; margin-bottom: 8px;"></div>
                                <p style="font-size: 12px; font-weight: bold; margin: 2px 0;">Accountant</p>
                                <p style="font-size: 10px; color: #666;">Signature</p>
                            </div>
                            <div>
                                <div style="height: 48px; border-bottom: 1px solid #666; margin-bottom: 8px;"></div>
                                <p style="font-size: 12px; font-weight: bold; margin: 2px 0;">Admin</p>
                                <p style="font-size: 10px; color: #666;">Signature</p>
                            </div>
                            <div>
                                <div style="height: 48px; border-bottom: 1px solid #666; margin-bottom: 8px;"></div>
                                <p style="font-size: 12px; font-weight: bold; margin: 2px 0;">Authority</p>
                                <p style="font-size: 10px; color: #666;">Signature</p>
                            </div>
                        </div>
                    </div>

                    <!-- Print Footer -->
                    <div style="margin-top: 16px; padding-top: 8px; border-top: 1px solid #ccc; text-align: center; font-size: 10px; color: #666;">
                        <p>Print Date: ${new Date().toLocaleDateString('en-GB')} | Developed by Mir Javed Jeetu | 01811480222</p>
                        <p style="margin-top: 2px;">TUFAN RESORT | 01958216727</p>
                    </div>
                </div>
            `;
        } else {
            content.innerHTML = '<div class="text-center py-8 text-red-600">Failed to load data</div>';
        }
    })
    .catch(err => {
        console.error(err);
        content.innerHTML = '<div class="text-center py-8 text-red-600">Error loading data</div>';
    });
}

function closeGuestInfoModal() {
    const modal = document.getElementById('guestInfoModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function printInvoice() {
    if (!currentBookingId) {
        alert('Booking info not found');
        return;
    }
    
    // Show loading indicator
    const printBtn = document.querySelector('button[onclick="printInvoice()"]');
    const originalText = printBtn.innerHTML;
    printBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Loading...';
    printBtn.disabled = true;
    
    // Create hidden iframe
    let iframe = document.getElementById('printInvoiceFrame');
    if (!iframe) {
        iframe = document.createElement('iframe');
        iframe.id = 'printInvoiceFrame';
        iframe.style.cssText = 'position: absolute; width: 0; height: 0; border: none; visibility: hidden;';
        document.body.appendChild(iframe);
    }
    
    // Load booking page in iframe
    iframe.src = `/admin/bookings/${currentBookingId}`;
    
    iframe.onload = function() {
        try {
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            
            // Wait for content to fully render then trigger print
            setTimeout(() => {
                // Add print-invoice class to iframe body
                iframeDoc.body.classList.add('print-invoice');
                
                // Show invoice area
                const invoiceArea = iframeDoc.getElementById('invoice-print-area');
                if (invoiceArea) {
                    invoiceArea.style.display = 'block';
                }
                
                // Print iframe
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
                
                // Reset button
                printBtn.innerHTML = originalText;
                printBtn.disabled = false;
            }, 1000);
        } catch (e) {
            console.error('Print error:', e);
            printBtn.innerHTML = originalText;
            printBtn.disabled = false;
            alert('Error printing');
        }
    };
    
    iframe.onerror = function() {
        printBtn.innerHTML = originalText;
        printBtn.disabled = false;
        alert('Error loading invoice');
    };
}

// Close modal on outside click
document.getElementById('guestInfoModal').addEventListener('click', function(e) {
    if (e.target === this) closeGuestInfoModal();
});
</script>
@endsection
