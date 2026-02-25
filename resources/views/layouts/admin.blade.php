<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ $resortInfo->resort_name ?? 'Tufan Resort' }} Admin</title>
    @if($resortInfo && $resortInfo->favicon)
        <link rel="icon" type="image/jpeg" href="{{ asset('storage/' . $resortInfo->favicon) }}">
        <link rel="shortcut icon" type="image/jpeg" href="{{ asset('storage/' . $resortInfo->favicon) }}">
    @else
        <link rel="icon" type="image/jpeg" href="{{ asset('images/favicon.jpg') }}">
        <link rel="shortcut icon" type="image/jpeg" href="{{ asset('images/favicon.jpg') }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Global responsive fixes */
        .overflow-x-auto { -webkit-overflow-scrolling: touch; }
        table { border-collapse: collapse; }
        @media (max-width: 640px) {
            .responsive-table { display: block; }
            .responsive-table thead { display: none; }
            .responsive-table tbody { display: block; }
            .responsive-table tr { display: block; margin-bottom: 1rem; background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1rem; }
            .responsive-table td { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6; }
            .responsive-table td:last-child { border-bottom: none; }
            .responsive-table td::before { content: attr(data-label); font-weight: 600; color: #374151; margin-right: 1rem; }
        }
        /* Prevent horizontal overflow */
        .main-content { max-width: 100%; overflow-x: hidden; }
        .main-content > * { max-width: 100%; }
        /* Input and select fixes for mobile */
        @media (max-width: 640px) {
            input, select, textarea { font-size: 16px !important; }
        }
        /* Premium Sidebar Styles */
        .sidebar-collapsed { width: 72px !important; }
        .sidebar-collapsed .sidebar-text { display: none; }
        .sidebar-collapsed .sidebar-logo-text { display: none; }
        .sidebar-collapsed .sidebar-section-title { display: none; }
        .sidebar-collapsed .sidebar-menu-item { justify-content: center; padding-left: 0; padding-right: 0; }
        .sidebar-collapsed .sidebar-menu-item i { margin-right: 0; }
        .sidebar-collapsed .sidebar-collapse-btn i { transform: rotate(180deg); }
        .sidebar-collapsed .sidebar-header { padding: 1rem 0.5rem; justify-content: center; }
        .sidebar-collapsed .sidebar-logo { margin: 0 auto; }
        .sidebar-section { overflow: hidden; transition: max-height 0.3s ease-out; }
        .sidebar-section.collapsed { max-height: 0 !important; }
        .sidebar-section-header { cursor: pointer; user-select: none; }
        .sidebar-section-header .chevron { transition: transform 0.3s ease; }
        .sidebar-section-header.collapsed .chevron { transform: rotate(-90deg); }
        /* Scrollbar Styling */
        .sidebar-nav { overflow-x: hidden; }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
        /* Tooltip for collapsed sidebar */
        .sidebar-tooltip { 
            position: absolute; left: 100%; top: 50%; transform: translateY(-50%);
            background: #1f2937; color: white; padding: 0.5rem 0.75rem; border-radius: 0.5rem;
            font-size: 0.75rem; white-space: nowrap; opacity: 0; visibility: hidden;
            transition: opacity 0.2s, visibility 0.2s; margin-left: 0.5rem; z-index: 100;
        }
        .sidebar-collapsed .sidebar-menu-item:hover .sidebar-tooltip { opacity: 1; visibility: visible; }
        /* Main content transition */
        .main-wrapper { transition: margin-left 0.3s ease; }
        .main-wrapper.sidebar-collapsed { margin-left: 72px !important; }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#020617',
                        },
                        accent: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
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
        <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden" onclick="toggleMobileSidebar()"></div>
        
        <!-- Premium Sidebar -->
        <aside id="sidebar" class="w-64 bg-gradient-to-b from-slate-900 via-slate-900 to-slate-800 text-white shadow-2xl flex-shrink-0 fixed h-screen z-50 flex flex-col transform -translate-x-full lg:translate-x-0 transition-all duration-300 ease-in-out overflow-hidden">
            <!-- Sidebar Header -->
            <div class="sidebar-header p-4 border-b border-slate-700/50 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    @if($resortInfo && $resortInfo->admin_logo)
                        <img src="{{ asset('storage/' . $resortInfo->admin_logo) }}" alt="Admin Logo" class="sidebar-logo h-10 w-10 rounded-xl object-contain bg-white p-1 shadow-lg">
                    @else
                        <div class="sidebar-logo w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-hotel text-white text-lg"></i>
                        </div>
                    @endif
                    <div class="sidebar-logo-text">
                        <p class="text-sm font-bold text-white">Tufan Resort</p>
                        <p class="text-[10px] text-slate-400">Management System</p>
                    </div>
                </div>
                <!-- Mobile Close Button -->
                <button onclick="toggleMobileSidebar()" class="lg:hidden text-slate-400 hover:text-white p-1">
                    <i class="fas fa-times text-lg"></i>
                </button>
                <!-- Desktop Collapse Button -->
                <button onclick="toggleSidebarCollapse()" class="sidebar-collapse-btn hidden lg:flex items-center justify-center w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 transition text-slate-400 hover:text-white">
                    <i class="fas fa-chevron-left text-xs transition-transform duration-300"></i>
                </button>
            </div>
            
            <!-- Navigation -->
            <nav class="sidebar-nav flex-1 overflow-y-auto py-4 px-3 min-h-0">
                @php
                    $user = auth()->user();
                    $groupedMenus = \App\Models\AdminMenuSetting::getMenusForUser($user);
                    
                    // Define section categories
                    $sectionConfig = [
                        'main' => [
                            'title' => null,
                            'icon' => null,
                            'groups' => [null], // Dashboard, Today's Summary
                            'color' => 'slate'
                        ],
                        'resort' => [
                            'title' => 'রিসোর্ট ম্যানেজমেন্ট',
                            'icon' => 'fas fa-hotel',
                            'groups' => ['Rooms Management', 'Room Bookings', 'Services'],
                            'color' => 'emerald'
                        ],
                        'convention' => [
                            'title' => 'কনভেনশন হল',
                            'icon' => 'fas fa-building-columns',
                            'groups' => ['Convention Halls'],
                            'color' => 'violet'
                        ],
                        'website' => [
                            'title' => 'ওয়েবসাইট',
                            'icon' => 'fas fa-globe',
                            'groups' => ['Website'],
                            'color' => 'sky'
                        ],
                        'reports' => [
                            'title' => 'রিপোর্টস',
                            'icon' => 'fas fa-chart-pie',
                            'groups' => ['Reports'],
                            'color' => 'amber'
                        ],
                        'system' => [
                            'title' => 'সিস্টেম',
                            'icon' => 'fas fa-cogs',
                            'groups' => ['System'],
                            'color' => 'rose'
                        ],
                    ];
                    
                    // Organize menus by sections
                    $organizedMenus = [];
                    foreach ($sectionConfig as $sectionKey => $config) {
                        $sectionMenus = [];
                        foreach ($config['groups'] as $groupName) {
                            if (isset($groupedMenus[$groupName])) {
                                foreach ($groupedMenus[$groupName] as $menu) {
                                    $sectionMenus[] = ['menu' => $menu, 'group' => $groupName];
                                }
                            }
                        }
                        if (!empty($sectionMenus)) {
                            $organizedMenus[$sectionKey] = [
                                'config' => $config,
                                'menus' => $sectionMenus
                            ];
                        }
                    }
                @endphp

                @foreach($organizedMenus as $sectionKey => $section)
                    @if($section['config']['title'])
                        <!-- Section Header -->
                        <div class="sidebar-section-header flex items-center justify-between px-3 py-2 mt-4 mb-1 rounded-lg hover:bg-slate-800/50 transition" onclick="toggleSection('{{ $sectionKey }}')">
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded-md bg-{{ $section['config']['color'] }}-500/20 flex items-center justify-center mr-2">
                                    <i class="{{ $section['config']['icon'] }} text-{{ $section['config']['color'] }}-400 text-xs"></i>
                                </div>
                                <span class="sidebar-text text-xs font-bold uppercase tracking-wider text-slate-400">{{ $section['config']['title'] }}</span>
                            </div>
                            <i class="fas fa-chevron-down chevron text-slate-500 text-xs sidebar-text"></i>
                        </div>
                    @endif
                    
                    <!-- Section Content -->
                    <div id="section-{{ $sectionKey }}" class="sidebar-section" style="max-height: 1000px;">
                        @php $lastGroup = null; @endphp
                        @foreach($section['menus'] as $item)
                            @if($item['group'] && $item['group'] !== $lastGroup && count($section['menus']) > 3)
                                <div class="sidebar-text text-[10px] text-slate-500 px-3 py-1 mt-2 uppercase tracking-wider">{{ $item['group'] }}</div>
                            @endif
                            @php $lastGroup = $item['group']; @endphp
                            
                            <a href="{{ route($item['menu']->route_name) }}" 
                               class="sidebar-menu-item group relative flex items-center px-3 py-2.5 mb-1 rounded-xl transition-all duration-200 text-sm
                                      @if(request()->routeIs($item['menu']->route_pattern)) 
                                          bg-gradient-to-r from-{{ $section['config']['color'] }}-500 to-{{ $section['config']['color'] }}-600 text-white shadow-lg shadow-{{ $section['config']['color'] }}-500/30
                                      @else 
                                          text-slate-300 hover:bg-slate-800/80 hover:text-white
                                      @endif">
                                <i class="{{ $item['menu']->menu_icon }} w-5 mr-3 text-sm @if(request()->routeIs($item['menu']->route_pattern)) text-white @else text-slate-400 group-hover:text-{{ $section['config']['color'] }}-400 @endif"></i>
                                <span class="sidebar-text">{{ $item['menu']->menu_label }}</span>
                                <span class="sidebar-tooltip">{{ $item['menu']->menu_label }}</span>
                            </a>
                        @endforeach
                    </div>
                @endforeach
                
                <!-- Logout -->
                <div class="border-t border-slate-700/50 mt-4 pt-4">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="sidebar-menu-item group relative flex items-center w-full px-3 py-2.5 rounded-xl text-slate-400 hover:bg-red-500/10 hover:text-red-400 transition text-sm">
                            <i class="fas fa-sign-out-alt w-5 mr-3 group-hover:text-red-400"></i>
                            <span class="sidebar-text">Logout</span>
                            <span class="sidebar-tooltip">Logout</span>
                        </button>
                    </form>
                </div>
            </nav>
            
            <!-- Sidebar Footer -->
            <div class="p-3 border-t border-slate-700/50">
                <div class="sidebar-text flex items-center justify-between px-2 py-2 bg-slate-800/50 rounded-xl">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-2">
                            <i class="fas fa-user text-white text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-white truncate max-w-[100px]">{{ auth()->user()->name ?? 'Admin' }}</p>
                            <p class="text-[10px] text-slate-400 capitalize">{{ auth()->user()->role ?? 'User' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div id="mainWrapper" class="main-wrapper flex-1 flex flex-col lg:ml-64 overflow-hidden w-full">
            <header class="bg-white border-b border-gray-200 z-20 shadow-sm">
                <div class="px-4 lg:px-6 py-3 lg:py-4 flex items-center justify-between">
                    <!-- Mobile Menu Button -->
                    <button onclick="toggleMobileSidebar()" class="lg:hidden text-gray-600 hover:text-gray-900 p-2 -ml-2">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-lg lg:text-xl font-bold text-gray-800 truncate flex-1 lg:flex-none text-center lg:text-left">@yield('header', 'Dashboard')</h2>
                    <div class="flex items-center space-x-3">
                        <!-- Notification Bell -->
                        <div class="relative" id="notificationWrapper">
                            <button onclick="toggleNotifications()" class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition">
                                <i class="fas fa-bell text-xl"></i>
                                <span id="notificationBadge" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center hidden">0</span>
                            </button>
                            <!-- Notification Dropdown -->
                            <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 max-h-[80vh] overflow-hidden">
                                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3 flex items-center justify-between">
                                    <h3 class="text-white font-bold"><i class="fas fa-bell mr-2"></i>নোটিফিকেশন</h3>
                                    <button onclick="markAllRead()" class="text-white/80 hover:text-white text-xs">সব পড়া হয়েছে</button>
                                </div>
                                <div id="notificationList" class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                                    <div class="p-4 text-center text-gray-500">
                                        <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                        <p class="text-sm">লোডিং...</p>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-2 text-center border-t">
                                    <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">সব দেখুন</a>
                                </div>
                            </div>
                        </div>
                        <div class="hidden sm:flex items-center bg-gradient-to-r from-slate-100 to-slate-50 rounded-full px-4 py-2 shadow-sm border border-slate-200">
                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mr-2 shadow">
                                <i class="fas fa-user text-white text-xs"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-700">{{ auth()->user()->name ?? 'Admin' }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto overflow-x-hidden bg-gray-50">
                <div class="p-3 sm:p-4 lg:p-8 main-content">
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
        // Sidebar collapse state
        let sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        
        // Initialize sidebar state on load
        document.addEventListener('DOMContentLoaded', function() {
            if (sidebarCollapsed && window.innerWidth >= 1024) {
                document.getElementById('sidebar').classList.add('sidebar-collapsed');
                document.getElementById('mainWrapper').classList.add('sidebar-collapsed');
            }
            
            // Restore section collapse states
            const sectionStates = JSON.parse(localStorage.getItem('sidebarSections') || '{}');
            Object.keys(sectionStates).forEach(key => {
                if (sectionStates[key]) {
                    const section = document.getElementById('section-' + key);
                    const header = document.querySelector(`[onclick="toggleSection('${key}')"]`);
                    if (section) {
                        section.classList.add('collapsed');
                        section.style.maxHeight = '0';
                    }
                    if (header) header.classList.add('collapsed');
                }
            });
        });
        
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
            
            if (!sidebar.classList.contains('-translate-x-full')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }
        
        function toggleSidebarCollapse() {
            const sidebar = document.getElementById('sidebar');
            const mainWrapper = document.getElementById('mainWrapper');
            
            sidebar.classList.toggle('sidebar-collapsed');
            mainWrapper.classList.toggle('sidebar-collapsed');
            
            sidebarCollapsed = sidebar.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
        }
        
        function toggleSection(sectionKey) {
            const section = document.getElementById('section-' + sectionKey);
            const header = document.querySelector(`[onclick="toggleSection('${sectionKey}')"]`);
            
            if (section.classList.contains('collapsed')) {
                section.classList.remove('collapsed');
                section.style.maxHeight = section.scrollHeight + 'px';
                header.classList.remove('collapsed');
            } else {
                section.classList.add('collapsed');
                section.style.maxHeight = '0';
                header.classList.add('collapsed');
            }
            
            // Save state
            const sectionStates = JSON.parse(localStorage.getItem('sidebarSections') || '{}');
            sectionStates[sectionKey] = section.classList.contains('collapsed');
            localStorage.setItem('sidebarSections', JSON.stringify(sectionStates));
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

        // =================== NOTIFICATION SYSTEM ===================
        let notificationOpen = false;
        
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            notificationOpen = !notificationOpen;
            
            if (notificationOpen) {
                dropdown.classList.remove('hidden');
                loadNotifications();
            } else {
                dropdown.classList.add('hidden');
            }
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('notificationWrapper');
            if (wrapper && !wrapper.contains(e.target) && notificationOpen) {
                document.getElementById('notificationDropdown').classList.add('hidden');
                notificationOpen = false;
            }
        });
        
        async function loadNotifications() {
            try {
                const response = await fetch('/admin/notifications', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                renderNotifications(data.notifications);
                updateBadge(data.unreadCount);
            } catch (error) {
                console.error('Error loading notifications:', error);
                document.getElementById('notificationList').innerHTML = '<div class="p-4 text-center text-red-500"><i class="fas fa-exclamation-circle mr-2"></i>লোড করতে সমস্যা হয়েছে</div>';
            }
        }
        
        function renderNotifications(notifications) {
            const container = document.getElementById('notificationList');
            
            if (notifications.length === 0) {
                container.innerHTML = '<div class="p-6 text-center text-gray-400"><i class="fas fa-bell-slash text-3xl mb-2"></i><p class="text-sm">কোনো নোটিফিকেশন নেই</p></div>';
                return;
            }
            
            let html = '';
            notifications.forEach(n => {
                const iconClass = getNotificationIcon(n.type);
                const bgClass = n.read ? 'bg-white' : 'bg-blue-50';
                html += `
                    <div class="p-3 hover:bg-gray-50 cursor-pointer ${bgClass}" onclick="handleNotificationClick('${n.type}', '${n.link || ''}')">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full ${iconClass.bg} flex items-center justify-center mr-3">
                                <i class="${iconClass.icon} ${iconClass.color}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900">${n.title}</p>
                                <p class="text-xs text-gray-500 mt-1">${n.message}</p>
                                <p class="text-xs text-gray-400 mt-1"><i class="fas fa-clock mr-1"></i>${n.time}</p>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }
        
        function getNotificationIcon(type) {
            const icons = {
                'checkin': { icon: 'fas fa-sign-in-alt', color: 'text-green-600', bg: 'bg-green-100' },
                'checkout': { icon: 'fas fa-sign-out-alt', color: 'text-orange-600', bg: 'bg-orange-100' },
                'due_payment': { icon: 'fas fa-exclamation-circle', color: 'text-red-600', bg: 'bg-red-100' },
                'convention_today': { icon: 'fas fa-building-columns', color: 'text-violet-600', bg: 'bg-violet-100' },
                'convention_upcoming': { icon: 'fas fa-calendar-check', color: 'text-purple-600', bg: 'bg-purple-100' },
                'new_booking': { icon: 'fas fa-plus-circle', color: 'text-blue-600', bg: 'bg-blue-100' },
                'default': { icon: 'fas fa-info-circle', color: 'text-gray-600', bg: 'bg-gray-100' }
            };
            return icons[type] || icons['default'];
        }
        
        function handleNotificationClick(type, link) {
            if (link) {
                window.location.href = link;
            }
        }
        
        function updateBadge(count) {
            const badge = document.getElementById('notificationBadge');
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
        
        function markAllRead() {
            fetch('/admin/notifications/mark-read', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => {
                updateBadge(0);
                loadNotifications();
            });
        }
        
        // Load notifications on page load and refresh every 60 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(loadNotifications, 1000);
            setInterval(loadNotifications, 60000);
        });
    </script>
</body>
</html>