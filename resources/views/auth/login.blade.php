<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('logo-image-feedtan-store.png') }}">
    <title>Login - FEEDTAN STORE</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 
                            50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',300:'#6ee7b7',
                            400:'#34d399',500:'#10b981',600:'#059669',700:'#047857',
                            800:'#065f46',900:'#064e3b',950:'#022c22' 
                        },
                    },
                    fontFamily: { sans:['Plus Jakarta Sans','sans-serif'] },
                    animation: { 
                        'fade-in':'fadeIn 0.4s ease',
                        'pulse-slow':'pulse 3s infinite',
                        'spin-slow':'spin 4s linear infinite'
                    },
                    keyframes: { 
                        fadeIn:{from:{opacity:0,transform:'translateY(10px)'},to:{opacity:1,transform:'translateY(0,0)'}} 
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950">
    <div class="h-full flex items-center justify-center p-4">
        <div 
            x-data="{ loading: false }"
            class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden animate-[fadeIn_0.5s_ease]"
        >
            <!-- Header -->
            <div class="bg-gradient-to-r from-primary-600 to-primary-800 p-8 text-center">
                <div class="w-20 h-20 mx-auto bg-white/20 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-leaf text-4xl text-white"></i>
                </div>
                <h1 class="text-2xl font-bold text-white">FEEDTAN STORE</h1>
                <p class="text-primary-100 text-sm mt-1">Sign in to your account</p>
            </div>

            <!-- Form -->
            <div class="p-8">
                <form 
                    method="POST" 
                    action="{{ route('login') }}"
                    @submit="loading = true"
                >
                    @csrf

                    <!-- Email -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-primary-900 mb-2">Email Address</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-primary-400">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}"
                                required 
                                autofocus
                                class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-primary-100 bg-primary-50 text-primary-900 placeholder-primary-400 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition-all"
                                placeholder="you@example.com"
                            >
                        </div>
                        @error('email')
                            <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-primary-900 mb-2">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-primary-400">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input 
                                x-data="{ show: false }"
                                :type="show ? 'text' : 'password'"
                                name="password" 
                                required 
                                class="w-full pl-12 pr-12 py-3 rounded-xl border-2 border-primary-100 bg-primary-50 text-primary-900 placeholder-primary-400 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition-all"
                                placeholder="••••••••"
                            >
                            <button 
                                type="button" 
                                @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-primary-400 hover:text-primary-600 transition-colors"
                            >
                                <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-primary-300 text-primary-600 focus:ring-primary-500">
                            <span class="text-sm text-primary-600">Remember me</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        :disabled="loading"
                        class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold py-3 rounded-xl hover:from-primary-700 hover:to-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all shadow-lg hover:shadow-xl disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <span x-show="!loading">Sign In</span>
                        <span x-show="loading" class="flex items-center gap-2">
                            <i class="fa-solid fa-spinner fa-spin"></i> Signing In...
                        </span>
                    </button>
                </form>

                
            </div>
        </div>
    </div>
</body>
</html>
