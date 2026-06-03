@extends('layouts.admin')
@section('content')
<div class="p-6">
 <div class="flex justify-between items-center mb-8">
 <div><h1 class="text-3xl font-bold text-gray-800">Users</h1><p class="text-gray-600 mt-2">Manage System Users</p></div>
 <a href="{{ route('admin.users.create') }}" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg"><i class="fas fa-plus mr-2"></i>New User</a>
 </div>
 <div class="bg-white rounded-xl shadow-lg overflow-hidden">
 <table class="min-w-full">
 <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
 <tr>
 <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Name</th>
 <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Email</th>
 <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Role</th>
 <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Date</th>
 <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">Action</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-200">
 @forelse($users as $user)
 <tr class="hover:bg-gray-50 transition">
 <td class="px-6 py-4 font-semibold text-gray-800">{{ $user->name }}</td>
 <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
 <td class="px-6 py-4"><span class="px-3 py-1 rounded-full text-xs font-semibold bg-primary-100 text-primary-800">{{ $user->role ?? 'admin' }}</span></td>
 <td class="px-6 py-4 text-gray-600">{{ $user->created_at->format('d M Y') }}</td>
 <td class="px-6 py-4 text-right">
 <a href="{{ route('admin.users.edit', $user) }}" class="text-primary-600 hover:text-primary-800 mr-3"><i class="fas fa-edit"></i></a>
 @if($user->id != auth()->id())
 <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">@csrf @method('DELETE')<button type="submit" onclick="event.preventDefault(); confirmDelete(this.form, 'Are you sure?')" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button></form>
 @endif
 </td>
 </tr>
 @empty
 <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No users found</td></tr>
 @endforelse
 </tbody>
 </table>
 </div>
 <div class="mt-6">{{ $users->links() }}</div>
</div>
@endsection
