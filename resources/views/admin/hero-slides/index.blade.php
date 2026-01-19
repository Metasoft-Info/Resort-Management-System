@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div><h1 class="text-3xl font-bold text-gray-800">হিরো স্লাইড</h1><p class="text-gray-600 mt-2">হোমপেজ ক্যারোসেল পরিচালনা</p></div>
        <a href="{{ route('admin.hero-slides.create') }}" class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-6 py-3 rounded-lg hover:from-indigo-700 hover:to-indigo-800 transition shadow-lg"><i class="fas fa-plus mr-2"></i>নতুন স্লাইড</a>
    </div>
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">অর্ডার</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">শিরোনাম</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">সাবটাইটেল</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">ছবি</th>
                    <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($heroSlides as $slide)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $slide->order }}</td>
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $slide->title }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ Str::limit($slide->subtitle, 50) }}</td>
                    <td class="px-6 py-4"><img src="{{ $slide->image ?? '/placeholder.jpg' }}" class="h-12 w-20 object-cover rounded"></td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.hero-slides.edit', $slide) }}" class="text-blue-600 hover:text-blue-800 mr-3"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.hero-slides.destroy', $slide) }}" method="POST" class="inline">@csrf @method('DELETE')<button type="submit" onclick="return confirm('আপনি কি নিশ্চিত?')" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">কোনো স্লাইড পাওয়া যায়নি</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $heroSlides->links() }}</div>
</div>
@endsection
