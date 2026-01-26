@extends('layouts.admin')

@section('title', 'Food Packages Management')
@section('header', 'Convention Hall Food Packages')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Food Packages Management</h2>
            <p class="text-gray-600 mt-1">Manage food packages for convention hall events</p>
        </div>
        <button onclick="openModal()" class="bg-gradient-to-r from-orange-600 to-red-600 text-white px-6 py-3 rounded-lg hover:from-orange-700 hover:to-red-700 transition shadow-lg font-semibold">
            <i class="fas fa-plus mr-2"></i>Add New Package
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($foodPackages as $package)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-1">
            @if($package->image)
            <img src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name }}" class="w-full h-48 object-cover">
            @else
            <div class="w-full h-48 bg-gradient-to-br from-orange-400 to-red-400 flex items-center justify-center">
                <i class="fas fa-utensils text-6xl text-white opacity-50"></i>
            </div>
            @endif
            
            <div class="p-6">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="text-xl font-bold text-gray-800">{{ $package->name }}</h3>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $package->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $package->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                
                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $package->description }}</p>
                
                @if($package->items && count($package->items) > 0)
                <div class="mb-4">
                    <p class="text-xs font-semibold text-gray-500 mb-2">INCLUDES:</p>
                    <ul class="space-y-1">
                        @foreach(array_slice($package->items, 0, 3) as $item)
                        <li class="text-sm text-gray-700 flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2 text-xs"></i>
                            {{ $item }}
                        </li>
                        @endforeach
                        @if(count($package->items) > 3)
                        <li class="text-sm text-gray-500 italic">+{{ count($package->items) - 3 }} more items...</li>
                        @endif
                    </ul>
                </div>
                @endif
                
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500">Price per person</p>
                        <p class="text-2xl font-bold text-orange-600">৳{{ number_format($package->price_per_person) }}</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="editPackage({{ $package->id }})" class="bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600 transition">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('admin.food-packages.destroy', $package) }}" method="POST" class="inline" onsubmit="return confirmDelete(this, '{{ $package->name }} মুছে ফেলতে চান?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 bg-white rounded-xl shadow">
            <i class="fas fa-utensils text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No food packages found. Create your first package!</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="packageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-800" id="modalTitle">Add New Package</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <form id="packageForm" action="{{ route('admin.food-packages.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" value="POST" id="formMethod">
            
            <div class="space-y-6">
                <!-- Image Upload -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-image mr-2"></i>Package Image
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-orange-500 transition cursor-pointer"
                        onclick="document.getElementById('packageImage').click()">
                        <div id="imagePreview">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                            <p class="text-gray-600">Click to select image</p>
                            <p class="text-sm text-gray-500 mt-2">JPG, PNG, WEBP (Max 2MB)</p>
                        </div>
                    </div>
                    <input type="file" name="image" id="packageImage" accept="image/*" class="hidden" onchange="previewImage(this)">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tag mr-2"></i>Package Name *
                    </label>
                    <input type="text" name="name" id="packageName" required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-align-left mr-2"></i>Description
                    </label>
                    <textarea name="description" id="packageDescription" rows="3" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-bangladeshi-taka-sign mr-2"></i>Price per Person (৳) *
                    </label>
                    <input type="number" name="price_per_person" id="packagePrice" step="0.01" required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-list mr-2"></i>Package Items
                    </label>
                    <div id="itemsList" class="space-y-2 mb-3"></div>
                    <button type="button" onclick="addItem()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-sm font-semibold">
                        <i class="fas fa-plus mr-2"></i>Add Item
                    </button>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" id="packageIsActive" value="1" checked 
                            class="w-5 h-5 text-orange-600 rounded focus:ring-2 focus:ring-orange-500">
                        <span class="ml-3 text-sm font-semibold text-gray-700">
                            <i class="fas fa-toggle-on mr-2"></i>Active
                        </span>
                    </label>
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <button type="submit" 
                    class="flex-1 bg-gradient-to-r from-orange-600 to-red-600 text-white px-6 py-3 rounded-lg hover:from-orange-700 hover:to-red-700 transition shadow-lg font-semibold">
                    <i class="fas fa-save mr-2"></i><span id="submitBtnText">Create Package</span>
                </button>
                <button type="button" onclick="closeModal()" 
                    class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition font-semibold">
                    <i class="fas fa-times mr-2"></i>Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const packages = @json($foodPackages);
let itemIndex = 0;

function openModal() {
    document.getElementById('packageModal').classList.remove('hidden');
    document.getElementById('packageModal').classList.add('flex');
    document.getElementById('modalTitle').textContent = 'Add New Package';
    document.getElementById('submitBtnText').textContent = 'Create Package';
    document.getElementById('packageForm').reset();
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('packageForm').action = '{{ route("admin.food-packages.store") }}';
    document.getElementById('itemsList').innerHTML = '';
    itemIndex = 0;
    resetImagePreview();
}

function closeModal() {
    document.getElementById('packageModal').classList.add('hidden');
    document.getElementById('packageModal').classList.remove('flex');
}

function editPackage(id) {
    const pkg = packages.find(p => p.id === id);
    if (!pkg) return;
    
    document.getElementById('packageModal').classList.remove('hidden');
    document.getElementById('packageModal').classList.add('flex');
    document.getElementById('modalTitle').textContent = 'Edit Package';
    document.getElementById('submitBtnText').textContent = 'Update Package';
    
    document.getElementById('packageName').value = pkg.name;
    document.getElementById('packageDescription').value = pkg.description || '';
    document.getElementById('packagePrice').value = pkg.price_per_person;
    document.getElementById('packageIsActive').checked = pkg.is_active;
    
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('packageForm').action = `/admin/food-packages/${id}`;
    
    document.getElementById('itemsList').innerHTML = '';
    itemIndex = 0;
    if (pkg.items && pkg.items.length > 0) {
        pkg.items.forEach(item => addItem(item));
    }
    
    if (pkg.image) {
        document.getElementById('imagePreview').innerHTML = `<img src="/storage/${pkg.image}" class="max-h-48 rounded-lg mx-auto">`;
    }
}

function addItem(value = '') {
    const itemsList = document.getElementById('itemsList');
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = `
        <input type="text" name="items[]" value="${value}" placeholder="Item name (e.g., Rice, Dal, Chicken)" 
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
        <button type="button" onclick="this.parentElement.remove()" 
            class="bg-red-500 text-white px-3 py-2 rounded-lg hover:bg-red-600 transition">
            <i class="fas fa-times"></i>
        </button>
    `;
    itemsList.appendChild(div);
    itemIndex++;
}

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="max-h-48 rounded-lg mx-auto">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function resetImagePreview() {
    document.getElementById('imagePreview').innerHTML = `
        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
        <p class="text-gray-600">Click to select image</p>
        <p class="text-sm text-gray-500 mt-2">JPG, PNG, WEBP (Max 2MB)</p>
    `;
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
