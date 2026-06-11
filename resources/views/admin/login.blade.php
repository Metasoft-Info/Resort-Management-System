<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Tufan Convention & Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 600: '#9333ea', 700: '#7e22ce', 800: '#6b21a8' },
                        accent: { 600: '#db2777' }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-700 via-primary-600 to-accent-600 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="relative z-10 w-full max-w-5xl mx-4">
        <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl overflow-hidden flex flex-col lg:flex-row min-h-[560px]">
            <!-- Left Side: Resort Info -->
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary-600 to-primary-800 text-white p-10 flex-col justify-between relative overflow-hidden">
                <!-- Decorative circles -->
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-white/10 rounded-full"></div>

                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl backdrop-blur-sm mb-6">
                        <i class="fas fa-hotel text-3xl text-white"></i>
                    </div>
                    <h1 class="text-4xl font-bold mb-2">Tufan Convention & Resort</h1>
                    <p class="text-primary-100 text-lg">Admin Dashboard</p>
                </div>

                <div class="relative z-10 space-y-5 mt-8 lg:mt-0">
                    @if($resortInfo->address ?? false)
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-primary-200 font-semibold uppercase tracking-wider">Address</p>
                                <p class="text-white mt-0.5">{{ $resortInfo->address }}</p>
                            </div>
                        </div>
                    @endif

                    @if($resortInfo->phone ?? false)
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone-alt text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-primary-200 font-semibold uppercase tracking-wider">Phone</p>
                                <p class="text-white mt-0.5">{{ $resortInfo->phone }}</p>
                            </div>
                        </div>
                    @endif

                    @if($resortInfo->email ?? false)
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-primary-200 font-semibold uppercase tracking-wider">Email</p>
                                <p class="text-white mt-0.5">{{ $resortInfo->email }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="relative z-10 mt-8 lg:mt-0">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-white/80 hover:text-white transition font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Website
                    </a>
                </div>
            </div>

            <!-- Right Side: Login Form -->
            <div class="w-full lg:w-1/2 p-8 sm:p-10 flex flex-col justify-center">
                <!-- Mobile-only header -->
                <div class="lg:hidden text-center mb-6">
                    <div class="inline-flex items-center justify-center w-14 h-14 bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl shadow-lg mb-3">
                        <i class="fas fa-hotel text-2xl text-white"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800">Tufan Convention & Resort</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Admin Dashboard</p>
                </div>

                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Welcome Back</h2>
                    <p class="text-gray-500 mt-1">Please sign in to access the admin panel</p>
                </div>

                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-start">
                        <i class="fas fa-exclamation-circle text-xl mr-3 mt-0.5"></i>
                        <div class="flex-1">
                            @foreach($errors->all() as $error)
                                <p class="font-semibold">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2" for="email">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition @error('email') border-red-500 @enderror"
                                placeholder="admin@tufanresort.com"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2" for="password">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition @error('password') border-red-500 @enderror"
                                placeholder="Enter your password"
                                required
                            >
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <label for="remember" class="ml-2 text-sm text-gray-600">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-bold py-3.5 px-6 rounded-xl transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
