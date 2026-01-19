@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">অ্যাডঅন সার্ভিস</h1>
            <p class="text-gray-600 mt-2">সকল অতিরিক্ত সেবা পরিচালনা করুন</p>
        </div>
        <a href="{{ route('admin.addon-services.create') }}" class="bg-gradient-to-r from-purple-600 to-purple-700 text-white px-6 py-3 rounded-lg hover:from-purple-700 hover:to-purple-800 transition shadow-lg">
            <i class="fas fa-plus mr-2"></i>নতুন সার্ভিস যোগ করুন
        </a>
    </div>
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">নাম</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">বর্ণনা</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">মূল্য</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">ইউনিট</th>
                    <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($addonServices as $service)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $service->name }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ Str::limit($service->description, 50) }}</td>
                    <td class="px-6 py-4 text-gray-800 font-semibold">৳{{ number_format($service->price, 2) }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $service->unit ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.addon-services.edit', $service) }}" class="text-blue-600 hover:text-blue-800 mr-3"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.addon-services.destroy', $service) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('আপনি কি নিশ্চিত?')" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">কোনো সার্ভিস পাওয়া যায়নি</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $addonServices->links() }}</div>
</div>
@endsection
