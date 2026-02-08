@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Customer List</h1>
            <p class="text-gray-600 mt-1">All Room Booking Customers</p>
        </div>
        <a href="{{ route('admin.customers.export') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-file-export mr-2"></i>Export CSV
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-primary-100 text-sm">Total Customers</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($totalCustomers) }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3"><i class="fas fa-users text-2xl"></i></div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Total Revenue</p>
                    <p class="text-3xl font-bold mt-2">৳{{ number_format($totalRevenue, 0) }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3"><i class="fas fa-money-bill-wave text-2xl"></i></div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">Total Due</p>
                    <p class="text-3xl font-bold mt-2">৳{{ number_format($totalDue, 0) }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3"><i class="fas fa-exclamation-triangle text-2xl"></i></div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
        <form method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, phone, email or company..." 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
            <button type="submit" class="bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition">
                <i class="fas fa-search mr-2"></i>Search
            </button>
            @if(request('search'))
            <a href="{{ route('admin.customers.index') }}" class="bg-gray-500 text-white px-4 py-3 rounded-lg hover:bg-gray-600 transition">
                <i class="fas fa-times"></i>
            </a>
            @endif
        </form>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Contact</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Company</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Bookings</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Total Spent</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Due</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($customers as $index => $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold">{{ $customers->firstItem() + $index }}</td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-900">{{ $customer->customer_name }}</div>
                            @if($customer->customer_nid)
                            <div class="text-xs text-gray-500">NID: {{ $customer->customer_nid }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-primary-600 font-medium">{{ $customer->customer_phone }}</div>
                            @if($customer->customer_email)
                            <div class="text-xs text-gray-500">{{ $customer->customer_email }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $customer->company_name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-primary-100 text-primary-800 px-2 py-1 rounded-full text-sm font-bold">{{ $customer->booking_count }}</span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-green-600">৳{{ number_format($customer->total_spent, 0) }}</td>
                        <td class="px-4 py-3 text-right font-semibold {{ $customer->total_due > 0 ? 'text-red-600' : 'text-gray-500' }}">
                            ৳{{ number_format($customer->total_due, 0) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.customers.show', urlencode($customer->customer_phone)) }}" 
                                   class="bg-primary-100 text-primary-700 p-2 rounded-lg hover:bg-primary-200 transition" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="tel:{{ $customer->customer_phone }}" 
                                   class="bg-green-100 text-green-700 p-2 rounded-lg hover:bg-green-200 transition" title="Call">
                                    <i class="fas fa-phone"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                            <p>No customers found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $customers->links() }}</div>
</div>
@endsection
