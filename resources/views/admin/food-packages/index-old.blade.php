@extends('layouts.admin')
@section('content')
<div class="p-6">
 <div class="flex justify-between items-center mb-8">
 <div><h1 class="text-3xl font-bold text-gray-800">Food Package</h1></div>
 <a href="{{ route('admin.food-packages.create') }}" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg"><i class="fas fa-plus mr-2"></i>New Package</a>
 </div>
 <div class="bg-white rounded-xl shadow-lg overflow-hidden">
 <table class="min-w-full">
 <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
 <tr>
 <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Name</th>
 <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Description</th>
 <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Price</th>
 <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">Action</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-200">
 @forelse($foodPackages as $package)
 <tr class="hover:bg-gray-50 transition">
 <td class="px-6 py-4 font-semibold text-gray-800">{{ $package->name }}</td>
 <td class="px-6 py-4 text-gray-600">{{ Str::limit($package->description, 50) }}</td>
 <td class="px-6 py-4 text-gray-800 font-semibold">{{ number_format($package->price, 2) }}</td>
 <td class="px-6 py-4 text-right">
 <a href="{{ route('admin.food-packages.edit', $package) }}" class="text-primary-600 hover:text-primary-800 mr-3"><i class="fas fa-edit"></i></a>
 <form action="{{ route('admin.food-packages.destroy', $package) }}" method="POST" class="inline">@csrf @method('DELETE')<button type="submit" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button></form>
 </td>
 </tr>
 @empty
 <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">No packages found</td></tr>
 @endforelse
 </tbody>
 </table>
 </div>
 <div class="mt-6">{{ $foodPackages->links() }}</div>
</div>
@endsection