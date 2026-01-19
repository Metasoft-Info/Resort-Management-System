<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Tufan Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 600: '#9333ea', 700: '#7e22ce' },
                        accent: { 600: '#db2777' }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-600 via-accent-600 to-pink-600 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    
    <div class="relative z-10 bg-white rounded-3xl shadow-2xl p-8 sm:p-12 max-w-md w-full mx-4">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-primary-600 to-accent-600 rounded-2xl shadow-xl mb-4">
                <i class="fas fa-hotel text-4xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-primary-600 to-accent-600 bg-clip-text text-transparent mb-2">Tufan Resort</h1>
            <p class="text-gray-700 font-semibold mb-1">প্রশাসক ড্যাশবোর্ড লগইন</p>
            <p class="text-sm text-gray-500">Admin Dashboard Login</p>
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

        <form method="POST" action="{{ route('admin.login') }}" class="space-y-6">
            @csrf
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    <i class="fas fa-envelope mr-2 text-primary-600"></i>Email / ইমেইল
                </label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email') }}"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 transition @error('email') border-red-500 @enderror"
                    placeholder="admin@tufanresort.com"
                    required
                    autofocus
                >
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    <i class="fas fa-lock mr-2 text-primary-600"></i>Password / পাসওয়ার্ড
                </label>
                <input 
                    type="password" 
                    name="password" 
                    id="password"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 transition @error('password') border-red-500 @enderror"
                    placeholder="••••••••"
                    required
                >
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                <label for="remember" class="ml-2 text-sm text-gray-700">
                    Remember me / আমাকে মনে রাখুন
                </label>
            </div>

            <button 
                type="submit"
                class="w-full bg-gradient-to-r from-primary-600 to-accent-600 hover:from-primary-700 hover:to-accent-700 text-white font-bold py-4 px-6 rounded-xl transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center"
            >
                <i class="fas fa-sign-in-alt mr-2"></i>
                Login / লগইন
            </button>
        </form>

        <div class="mt-8 text-center pt-6 border-t">
            <a href="{{ route('home') }}" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-semibold transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Website / ওয়েবসাইটে ফিরে যান
            </a>
        </div>
    </div>
</body>
</html>
