<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php $siteInfo = \App\Models\ResortInfo::first(); @endphp
    <title>@yield('title', 'Dashboard') - {{ $siteInfo->resort_name ?? 'Tufan Resort' }} Admin</title>
    @if($siteInfo && $siteInfo->favicon)
        <link rel="icon" type="image/jpeg" href="{{ asset('storage/' . $siteInfo->favicon) }}">
        <link rel="shortcut icon" type="image/jpeg" href="{{ asset('storage/' . $siteInfo->favicon) }}">
    @else
        <link rel="icon" type="image/jpeg" href="{{ asset('images/favicon.jpg') }}">
        <link rel="shortcut icon" type="image/jpeg" href="{{ asset('images/favicon.jpg') }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#faf5ff',
                            100: '#f3e8ff',
                            200: '#e9d5ff',
                            300: '#d8b4fe',
                            400: '#c084fc',
                            500: '#a855f7',
                            600: '#9333ea',
                            700: '#7e22ce',
                            800: '#6b21a8',
                            900: '#581c87',
                        },
                        accent: {
                            500: '#ec4899',
                            600: '#db2777',
                            700: '#be185d',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Mobile Menu Overlay -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>
        
        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 bg-gradient-to-b from-primary-900 via-primary-800 to-primary-900 text-white shadow-2xl flex-shrink-0 fixed h-screen z-50 flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="p-4 lg:p-6 border-b border-primary-700">
                <div class="flex items-center space-x-3">
                    @if($siteInfo && $siteInfo->admin_logo)
                        <img src="{{ asset('storage/' . $siteInfo->admin_logo) }}" alt="Admin Logo" class="h-10 lg:h-12 w-auto rounded-xl object-contain shadow-lg">
                    @else
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-accent-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-hotel text-xl lg:text-2xl"></i>
                        </div>
                    @endif
                    <div class="flex-1">
                        <p class="text-xs lg:text-sm text-primary-200 font-medium">Resort Management</p>
                    </div>
                    <!-- Close button for mobile -->
                    <button onclick="toggleSidebar()" class="lg:hidden text-white hover:text-primary-200 p-1">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <nav class="mt-4 lg:mt-6 px-2 lg:px-3 flex-1 overflow-y-auto pb-8" style="max-height: calc(100vh - 120px);">
                @php
                    $groupedMenus = \App\Models\AdminMenuSetting::getMenusForUser(auth()->user());
                @endphp

                @foreach($groupedMenus as $groupName => $menus)
                    @if($groupName)
                        <div class="text-xs text-primary-300 px-4 py-2 mt-4 font-semibold uppercase tracking-wider">{{ $groupName }}</div>
                    @endif
                    @foreach($menus as $menu)
                        <a href="{{ route($menu->route_name) }}" class="flex items-center px-3 lg:px-4 py-2.5 lg:py-3 mb-1 lg:mb-2 rounded-xl transition text-sm lg:text-base @if(request()->routeIs($menu->route_pattern)) bg-primary-700 shadow-lg @else hover:bg-primary-700/50 @endif">
                            <i class="{{ $menu->menu_icon }} w-5 mr-2 lg:mr-3 text-sm"></i>
                            <span class="font-medium lg:font-semibold">{{ $menu->menu_label }}</span>
                        </a>
                    @endforeach
                @endforeach
                
                <div class="border-t border-primary-700 my-4"></div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-3 lg:px-4 py-2.5 lg:py-3 rounded-xl hover:bg-red-500/20 text-red-200 hover:text-red-100 transition text-sm lg:text-base">
                        <i class="fas fa-sign-out-alt w-5 mr-2 lg:mr-3"></i>
                        <span class="font-medium lg:font-semibold">Logout</span>
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col lg:ml-64 overflow-hidden w-full">
            <header class="bg-white shadow-md z-20">
                <div class="px-4 lg:px-8 py-4 lg:py-5 flex items-center justify-between">
                    <!-- Mobile Menu Button -->
                    <button onclick="toggleSidebar()" class="lg:hidden text-gray-700 hover:text-primary-600 p-2 -ml-2">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-lg lg:text-2xl font-bold bg-gradient-to-r from-primary-600 to-accent-600 bg-clip-text text-transparent truncate flex-1 lg:flex-none text-center lg:text-left">@yield('header', 'Dashboard')</h2>
                    <div class="flex items-center space-x-2 lg:space-x-4">
                        <span class="text-xs lg:text-sm text-gray-600 hidden sm:inline"><i class="fas fa-user-circle mr-1 lg:mr-2"></i>{{ auth()->user()->name ?? 'Admin' }}</span>
                        <span class="text-xs text-gray-600 sm:hidden"><i class="fas fa-user-circle"></i></span>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-gray-50">
                <div class="p-4 lg:p-8">
                    @yield('content')
                </div>
            </main>
            
            <!-- Developer Footer -->
            <footer class="bg-gray-100 border-t border-gray-200 py-2 lg:py-3 px-4 lg:px-6 text-center">
                <p class="text-xs lg:text-sm text-gray-600">Developed by <span class="font-semibold text-gray-800">Mir Javed Jeetu</span> | <a href="tel:01811480222" class="text-indigo-600 hover:text-indigo-800 font-medium">01811480222</a></p>
            </footer>
        </div>
    </div>
    
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
            
            // Prevent body scroll when sidebar is open
            if (!sidebar.classList.contains('-translate-x-full')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }
        
        // Close sidebar on window resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });
    </script>

    <!-- Global Modal for Success/Error Messages -->
    <div id="globalModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[9999] transition-opacity duration-300">
        <div id="modalContent" class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0">
            <!-- Success Modal Content -->
            <div id="successModal" class="hidden text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
                    <i class="fas fa-check-circle text-green-500 text-5xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-3">সফল!</h3>
                <p id="successMessage" class="text-gray-600 mb-6 text-lg"></p>
                <button onclick="closeGlobalModal()" class="bg-gradient-to-r from-green-500 to-green-600 text-white px-8 py-3 rounded-xl font-semibold hover:from-green-600 hover:to-green-700 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fas fa-thumbs-up mr-2"></i>ঠিক আছে
                </button>
            </div>
            
            <!-- Error Modal Content -->
            <div id="errorModal" class="hidden text-center">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
                    <i class="fas fa-times-circle text-red-500 text-5xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-3">ত্রুটি!</h3>
                <p id="errorMessage" class="text-gray-600 mb-6 text-lg"></p>
                <button onclick="closeGlobalModal()" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-8 py-3 rounded-xl font-semibold hover:from-red-600 hover:to-red-700 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fas fa-times mr-2"></i>বন্ধ করুন
                </button>
            </div>
            
            <!-- Warning Modal Content -->
            <div id="warningModal" class="hidden text-center">
                <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-5xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-3">সতর্কতা!</h3>
                <p id="warningMessage" class="text-gray-600 mb-6 text-lg"></p>
                <button onclick="closeGlobalModal()" class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-8 py-3 rounded-xl font-semibold hover:from-yellow-600 hover:to-yellow-700 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fas fa-check mr-2"></i>বুঝেছি
                </button>
            </div>
            
            <!-- Info Modal Content -->
            <div id="infoModal" class="hidden text-center">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-info-circle text-blue-500 text-5xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-3">তথ্য</h3>
                <p id="infoMessage" class="text-gray-600 mb-6 text-lg"></p>
                <button onclick="closeGlobalModal()" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-8 py-3 rounded-xl font-semibold hover:from-blue-600 hover:to-blue-700 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fas fa-check mr-2"></i>ঠিক আছে
                </button>
            </div>

            <!-- Confirm Modal Content -->
            <div id="confirmModal" class="hidden text-center">
                <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-question-circle text-purple-500 text-5xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-3">নিশ্চিত করুন</h3>
                <p id="confirmMessage" class="text-gray-600 mb-6 text-lg"></p>
                <div class="flex gap-3 justify-center">
                    <button onclick="closeGlobalModal()" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-300 transition">
                        <i class="fas fa-times mr-2"></i>বাতিল
                    </button>
                    <button id="confirmYesBtn" class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-purple-600 hover:to-purple-700 transition shadow-lg">
                        <i class="fas fa-check mr-2"></i>হ্যাঁ, নিশ্চিত
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global Modal Functions
        function showGlobalModal(type, message) {
            const modal = document.getElementById('globalModal');
            const content = document.getElementById('modalContent');
            
            // Hide all modal types
            document.getElementById('successModal').classList.add('hidden');
            document.getElementById('errorModal').classList.add('hidden');
            document.getElementById('warningModal').classList.add('hidden');
            document.getElementById('infoModal').classList.add('hidden');
            document.getElementById('confirmModal').classList.add('hidden');
            
            // Show the correct modal type
            if (type === 'success') {
                document.getElementById('successModal').classList.remove('hidden');
                document.getElementById('successMessage').textContent = message;
            } else if (type === 'error') {
                document.getElementById('errorModal').classList.remove('hidden');
                document.getElementById('errorMessage').textContent = message;
            } else if (type === 'warning') {
                document.getElementById('warningModal').classList.remove('hidden');
                document.getElementById('warningMessage').textContent = message;
            } else if (type === 'info') {
                document.getElementById('infoModal').classList.remove('hidden');
                document.getElementById('infoMessage').textContent = message;
            }
            
            // Show modal with animation
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function showConfirmModal(message, onConfirm) {
            const modal = document.getElementById('globalModal');
            const content = document.getElementById('modalContent');
            
            // Hide all modal types
            document.getElementById('successModal').classList.add('hidden');
            document.getElementById('errorModal').classList.add('hidden');
            document.getElementById('warningModal').classList.add('hidden');
            document.getElementById('infoModal').classList.add('hidden');
            document.getElementById('confirmModal').classList.remove('hidden');
            
            document.getElementById('confirmMessage').textContent = message;
            
            // Set up confirm button
            const confirmBtn = document.getElementById('confirmYesBtn');
            confirmBtn.onclick = function() {
                closeGlobalModal();
                if (typeof onConfirm === 'function') {
                    onConfirm();
                }
            };
            
            // Show modal with animation
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeGlobalModal() {
            const modal = document.getElementById('globalModal');
            const content = document.getElementById('modalContent');
            
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }

        // Close modal on backdrop click
        document.getElementById('globalModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeGlobalModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeGlobalModal();
            }
        });

        // Helper function for delete confirmation with form submission
        function confirmDelete(form, message) {
            event.preventDefault();
            showConfirmModal(message || 'আপনি কি নিশ্চিত মুছে ফেলতে চান?', function() {
                form.submit();
            });
            return false;
        }

        // Show session messages on page load
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showGlobalModal('success', @json(session('success')));
            @endif
            
            @if(session('error'))
                showGlobalModal('error', @json(session('error')));
            @endif
            
            @if(session('warning'))
                showGlobalModal('warning', @json(session('warning')));
            @endif
            
            @if(session('info'))
                showGlobalModal('info', @json(session('info')));
            @endif
        });

        // Helper function for AJAX responses
        function handleAjaxResponse(response) {
            if (response.success) {
                showGlobalModal('success', response.message || 'অপারেশন সফল হয়েছে!');
            } else if (response.error) {
                showGlobalModal('error', response.message || response.error || 'কিছু একটা সমস্যা হয়েছে!');
            }
        }
    </script>
</body>
</html>