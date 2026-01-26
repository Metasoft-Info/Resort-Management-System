@extends('layouts.app')

@section('title', 'কনভেনশন হল')

@section('content')
<!-- Hero Section -->
<div class="relative bg-gradient-to-br from-blue-600 via-primary-600 to-purple-600 text-white py-24 overflow-hidden">
    <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    <div class="container mx-auto px-4 relative z-10">
        <nav class="flex items-center text-sm mb-6">
            <a href="{{ route('home') }}" class="hover:text-yellow-200 transition">
                <i class="fas fa-home mr-2"></i>হোম
            </a>
            <i class="fas fa-chevron-right mx-3 text-sm opacity-50"></i>
            <span class="text-yellow-200">কনভেনশন হল</span>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 leading-tight">কনভেনশন হল এবং ইভেন্ট স্পেস</h1>
            <p class="text-xl md:text-2xl text-gray-100 font-light">আপনার স্মরণীয় ইভেন্টের জন্য পারফেক্ট ভেন্যু</p>
        </div>
    </div>
</div>

<section class="py-16 bg-gradient-to-b from-white to-gray-50">
    <div class="container mx-auto px-4">
        @foreach($halls as $hall)
            @php
                $images = is_array($hall->images) ? $hall->images : (json_decode($hall->images, true) ?? []);
            @endphp
            <div class="max-w-6xl mx-auto bg-white rounded-3xl shadow-2xl overflow-hidden mb-12 transform hover:-translate-y-1 transition">
                <!-- Hero Image/Gallery -->
                <div class="relative h-80 md:h-96 bg-gradient-to-br from-blue-400 via-primary-400 to-purple-500 overflow-hidden">
                    @if(count($images) > 0)
                        <!-- Main Image -->
                        <img src="{{ asset('storage/' . $images[0]) }}" alt="{{ $hall->name }}" class="w-full h-full object-cover" id="mainImage-{{ $hall->id }}">
                        
                        <!-- Image Count Badge -->
                        @if(count($images) > 1)
                            <div class="absolute top-4 right-4 bg-black/50 text-white px-3 py-2 rounded-lg text-sm font-bold">
                                <i class="fas fa-images mr-1"></i>{{ count($images) }}টি ছবি
                            </div>
                        @endif
                        
                        <!-- Navigation Arrows (if multiple images) -->
                        @if(count($images) > 1)
                            <button type="button" onclick="changeImage('{{ $hall->id }}', -1)" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-black/50 hover:bg-black/70 text-white rounded-full flex items-center justify-center transition">
                                <i class="fas fa-chevron-left text-xl"></i>
                            </button>
                            <button type="button" onclick="changeImage('{{ $hall->id }}', 1)" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-black/50 hover:bg-black/70 text-white rounded-full flex items-center justify-center transition">
                                <i class="fas fa-chevron-right text-xl"></i>
                            </button>
                        @endif
                    @else
                        <div class="absolute inset-0 opacity-30" style="background-image: url('data:image/svg+xml,%3Csvg width=\'100\' height=\'100\' viewBox=\'0 0 100 100\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z\' fill=\'%23ffffff\' fill-opacity=\'1\' fill-rule=\'evenodd\'/%3E%3C/svg%3E');"></div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    <div class="absolute bottom-8 left-8 right-8">
                        <div class="inline-block px-4 py-2 bg-white rounded-full text-sm font-bold text-primary-700 mb-4">
                            <i class="fas fa-building mr-2"></i>এক্সক্লুসিভ ভেন্যু
                        </div>
                        <h2 class="text-4xl md:text-5xl font-bold text-white mb-2">{{ $hall->name }}</h2>
                    </div>
                </div>
                
                <!-- Thumbnail Gallery (if multiple images) -->
                @if(count($images) > 1)
                    <div class="bg-gray-100 px-4 py-3 overflow-x-auto">
                        <div class="flex gap-2" id="thumbnails-{{ $hall->id }}">
                            @foreach($images as $index => $image)
                                <button type="button" onclick="setMainImage('{{ $hall->id }}', {{ $index }})" class="flex-shrink-0 w-20 h-16 rounded-lg overflow-hidden border-2 transition {{ $index === 0 ? 'border-primary-500' : 'border-transparent hover:border-primary-300' }}" id="thumb-{{ $hall->id }}-{{ $index }}">
                                    <img src="{{ asset('storage/' . $image) }}" alt="Image {{ $index + 1 }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <div class="p-8 md:p-12">
                    <p class="text-gray-700 text-xl leading-relaxed mb-10">{{ $hall->description }}</p>
                    
                    <!-- Key Stats -->
                    <div class="grid md:grid-cols-3 gap-6 mb-12">
                        <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-primary-50 rounded-2xl transform hover:-translate-y-1 transition">
                            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-primary-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                                <i class="fas fa-ruler-combined text-3xl text-white"></i>
                            </div>
                            <div class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-primary-600 bg-clip-text text-transparent mb-1">{{ number_format($hall->dimensions) }}</div>
                            <div class="text-sm text-gray-600 font-semibold">বর্গফুট</div>
                        </div>
                        <div class="text-center p-6 bg-gradient-to-br from-purple-50 to-accent-50 rounded-2xl transform hover:-translate-y-1 transition">
                            <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-accent-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                                <i class="fas fa-users text-3xl text-white"></i>
                            </div>
                            <div class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-accent-600 bg-clip-text text-transparent mb-1">{{ $hall->max_capacity }}</div>
                            <div class="text-sm text-gray-600 font-semibold">অতিথি ধারণক্ষমতা</div>
                        </div>
                        <div class="text-center p-6 bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl transform hover:-translate-y-1 transition">
                            <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                                <i class="fas fa-tag text-3xl text-white"></i>
                            </div>
                            <div class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-1">৳{{ number_format($hall->price_per_day) }}</div>
                            <div class="text-sm text-gray-600 font-semibold">প্রতিদিন</div>
                        </div>
                    </div>
                    
                    <!-- Amenities -->
                    @php
                        $amenities = is_array($hall->amenities) ? $hall->amenities : (json_decode($hall->amenities, true) ?? []);
                    @endphp
                    @if(count($amenities) > 0)
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-accent-500 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-star text-2xl text-white"></i>
                            </div>
                            <h3 class="text-2xl md:text-3xl font-bold">সুবিধাসমূহ</h3>
                        </div>
                        <div class="grid md:grid-cols-3 gap-4">
                            @foreach($amenities as $amenity)
                                <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-gray-50 to-white rounded-xl hover:shadow-md transition border border-gray-100">
                                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-check text-primary-600 text-lg font-bold"></i>
                                    </div>
                                    <span class="text-gray-700 font-medium">{{ $amenity }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Event Types -->
                    @php
                        $eventTypes = is_array($hall->event_types) ? $hall->event_types : (json_decode($hall->event_types, true) ?? []);
                    @endphp
                    @if(count($eventTypes) > 0)
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-calendar-alt text-2xl text-white"></i>
                            </div>
                            <h3 class="text-2xl md:text-3xl font-bold">উপযুক্ত ইভেন্ট</h3>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            @foreach($eventTypes as $type)
                                <span class="px-6 py-3 bg-gradient-to-r from-primary-100 to-accent-100 text-primary-700 rounded-xl font-bold hover:from-primary-200 hover:to-accent-200 transition shadow-sm hover:shadow-md">
                                    <i class="fas fa-check-circle mr-2"></i>{{ $type }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Contact Info -->
                    <div class="text-center pt-6 border-t">
                        <p class="text-gray-600 text-lg mb-4"><i class="fas fa-info-circle mr-2 text-primary-500"></i>বুকিং এর জন্য অ্যাডমিন প্যানেলে যোগাযোগ করুন</p>
                        <div class="inline-flex items-center px-10 py-5 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-2xl font-bold text-lg">
                            <i class="fas fa-phone-alt mr-3 text-2xl text-primary-600"></i>
                            {{ $resortInfo->phone ?? 'যোগাযোগ করুন' }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

@push('scripts')
<script>
// Store current image index for each hall
const currentImageIndex = {};

// Store images for each hall
const hallImages = {
    @foreach($halls as $hall)
        @php
            $images = is_array($hall->images) ? $hall->images : (json_decode($hall->images, true) ?? []);
        @endphp
        '{{ $hall->id }}': [
            @foreach($images as $image)
                '{{ asset("storage/" . $image) }}',
            @endforeach
        ],
    @endforeach
};

function setMainImage(hallId, index) {
    const images = hallImages[hallId];
    if (!images || images.length === 0) return;
    
    currentImageIndex[hallId] = index;
    
    const mainImg = document.getElementById('mainImage-' + hallId);
    if (mainImg) {
        mainImg.src = images[index];
    }
    
    // Update thumbnail borders
    const thumbnails = document.getElementById('thumbnails-' + hallId);
    if (thumbnails) {
        const buttons = thumbnails.querySelectorAll('button');
        buttons.forEach((btn, i) => {
            if (i === index) {
                btn.classList.remove('border-transparent', 'hover:border-primary-300');
                btn.classList.add('border-primary-500');
            } else {
                btn.classList.remove('border-primary-500');
                btn.classList.add('border-transparent', 'hover:border-primary-300');
            }
        });
    }
}

function changeImage(hallId, direction) {
    const images = hallImages[hallId];
    if (!images || images.length === 0) return;
    
    if (currentImageIndex[hallId] === undefined) {
        currentImageIndex[hallId] = 0;
    }
    
    let newIndex = currentImageIndex[hallId] + direction;
    
    // Loop around
    if (newIndex < 0) newIndex = images.length - 1;
    if (newIndex >= images.length) newIndex = 0;
    
    setMainImage(hallId, newIndex);
}
</script>
@endpush
@endsection
