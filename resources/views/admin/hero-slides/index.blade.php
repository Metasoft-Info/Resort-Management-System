@extends('layouts.admin')

@section('title', 'Hero Slides Management')
@section('header', 'Homepage Carousel Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Hero Slides Management</h2>
            <p class="text-gray-600 mt-1">Manage homepage carousel slides</p>
        </div>
        <button onclick="openModal()" class="bg-gradient-to-r from-primary-600 to-primary-600 text-white px-6 py-3 rounded-lg hover:from-primary-700 hover:to-primary-700 transition shadow-lg font-semibold">
            <i class="fas fa-plus mr-2"></i>Add New Slide
        </button>
    </div>

    <!-- Slides Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse($heroSlides as $slide)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition">
            <div class="relative h-64">
                <img src="{{ $slide->image ? asset('storage/' . $slide->image) : '/placeholder.jpg' }}" 
                    alt="{{ $slide->title }}" 
                    class="w-full h-full object-cover">
                <div class="absolute top-4 left-4">
                    <span class="bg-primary-600 text-white px-3 py-1 rounded-full text-sm font-bold">
                        #{{ $slide->order }}
                    </span>
                </div>
                <div class="absolute top-4 right-4">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        {{ $slide->is_active ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                        {{ $slide->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $slide->title }}</h3>
                <p class="text-gray-600 mb-4">{{ $slide->subtitle }}</p>
                
                @if($slide->button_text)
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                    <i class="fas fa-mouse-pointer"></i>
                    <span>Button: {{ $slide->button_text }}</span>
                    @if($slide->button_link)
                        <span class="text-primary-600">→ {{ $slide->button_link }}</span>
                    @endif
                </div>
                @endif

                <div class="flex items-center gap-3">
                    <button onclick="editSlide({{ $slide->id }})" 
                        class="flex-1 bg-primary-500 text-white px-4 py-2 rounded-lg hover:bg-primary-600 transition font-semibold">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </button>
                    <form action="{{ route('admin.hero-slides.destroy', $slide) }}" method="POST" class="flex-1" 
                        onsubmit="return confirmDelete(this, 'এই স্লাইড মুছে ফেলতে চান?')">
                        @csrf @method('DELETE')
                        <button type="submit" 
                            class="w-full bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition font-semibold">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 bg-white rounded-xl shadow">
            <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No slides found. Create your first hero slide!</p>
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $heroSlides->links() }}
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="slideModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-800" id="modalTitle">Add New Slide</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <form id="slideForm" action="{{ route('admin.hero-slides.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" value="POST" id="formMethod">
            
            <div class="space-y-6">
                <!-- Image Upload -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-image mr-2"></i>Hero Image * (Recommended: 1920x800px)
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-500 transition cursor-pointer"
                        onclick="document.getElementById('slideImage').click()">
                        <div id="imagePreview">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                            <p class="text-gray-600">Click to select image</p>
                            <p class="text-sm text-gray-500 mt-2">JPG, PNG, WEBP (Max 5MB)</p>
                        </div>
                    </div>
                    <input type="file" name="image" id="slideImage" accept="image/*" class="hidden" 
                        onchange="previewImage(this)" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-heading mr-2"></i>Title *
                        </label>
                        <input type="text" name="title" id="slideTitle" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-sort-numeric-up mr-2"></i>Order *
                        </label>
                        <input type="number" name="order" id="slideOrder" value="1" min="1" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-text-height mr-2"></i>Subtitle
                    </label>
                    <input type="text" name="subtitle" id="slideSubtitle" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-mouse-pointer mr-2"></i>Button Text
                        </label>
                        <input type="text" name="button_text" id="slideButtonText" placeholder="e.g., Book Now" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-link mr-2"></i>Button Link
                        </label>
                        <input type="text" name="button_link" id="slideButtonLink" placeholder="/rooms" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" id="slideIsActive" value="1" checked 
                            class="w-5 h-5 text-primary-600 rounded focus:ring-2 focus:ring-primary-500">
                        <span class="ml-3 text-sm font-semibold text-gray-700">
                            <i class="fas fa-toggle-on mr-2"></i>Active
                        </span>
                    </label>
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <button type="submit" 
                    class="flex-1 bg-gradient-to-r from-primary-600 to-primary-600 text-white px-6 py-3 rounded-lg hover:from-primary-700 hover:to-primary-700 transition shadow-lg font-semibold">
                    <i class="fas fa-save mr-2"></i><span id="submitBtnText">Create Slide</span>
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
const slides = @json($heroSlides);

function openModal() {
    document.getElementById('slideModal').classList.remove('hidden');
    document.getElementById('slideModal').classList.add('flex');
    document.getElementById('modalTitle').textContent = 'Add New Slide';
    document.getElementById('submitBtnText').textContent = 'Create Slide';
    document.getElementById('slideForm').reset();
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('slideForm').action = '{{ route("admin.hero-slides.store") }}';
    document.getElementById('slideImage').required = true;
    resetImagePreview();
}

function closeModal() {
    document.getElementById('slideModal').classList.add('hidden');
    document.getElementById('slideModal').classList.remove('flex');
}

function editSlide(id) {
    const slide = slides.data.find(s => s.id === id);
    if (!slide) return;
    
    document.getElementById('slideModal').classList.remove('hidden');
    document.getElementById('slideModal').classList.add('flex');
    document.getElementById('modalTitle').textContent = 'Edit Slide';
    document.getElementById('submitBtnText').textContent = 'Update Slide';
    
    document.getElementById('slideTitle').value = slide.title;
    document.getElementById('slideSubtitle').value = slide.subtitle || '';
    document.getElementById('slideButtonText').value = slide.button_text || '';
    document.getElementById('slideButtonLink').value = slide.button_link || '';
    document.getElementById('slideOrder').value = slide.order;
    document.getElementById('slideIsActive').checked = slide.is_active;
    
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('slideForm').action = `/admin/hero-slides/${id}`;
    document.getElementById('slideImage').required = false;
    
    if (slide.image) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = `<img src="/storage/${slide.image}" class="max-h-48 rounded-lg mx-auto">`;
    }
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
        <p class="text-sm text-gray-500 mt-2">JPG, PNG, WEBP (Max 5MB)</p>
    `;
}
</script>
@endsection
