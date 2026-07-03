<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('logo-image-feedtan-store.png') }}">
    <title>Sign In - FEEDTAN STORE</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <!-- Google Fonts: Fraunces (display) + Plus Jakarta Sans (body/UI) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forest:  { 950:'#07211a', 900:'#0b3327', 800:'#124a38', 700:'#186049' },
                        emerald: { 600:'#0f9d64', 500:'#16b876', 400:'#3cc98d' },
                        wheat:   { 400:'#e3ad5c', 300:'#edc789', 100:'#faf1de' },
                        ink:     { 900:'#142621', 600:'#516a62', 400:'#8aa199' },
                        mist:    { 50:'#f5f9f6', 100:'#eaf3ee' },
                    },
                    fontFamily: {
                        display: ['Fraunces', 'ui-serif', 'serif'],
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-display { font-family: 'Fraunces', serif; font-variation-settings: 'opsz' 60; }

        @keyframes riseIn {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .rise-in { opacity: 0; animation: riseIn 0.7s cubic-bezier(.22,.61,.36,1) forwards; }

        @keyframes grainFloat {
            0%   { transform: translateY(0) translateX(0) rotate(0deg); opacity: 0; }
            10%  { opacity: .55; }
            85%  { opacity: .4; }
            100% { transform: translateY(-140px) translateX(var(--drift,10px)) rotate(45deg); opacity: 0; }
        }
        .grain { position: absolute; animation: grainFloat linear infinite; }

        @keyframes glowSpin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
        .glow-spin { animation: glowSpin 22s linear infinite; }

        @keyframes shimmer {
            0%, 100% { opacity: .5; }
            50% { opacity: 1; }
        }
        .shimmer { animation: shimmer 3.2s ease-in-out infinite; }

        @media (prefers-reduced-motion: reduce) {
            .rise-in, .grain, .glow-spin, .shimmer { animation: none !important; opacity: 1 !important; transform: none !important; }
        }

        input:-webkit-autofill { -webkit-box-shadow: 0 0 0 1000px #f5f9f6 inset; -webkit-text-fill-color: #142621; }
    </style>
</head>
<body class="h-full bg-white text-ink-900">
    <div class="h-full lg:grid lg:grid-cols-[minmax(0,1.05fr)_minmax(0,1fr)]">

        <!-- LEFT: Brand panel -->
        <aside class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-gradient-to-br from-forest-950 via-forest-900 to-forest-800 px-14 py-12 text-white">

            <!-- ambient glow -->
            <div class="pointer-events-none absolute -top-32 -left-24 h-[26rem] w-[26rem] rounded-full bg-emerald-500/20 blur-3xl glow-spin"></div>
            <div class="pointer-events-none absolute bottom-[-8rem] right-[-6rem] h-[22rem] w-[22rem] rounded-full bg-wheat-400/10 blur-3xl"></div>

            <!-- drifting grain particles -->
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <i class="grain fa-solid fa-wheat-awn text-wheat-300/60 text-sm" style="left:12%; bottom:-5%; --drift:18px; animation-duration:9s; animation-delay:0s;"></i>
                <i class="grain fa-solid fa-wheat-awn text-wheat-300/50 text-xs" style="left:28%; bottom:-8%; --drift:-14px; animation-duration:12s; animation-delay:1.4s;"></i>
                <i class="grain fa-solid fa-leaf text-emerald-400/40 text-sm" style="left:47%; bottom:-6%; --drift:10px; animation-duration:10.5s; animation-delay:3s;"></i>
                <i class="grain fa-solid fa-wheat-awn text-wheat-300/60 text-base" style="left:63%; bottom:-10%; --drift:-20px; animation-duration:13s; animation-delay:.7s;"></i>
                <i class="grain fa-solid fa-leaf text-emerald-400/30 text-xs" style="left:78%; bottom:-4%; --drift:16px; animation-duration:11s; animation-delay:4.2s;"></i>
                <i class="grain fa-solid fa-wheat-awn text-wheat-300/40 text-sm" style="left:89%; bottom:-9%; --drift:-10px; animation-duration:9.5s; animation-delay:2.1s;"></i>
            </div>

            <!-- brand mark -->
            <div class="relative z-10 rise-in" style="animation-delay:.05s">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/15 backdrop-blur-sm">
                        <i class="fa-solid fa-wheat-awn text-xl text-wheat-300"></i>
                    </div>
                    <div class="leading-tight">
                        <p class="text-[15px] font-bold tracking-wide">FEEDTAN STORE</p>
                        <p class="text-[11px] uppercase tracking-[0.18em] text-emerald-300/80">Farm &amp; Feed Supply</p>
                    </div>
                </div>
            </div>

            <!-- headline block -->
            <div class="relative z-10 max-w-md">
                <p class="rise-in text-[11px] font-semibold uppercase tracking-[0.22em] text-emerald-300/90" style="animation-delay:.15s">Since 2011 &middot; Trusted by farms nationwide</p>
                <h1 class="rise-in font-display mt-4 text-[2.6rem] leading-[1.1] font-medium text-white" style="animation-delay:.25s">
                    Feed the work<br> that feeds everyone.
                </h1>
                <p class="rise-in mt-5 text-[15px] leading-relaxed text-mist-100/80" style="animation-delay:.4s">
                    Sign in to manage orders, track dispatch, and reorder livestock
                    and crop nutrition — all from one account built for growers.
                </p>

                <!-- feature rows -->
                <div class="rise-in mt-9 space-y-4" style="animation-delay:.55s">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-300">
                            <i class="fa-solid fa-truck-fast text-sm"></i>
                        </div>
                        <p class="text-sm text-mist-100/90">Same-day dispatch on in-stock feed</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-300">
                            <i class="fa-solid fa-seedling text-sm"></i>
                        </div>
                        <p class="text-sm text-mist-100/90">Nutrition specialists on every order</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-300">
                            <i class="fa-solid fa-shield-halved text-sm"></i>
                        </div>
                        <p class="text-sm text-mist-100/90">Secure, session-based account access</p>
                    </div>
                </div>
            </div>

            <!-- footer stat -->
            <div class="relative z-10 rise-in flex items-center gap-6 border-t border-white/10 pt-6" style="animation-delay:.7s">
                <div>
                    <p class="font-display text-2xl font-medium text-white">12,400<span class="text-wheat-300">+</span></p>
                    <p class="text-[11px] uppercase tracking-wide text-mist-100/60">Orders this season</p>
                </div>
                <div class="h-8 w-px bg-white/10"></div>
                <div>
                    <p class="font-display text-2xl font-medium text-white">2,000<span class="text-wheat-300">+</span></p>
                    <p class="text-[11px] uppercase tracking-wide text-mist-100/60">Farms served</p>
                </div>
            </div>
        </aside>

        <!-- RIGHT: Login panel -->
        <main class="relative flex min-h-full items-center justify-center bg-white px-6 py-12 sm:px-10">

            <!-- soft mesh backdrop, kept subtle since page is white -->
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(16,184,118,0.06),_transparent_45%),radial-gradient(circle_at_bottom_left,_rgba(227,173,92,0.06),_transparent_40%)]"></div>

            <div x-data="{ loading: false }" class="relative w-full max-w-sm">

                <!-- mobile-only brand mark -->
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-forest-900">
                        <i class="fa-solid fa-wheat-awn text-lg text-wheat-300"></i>
                    </div>
                    <div class="leading-tight">
                        <p class="text-sm font-bold text-forest-900">FEEDTAN STORE</p>
                        <p class="text-[10px] uppercase tracking-[0.18em] text-ink-400">Farm &amp; Feed Supply</p>
                    </div>
                </div>

                <div class="rise-in" style="animation-delay:.1s">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600">Welcome back</p>
                    <h2 class="font-display mt-2 text-3xl font-medium text-forest-900">Sign in to your account</h2>
                    <p class="mt-2 text-sm text-ink-600">Enter your details to access your dashboard.</p>
                </div>

                @if($entryGranted ?? false)
                <form
                    method="POST"
                    action="{{ route('login') }}"
                    @submit="loading = true"
                    class="rise-in mt-8"
                    style="animation-delay:.2s"
                >
                    @csrf
                    <input type="hidden" name="access" value="{{ $accessToken ?? '' }}">

                    <!-- Email -->
                    <div class="mb-5">
                        <label class="mb-2 block text-sm font-semibold text-forest-900">Email address</label>
                        <div class="group relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-ink-400 transition-colors group-focus-within:text-emerald-600">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                class="w-full rounded-xl border-2 border-mist-100 bg-mist-50 py-3 pl-12 pr-4 text-forest-900 placeholder-ink-400 transition-all focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/10"
                                placeholder="you@example.com"
                            >
                        </div>
                        @error('email')
                            <p class="mt-2 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-5">
                        <label class="mb-2 block text-sm font-semibold text-forest-900">Password</label>
                        <div x-data="{ show: false }" class="group relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-ink-400 transition-colors group-focus-within:text-emerald-600">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input
                                :type="show ? 'text' : 'password'"
                                name="password"
                                required
                                class="w-full rounded-xl border-2 border-mist-100 bg-mist-50 py-3 pl-12 pr-12 text-forest-900 placeholder-ink-400 transition-all focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/10"
                                placeholder="••••••••"
                            >
                            <button
                                type="button"
                                @click="show = !show"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-ink-400 transition-colors hover:text-emerald-600"
                            >
                                <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember me -->
                    <div class="mb-7 flex items-center justify-between">
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-2 border-mist-100 text-emerald-600 focus:ring-emerald-500/30">
                            <span class="text-sm text-ink-600">Remember me</span>
                        </label>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="loading"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-forest-900 py-3.5 font-semibold text-white shadow-lg shadow-forest-900/10 transition-all hover:bg-emerald-600 hover:shadow-xl hover:shadow-emerald-500/20 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 active:scale-[.98] disabled:cursor-not-allowed disabled:opacity-70"
                    >
                        <span x-show="!loading">Sign in</span>
                        <span x-show="loading" class="flex items-center gap-2">
                            <i class="fa-solid fa-spinner fa-spin"></i> Signing in&hellip;
                        </span>
                    </button>
                </form>
                @else
                <div class="rise-in mt-8 rounded-2xl border border-wheat-300 bg-wheat-100 p-6 text-center" style="animation-delay:.2s">
                    <div class="shimmer mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-wheat-300/60 text-wheat-400">
                        <i class="fa-solid fa-shield-halved text-xl"></i>
                    </div>
                    <h3 class="font-display text-lg font-medium text-forest-900">Secure entry required</h3>
                    <p class="mt-2 text-sm leading-relaxed text-ink-600">
                        Use a valid signed <code class="rounded bg-white/70 px-1.5 py-0.5 text-[13px] text-forest-900">/entry?...</code> link first.
                        It will generate a coded <code class="rounded bg-white/70 px-1.5 py-0.5 text-[13px] text-forest-900">/login?...</code> URL for sign-in.
                    </p>
                    <p class="mt-3 text-xs text-ink-400">Entry and login links are temporary and tied to the current access session.</p>
                </div>
                @endif

                <p class="rise-in mt-8 text-center text-xs text-ink-400" style="animation-delay:.3s">
                    &copy; {{ date('Y') }} FEEDTAN STORE. All rights reserved.
                </p>
            </div>
        </main>
    </div>
</body>
</html>