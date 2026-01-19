@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">কার্যক্রম লগ</h1>
        <p class="text-gray-600 mt-2">সিস্টেম কার্যক্রমের ইতিহাস</p>
    </div>
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">ব্যবহারকারী</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">কার্যক্রম</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">বিবরণ</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">তারিখ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($activityLogs as $log)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $log->user->name ?? 'System' }}</td>
                    <td class="px-6 py-4"><span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">{{ $log->action ?? 'N/A' }}</span></td>
                    <td class="px-6 py-4 text-gray-600">{{ $log->description ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $log->created_at->format('d M Y, h:i A') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">কোনো লগ পাওয়া যায়নি</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $activityLogs->links() }}</div>
</div>
@endsection
