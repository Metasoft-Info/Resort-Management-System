@php
$isConvention = $isConvention ?? false;
$pageTitle = $isConvention ? 'Convention Customers' : 'Customers';
$pageHeader = $isConvention ? 'Convention Customer Management' : 'Customer Management';
$subTitle = $isConvention ? 'All convention hall customers' : 'All room booking customers';
$routeName = $isConvention ? 'admin.customers.convention' : 'admin.customers.index';
@endphp
@extends('layouts.admin')

@section('title', $pageTitle)
@section('header', $pageHeader)

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r {{ $isConvention ? 'from-violet-500 to-purple-600' : 'from-sky-500 to-blue-600' }} rounded-2xl p-6 shadow-xl text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">{{ $pageHeader }}</h1>
                    <p class="{{ $isConvention ? 'text-purple-100' : 'text-blue-100' }} text-sm">{{ $subTitle }}</p>
                </div>
            </div>
            @if(!$isConvention)
            <a href="{{ route('admin.customers.export') }}" class="inline-flex items-center px-5 py-2.5 bg-white text-blue-700 rounded-xl hover:bg-blue-50 transition font-semibold shadow-lg">
                <i class="fas fa-file-export mr-2"></i>Export CSV
            </a>
            @endif
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-users text-blue-500"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Customers</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalCustomers) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-money-bill-wave text-emerald-500"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalRevenue, 0) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Due</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($totalDue, 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
        <form method="GET" action="{{ route($routeName) }}" class="flex gap-3">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, phone, email or company..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl hover:bg-blue-700 transition font-semibold shadow-md">
                Search
            </button>
            @if(request('search'))
            <a href="{{ route($routeName) }}" class="bg-gray-100 text-gray-600 px-4 py-2.5 rounded-xl hover:bg-gray-200 transition flex items-center">
                <i class="fas fa-times"></i>
            </a>
            @endif
        </form>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Company</th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Bookings</th>
                        <th class="px-4 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total Spent</th>
                        <th class="px-4 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Due</th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($customers as $index => $customer)
                    <tr class="hover:bg-blue-50/20 transition group">
                        <td class="px-4 py-4 text-sm font-semibold text-gray-500">{{ $customers->firstItem() + $index }}</td>
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full flex items-center justify-center mr-2 shadow-sm">
                                    <span class="text-white text-xs font-bold">{{ strtoupper(substr($customer->customer_name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800 text-sm">{{ $customer->customer_name }}</div>
                                    @if($customer->customer_nid)
                                    <div class="text-[11px] text-gray-400">NID: {{ $customer->customer_nid }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-blue-600">{{ $customer->customer_phone }}</div>
                            @if($customer->customer_email)
                            <div class="text-xs text-gray-400">{{ $customer->customer_email }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-gray-500 text-sm">{{ $customer->company_name ?? '-' }}</td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                {{ $customer->booking_count }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right font-bold text-emerald-600 text-sm">{{ number_format($customer->total_spent, 0) }}</td>
                        <td class="px-4 py-4 text-right font-bold {{ $customer->total_due > 0 ? 'text-red-600' : 'text-gray-400' }} text-sm">
                            {{ number_format($customer->total_due, 0) }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition">
                                <a href="{{ route('admin.customers.show', urlencode($customer->customer_phone)) }}"
                                class="w-8 h-8 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition flex items-center justify-center" title="View Details">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="tel:{{ $customer->customer_phone }}"
                                class="w-8 h-8 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition flex items-center justify-center" title="Call">
                                    <i class="fas fa-phone text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-users text-3xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No customers found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex justify-end">
        {{ $customers->links() }}
    </div>
</div>
@endsection
