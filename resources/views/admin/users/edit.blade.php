@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">ব্যবহারকারী সম্পাদনা</h1>
        <p class="text-gray-600 mt-2">ব্যবহারকারীর তথ্য ও অনুমতি পরিচালনা করুন</p>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">নাম *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ইমেইল *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">পাসওয়ার্ড (খালি রাখলে পরিবর্তন হবে না)</label>
                    <input type="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">রোল *</label>
                    <select name="role" id="roleSelect" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" onchange="togglePermissions()">
                        <option value="superadmin" {{ ($user->role ?? '') == 'superadmin' ? 'selected' : '' }}>সুপার অ্যাডমিন</option>
                        <option value="admin" {{ ($user->role ?? 'admin') == 'admin' ? 'selected' : '' }}>অ্যাডমিন</option>
                        <option value="manager" {{ ($user->role ?? '') == 'manager' ? 'selected' : '' }}>ম্যানেজার</option>
                        <option value="receptionist" {{ ($user->role ?? '') == 'receptionist' ? 'selected' : '' }}>রিসেপশনিস্ট</option>
                        <option value="staff" {{ ($user->role ?? '') == 'staff' ? 'selected' : '' }}>স্টাফ</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" {{ ($user->is_active ?? true) ? 'checked' : '' }} class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-green-500 mr-3">
                        <span class="font-semibold text-gray-700">অ্যাকাউন্ট সক্রিয়</span>
                    </label>
                    <p class="text-sm text-gray-500 mt-1">নিষ্ক্রিয় অ্যাকাউন্ট লগইন করতে পারবে না</p>
                </div>
            </div>

            <!-- Permissions Section -->
            <div id="permissionsSection" class="border-t pt-6 {{ in_array($user->role, ['superadmin', 'admin']) ? 'hidden' : '' }}">
                <h3 class="text-xl font-bold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-key text-yellow-500 mr-3"></i>মেনু অনুমতি
                </h3>
                <p class="text-gray-600 mb-4">এই ব্যবহারকারী কোন মেনু গুলোতে অ্যাক্সেস পাবে নির্বাচন করুন। (সুপার অ্যাডমিন/অ্যাডমিন সব মেনুতে অ্যাক্সেস পায়)</p>
                
                @php
                    $userPermissions = $user->permissions ?? [];
                    $groupedMenus = $menuSettings->groupBy('group_name');
                @endphp

                <div class="space-y-4">
                    @foreach($groupedMenus as $groupName => $menus)
                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                                <h4 class="font-bold text-gray-700">
                                    <i class="fas fa-folder mr-2 text-yellow-500"></i>
                                    {{ $groupName ?: 'প্রধান মেনু' }}
                                </h4>
                                <label class="flex items-center text-sm">
                                    <input type="checkbox" onchange="toggleGroupPermissions(this, '{{ Str::slug($groupName ?: 'main') }}')" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 mr-2">
                                    <span class="text-gray-600">সব নির্বাচন</span>
                                </label>
                            </div>
                            <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($menus as $menu)
                                    @if($menu->menu_key !== 'dashboard')
                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer transition hover:bg-gray-50 {{ in_array($menu->menu_key, $userPermissions) ? 'border-green-300 bg-green-50' : 'border-gray-200' }}">
                                        <input type="checkbox" name="permissions[]" value="{{ $menu->menu_key }}" 
                                            {{ in_array($menu->menu_key, $userPermissions) ? 'checked' : '' }}
                                            data-group="{{ Str::slug($groupName ?: 'main') }}"
                                            class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-green-500 mr-3 permission-checkbox">
                                        <span class="flex items-center text-gray-800">
                                            <i class="{{ $menu->menu_icon }} w-5 mr-2 text-gray-500"></i>
                                            {{ $menu->menu_label }}
                                        </span>
                                    </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 flex gap-4">
                    <button type="button" onclick="selectAllPermissions()" class="text-primary-600 hover:text-primary-800 text-sm font-semibold">
                        <i class="fas fa-check-double mr-1"></i>সব নির্বাচন করুন
                    </button>
                    <button type="button" onclick="clearAllPermissions()" class="text-red-600 hover:text-red-800 text-sm font-semibold">
                        <i class="fas fa-times mr-1"></i>সব সরান
                    </button>
                </div>
            </div>

            <div class="flex gap-4 mt-8 pt-6 border-t">
                <button type="submit" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-8 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
                    <i class="fas fa-save mr-2"></i>আপডেট করুন
                </button>
                <a href="{{ route('admin.users.index') }}" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition">
                    <i class="fas fa-times mr-2"></i>বাতিল
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function togglePermissions() {
    const role = document.getElementById('roleSelect').value;
    const section = document.getElementById('permissionsSection');
    if (role === 'superadmin' || role === 'admin') {
        section.classList.add('hidden');
    } else {
        section.classList.remove('hidden');
    }
}

function toggleGroupPermissions(checkbox, groupName) {
    const checkboxes = document.querySelectorAll(`[data-group="${groupName}"]`);
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
        updateCheckboxStyle(cb);
    });
}

function selectAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.checked = true;
        updateCheckboxStyle(cb);
    });
}

function clearAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.checked = false;
        updateCheckboxStyle(cb);
    });
}

function updateCheckboxStyle(cb) {
    const label = cb.closest('label');
    if (cb.checked) {
        label.classList.add('border-green-300', 'bg-green-50');
        label.classList.remove('border-gray-200');
    } else {
        label.classList.remove('border-green-300', 'bg-green-50');
        label.classList.add('border-gray-200');
    }
}

document.querySelectorAll('.permission-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        updateCheckboxStyle(this);
    });
});
</script>
@endsection
