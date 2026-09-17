<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Store Closed - {{ $settings->store_name ?? 'Feedtan Store' }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
</style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('logo-image-feedtan-store.png') }}" alt="{{ $settings->store_name ?? 'Feedtan Store' }}" class="h-10 w-auto">
                <span class="text-xl font-bold text-gray-800">{{ $settings->store_name ?? 'Feedtan Store' }}</span>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#013618] text-white rounded-full text-sm font-semibold hover:bg-[#014a22] transition">
                        <i class="fa-solid fa-gauge-high"></i> {{ __('Dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#013618] text-white rounded-full text-sm font-semibold hover:bg-[#014a22] transition">
                        <i class="fa-solid fa-right-to-bracket"></i> {{ __('Login') }}
                    </a>
                    <a href="{{ route('entry') }}" class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 border border-gray-300 rounded-full text-sm font-semibold hover:bg-gray-50 transition">
                        <i class="fa-solid fa-key"></i> {{ __('Staff Entry') }}
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1 flex items-center justify-center px-4 py-16">
        <div class="max-w-lg w-full bg-white rounded-2xl shadow-lg border border-gray-200 p-10 text-center">
            <div class="mx-auto w-20 h-20 rounded-full bg-amber-100 flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-3">Our Online Store is Currently Closed</h1>
            <p class="text-gray-600 mb-2">Online ordering is temporarily unavailable. Please check back soon.</p>
            @if(($settings->store_phone ?? null) || ($settings->store_email ?? null))
                <div class="mt-6 pt-6 border-t border-gray-200 space-y-2 text-sm text-gray-600">
                    @if($settings->store_phone)
                        <p><span class="font-semibold text-gray-700">Call us:</span> {{ $settings->store_phone }}</p>
                    @endif
                    @if($settings->store_email)
                        <p><span class="font-semibold text-gray-700">Email:</span> {{ $settings->store_email }}</p>
                    @endif
                    @if($settings->store_address)
                        <p><span class="font-semibold text-gray-700">Find us at:</span> {{ $settings->store_address }}</p>
                    @endif
                </div>
            @endif
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 py-6">
        <div class="max-w-7xl mx-auto px-4 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} {{ $settings->store_name ?? 'Feedtan Store' }}. All rights reserved.
        </div>
    </footer>
</body>
</html>
