<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                        'spin-slow':'spin 4s linear infinite',
                        'slide-up': 'slideUp 0.5s ease-out'
                    },
                    keyframes: { 
                        fadeIn:{from:{opacity:0,transform:'translateY(10px)'},to:{opacity:1,transform:'translateY(0,0)'}},
                        slideUp: {from:{opacity:0,transform:'translateY(20px)'},to:{opacity:1,transform:'translateY(0)'}}
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-pattern {
            background-image: radial-gradient(circle at 20% 50%, rgba(16,185,129,0.1) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(6,78,59,0.15) 0%, transparent 50%);
        }
    </style>
</head>
<body class="h-full">
    <div class="h-full flex">
        <!-- Left Side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary-950 via-primary-800 to-primary-900 bg-pattern relative overflow-hidden">
            <!-- Decorative elements -->
            <div class="absolute top-20 left-20 w-32 h-32 bg-primary-500/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-20 w-48 h-48 bg-primary-400/20 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-primary-300/10 rounded-full blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col justify-center w-full p-12">
                <!-- Logo -->
                <div class="animate-[slide-up_0.5s_ease-out]">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-16 h-16 bg-gradient-to-br from-primary-400 to-primary-600 rounded-2xl flex items-center justify-center shadow-xl">
                            <i class="fa-solid fa-leaf text-3xl text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-4xl font-bold text-white">FEEDTAN</h2>
                            <p class="text-primary-200 text-lg font-medium">STORE</p>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="animate-[slide-up_0.5s_ease-out_0.1s] animate-fill-mode-forwards opacity-0">
                    <h1 class="text-5xl font-extrabold text-white mb-6 leading-tight">
                        Welcome Back to <br/>Your Business
                    </h1>
                    <p class="text-primary-100 text-lg leading-relaxed mb-10">
                        Streamline your inventory, manage sales, and grow your business with our powerful store management system.
                    </p>
                </div>

                <!-- Features -->
                <div class="animate-[slide-up_0.5s_ease-out_0.2s] animate-fill-mode-forwards opacity-0">
                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex items-center gap-4 p-4 bg-white/5 rounded-xl border border-white/10">
                            <div class="w-12 h-12 bg-primary-500/30 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-chart-line text-primary-300 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold">Real-time Analytics</h3>
                                <p class="text-primary-200 text-sm">Track your business performance</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 p-4 bg-white/5 rounded-xl border border-white/10">
                            <div class="w-12 h-12 bg-primary-500/30 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-boxes-stacked text-primary-300 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold">Inventory Management</h3>
                                <p class="text-primary-200 text-sm">Efficient stock control</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 p-4 bg-white/5 rounded-xl border border-white/10">
                            <div class="w-12 h-12 bg-primary-500/30 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-users text-primary-300 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold">Customer Management</h3>
                                <p class="text-primary-200 text-sm">Build lasting relationships</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="absolute bottom-12 left-12 animate-[slide-up_0.5s_ease-out_0.3s] animate-fill-mode-forwards opacity-0">
                    <p class="text-primary-200 text-sm">
                        © {{ date('Y') }} FEEDTAN STORE. All rights reserved.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50">
            <div 
                x-data="{ loading: false }"
                class="w-full max-w-md animate-[fadeIn_0.5s_ease]"
            >
                <!-- Mobile Logo -->
                <div class="lg:hidden flex items-center justify-center mb-8">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-400 to-primary-600 rounded-2xl flex items-center justify-center shadow-xl">
                        <i class="fa-solid fa-leaf text-3xl text-white"></i>
                    </div>
                </div>

                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-primary-900 mb-2">Welcome Back!</h1>
                    <p class="text-gray-500">Please enter your credentials to continue</p>
                </div>

                <!-- Form Card -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <form 
                        method="POST" 
                        action="{{ route('login') }}"
                        @submit="loading = true"
                    >
                        @csrf

                        <!-- Email -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>
                                <input 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}"
                                    required 
                                    autofocus
                                    class="w-full pl-12 pr-4 py-3.5 rounded-xl border-2 border-gray-200 bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-50 transition-all"
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
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input 
                                    x-data="{ show: false }"
                                    :type="show ? 'text' : 'password'"
                                    name="password" 
                                    required 
                                    class="w-full pl-12 pr-12 py-3.5 rounded-xl border-2 border-gray-200 bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-50 transition-all"
                                    placeholder="••••••••"
                                >
                                <button 
                                    type="button" 
                                    @click="show = !show"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                                >
                                    <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between mb-8">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember" class="w-4.5 h-4.5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <span class="text-sm text-gray-600">Remember me</span>
                            </label>
                            <a href="#" class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                                Forgot password?
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit"
                            :disabled="loading"
                            class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold py-3.5 rounded-xl hover:from-primary-700 hover:to-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-50 focus:ring-offset-2 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-70 disabled:cursor-not-allowed disabled:hover:translate-y-0 flex items-center justify-center gap-2"
                        >
                            <span x-show="!loading">Sign In</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <i class="fa-solid fa-spinner fa-spin"></i> Signing In...
                            </span>
                        </button>
                    </form>
                </div>

                <!-- Footer Note -->
                <p class="text-center text-gray-500 text-sm mt-6">
                    Need an account? <a href="#" class="text-primary-600 font-semibold hover:text-primary-700 transition-colors">Contact administrator</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
