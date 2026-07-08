@extends('layouts.admin')

@section('title', 'Users')
@section('header', 'System Users')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-slate-700 to-slate-800 rounded-2xl p-6 shadow-xl text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-users-cog text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">System Users</h1>
                    <p class="text-slate-300 text-sm">Manage staff and admin accounts</p>
                </div>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-5 py-2.5 bg-white text-slate-700 rounded-xl hover:bg-slate-50 transition font-semibold shadow-lg">
                <i class="fas fa-plus mr-2"></i>New User
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/30 transition group">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center mr-3 shadow-sm">
                                    <span class="text-white text-sm font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <span class="font-semibold text-gray-800">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-sm">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @php
                                $roleColors = [
                                    'admin' => 'bg-purple-100 text-purple-700',
                                    'manager' => 'bg-blue-100 text-blue-700',
                                    'receptionist' => 'bg-emerald-100 text-emerald-700',
                                ];
                                $roleColor = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $roleColor }}">
                                {{ ucfirst($user->role ?? 'admin') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-sm">
                            <i class="far fa-calendar-alt text-gray-300 mr-1"></i>{{ $user->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition">
                                <a href="{{ route('admin.users.edit', $user) }}" class="w-8 h-8 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition flex items-center justify-center" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                @if($user->id != auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="event.preventDefault(); confirmDelete(this.form, 'Are you sure you want to delete this user?')" class="w-8 h-8 bg-red-500 text-white rounded-lg hover:bg-red-600 transition flex items-center justify-center" title="Delete">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-users text-3xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No users found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex justify-end">
        {{ $users->links() }}
    </div>
</div>
@endsection
