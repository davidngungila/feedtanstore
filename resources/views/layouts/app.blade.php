<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('logo-image-feedtan-store.png') }}" />
    <title>FEEDTAN STORE – Store Management System</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet"/>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',300:'#6ee7b7',400:'#34d399',500:'#10b981',600:'#059669',700:'#047857',800:'#065f46',900:'#064e3b',950:'#022c22' },
                    },
                    fontFamily: { sans:['Plus Jakarta Sans','sans-serif'], mono:['JetBrains Mono','monospace'] },
                    animation: { 'fade-in':'fadeIn 0.4s ease','slide-in':'slideIn 0.3s ease','count-up':'countUp 1.5s ease','pulse-slow':'pulse 3s infinite','spin-slow':'spin 4s linear infinite','bounce-subtle':'bounceSubtle 2s infinite' },
                    keyframes: { fadeIn:{from:{opacity:0,transform:'translateY(10px)'},to:{opacity:1,transform:'translateY(0)'}}, slideIn:{from:{opacity:0,transform:'translateX(-20px)'},to:{opacity:1,transform:'translateX(0)'}}, bounceSubtle:{from:{transform:'translateY(0)'},'50%':{transform:'translateY(-4px)'},to:{transform:'translateY(0)'}} }
                }
            }
        }
    </script>

    <style>
        /* ============================================================
           GLOBAL STYLES
           ============================================================ */
        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; overflow: hidden; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #10b981; border-radius: 10px; }

        /* ============================================================
           MAIN THEME
           ============================================================ */
        body { background: #f0fdf4; color: #064e3b; }
        .main-bg { background: #f0fdf4; }
        .card { background: #ffffff; border: 1px solid #d1fae5; box-shadow: 0 2px 12px rgba(6,78,59,0.08); }
        .sidebar-bg { background: #064e3b; }
        .navbar-bg { background: #ffffff; border-bottom: 1px solid #d1fae5; }
        .text-main { color: #064e3b; }
        .text-sub { color: #6b7280; }
        .input-field { background: #f9fafb; border: 1px solid #d1fae5; color: #064e3b; }
        .table-row:hover { background: #ecfdf5; }
        .sidebar-item:hover { background: rgba(255,255,255,0.1); }

        /* Sidebar */
        .sidebar { transition: width 0.3s cubic-bezier(0.4,0,0.2,1), transform 0.3s cubic-bezier(0.4,0,0.2,1); }
        .sidebar-collapsed { width: 64px; }
        .sidebar-expanded { width: 260px; }

        /* Dropdown menus in sidebar */
        .sidebar-dropdown { max-height: 0; overflow: hidden; transition: max-height 0.35s ease; }
        .sidebar-dropdown.open { max-height: 1000px; }

        /* Page transitions */
        .page { display: none; animation: fadeIn 0.35s ease; }
        .page.active { display: block; }

        @keyframes fadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        @keyframes countUp { from { transform:translateY(8px); opacity:0; } to { transform:translateY(0); opacity:1; } }
        @keyframes slideInLeft { from { transform:translateX(-100%); } to { transform:translateX(0); } }
        @keyframes slideOutLeft { from { transform:translateX(0); } to { transform:translateX(-100%); } }

        /* Glassmorphism cards */
        .glass { backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
        .glass { background: rgba(255,255,255,0.85); border: 1px solid rgba(209,250,229,0.8); }

        /* Status badges */
        .badge { display:inline-flex; align-items:center; padding:2px 10px; border-radius:999px; font-size:11px; font-weight:700; letter-spacing:0.4px; }
        .badge-green { background:#d1fae5; color:#065f46; }
        .badge-red { background:#fee2e2; color:#991b1b; }
        .badge-yellow { background:#fef9c3; color:#854d0e; }
        .badge-blue { background:#dbeafe; color:#1e40af; }
        .badge-gray { background:#f3f4f6; color:#4b5563; }

        /* Animated counter */
        .counter { display:inline-block; }

        /* Tables */
        .data-table { width:100%; border-collapse:collapse; }
        .data-table th { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; padding:10px 14px; }
        .data-table td { padding:10px 14px; font-size:13px; }
        .data-table tbody tr { transition:background 0.15s; border-bottom:1px solid transparent; }
        .data-table th { color:#065f46; background:#ecfdf5; border-bottom:1px solid #d1fae5; }
        .data-table tbody tr { border-bottom-color:#f0fdf4; }
        .data-table tbody tr:hover { background:#f0fdf4; }

        /* Forms */
        .form-input {
            width:100%; padding:9px 14px; border-radius:8px; font-size:13px;
            outline:none; transition:border-color 0.2s, box-shadow 0.2s;
            font-family:'Plus Jakarta Sans',sans-serif;
        }
        .form-input:focus { border-color:#10b981; box-shadow:0 0 0 3px rgba(16,185,129,0.15); }
        .form-label { font-size:12px; font-weight:600; margin-bottom:4px; display:block; }

        /* Notification dot */
        .notif-dot { width:8px;height:8px;border-radius:50%;background:#ef4444;position:absolute;top:2px;right:2px;animation:pulse 1.5s infinite; }

        /* Loader */
        .skeleton { animation:skeleton-loading 1.2s linear infinite alternate; }
        .skeleton { background:linear-gradient(90deg,#f0fdf4 0%,#d1fae5 50%,#f0fdf4 100%); }
        @keyframes skeleton-loading { from{background-position:0 0;} to{background-position:100% 0;} }

        /* ============================================================
           STAT CARDS
           ============================================================ */
        .stat-card {
            border-radius:16px; padding:20px 22px;
            transition:transform 0.2s, box-shadow 0.2s;
            position:relative; overflow:hidden;
        }
        .stat-card:hover { transform:translateY(-3px); box-shadow:0 12px 32px rgba(16,185,129,0.18); }
        .stat-card .icon-wrap { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px; }
        .stat-card .bg-blob { position:absolute;right:-20px;top:-20px;width:100px;height:100px;border-radius:50%;opacity:0.08; }

        /* Modal */
        .modal-overlay {
            position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:999;
            display:flex;align-items:center;justify-content:center;
            backdrop-filter:blur(4px);animation:fadeIn 0.2s ease;
        }
        .modal-box {
            border-radius:16px;width:90%;max-width:600px;max-height:90vh;overflow-y:auto;
            animation:fadeIn 0.3s ease;
        }

        /* ============================================================
           CHART WRAPPERS
           ============================================================ */
        .chart-wrapper { position:relative; }

        /* Toast */
        .toast-container { position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px; }
        .toast {
            display:flex;align-items:center;gap:10px;padding:12px 18px;border-radius:12px;
            font-size:13px;font-weight:500;box-shadow:0 8px 24px rgba(0,0,0,0.2);
            animation:fadeIn 0.3s ease;
            min-width:240px;
        }
        .toast-success { background:#064e3b;color:#6ee7b7;border:1px solid #065f46; }
        .toast-error { background:#450a0a;color:#fca5a5;border:1px solid #991b1b; }
        .toast-info { background:#172554;color:#93c5fd;border:1px solid #1e40af; }

        /* Progress bars */
        .progress-bar { height:6px;border-radius:99px;background:#d1fae5;overflow:hidden; }
        .progress-fill { height:100%;border-radius:99px;background:linear-gradient(90deg,#10b981,#34d399);transition:width 1s ease; }

        /* Mobile overlay */
        .mobile-overlay { display:none; }
        @media(max-width:768px) {
            .mobile-overlay { display:block; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:40; }
            .sidebar { position:fixed!important; z-index:50; height:100vh; top:0; left:0; transform:translateX(-100%); }
            .sidebar.mobile-open { transform:translateX(0)!important; }
            .sidebar-expanded { width:260px!important; }
            .main-content { margin-left:0!important; }
        }
        @media(max-width:1024px) {
            .sidebar { position:fixed!important; z-index:50; height:100vh; top:0; left:0; transform:translateX(-100%); }
            .sidebar.mobile-open { transform:translateX(0)!important; width:260px!important; }
            .main-content { margin-left:0!important; }
        }

        /* Role badge */
        .role-tag { padding:3px 10px;border-radius:999px;font-size:10px;font-weight:700;letter-spacing:0.5px; }
        .role-admin { background:#059669;color:#fff; }
        .role-manager { background:#3b82f6;color:#fff; }
        .role-teller { background:#f59e0b;color:#fff; }
        .role-member { background:#6366f1;color:#fff; }
        .role-auditor { background:#ef4444;color:#fff; }
    </style>
</head>
<body class="h-full bg-[#f0fdf4]">

<!-- ============================================================
     TOAST CONTAINER
     ============================================================ -->
<div class="toast-container" id="toastContainer"></div>

<!-- ============================================================
     MAIN APP
     ============================================================ -->
@php
    $activeSection = null;
    if (request()->routeIs('dashboard.*')) {
        $activeSection = 'analytics';
    } elseif (request()->routeIs('sales.*')) {
        $activeSection = 'sales';
    } elseif (request()->routeIs('inventory.*')) {
        $activeSection = 'inventory';
    } elseif (request()->routeIs('purchasing.*')) {
        $activeSection = 'purchasing';
    } elseif (request()->routeIs('customers.*')) {
        $activeSection = 'customers';
    } elseif (request()->routeIs('finance.*')) {
        $activeSection = 'finance';
    } elseif (request()->routeIs('online.*')) {
        $activeSection = 'online';
    } elseif (request()->routeIs('store.*')) {
        $activeSection = 'store';
    } elseif (request()->routeIs('hr.*')) {
        $activeSection = 'hr';
    } elseif (request()->routeIs('security.*')) {
        $activeSection = 'security';
    } elseif (request()->routeIs('marketing.*')) {
        $activeSection = 'marketing';
    }
@endphp
<div x-data="{
    sidebarOpen: false,
    sidebarCollapsed: false,
    loading: false,
    activeSection: {{ $activeSection ? "'$activeSection'" : 'null' }},
    isCashier: {{ (Auth::check() && Auth::user()->role === 'cashier') ? 'true' : 'false' }},
    currentTime: '',
    currentUser: {
        name: '{{ Auth::check() ? Auth::user()->name : 'Admin User' }}',
        email: '{{ Auth::check() ? Auth::user()->email : 'admin@feedtan.co.tz' }}',
        role: '{{ Auth::check() ? Auth::user()->role : 'admin' }}',
        roleLabel: '{{ Auth::check() ? ucfirst(Auth::user()->role) : 'Administrator' }}',
        branch: 'Main Store'
    },
    toggleSection(section) {
        if (this.activeSection === section) {
            this.activeSection = null;
        } else {
            this.activeSection = section;
        }
    },
    init() {
        this.updateTime();
        setInterval(() => this.updateTime(), 1000);
    },
    updateTime() {
        const now = new Date();
        this.currentTime = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
}" class="flex h-screen overflow-hidden">

  <!-- Loading Overlay -->
  <div x-show="loading" x-transition:enter="transition-opacity duration-300"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition-opacity duration-300"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       class="fixed inset-0 z-[9999] bg-white/80 backdrop-blur-md flex items-center justify-center">
    <div class="text-center">
      <div class="w-20 h-20 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin mx-auto mb-4"></div>
      <p class="text-primary-700 font-semibold text-lg">Loading...</p>
      <p class="text-primary-500 text-sm mt-2">Please wait...</p>
    </div>
  </div>

  <!-- Mobile Overlay -->
  <div x-show="sidebarOpen" @click="sidebarOpen=false"
       class="fixed inset-0 bg-black/60 z-40 lg:hidden backdrop-blur-sm" x-transition></div>

  <!-- ============================================================
       SIDEBAR
       ============================================================ -->
  <template x-if="!isCashier">
    <aside :class="[sidebarOpen?'translate-x-0':'lg:translate-x-0 -translate-x-full','sidebar sidebar-bg fixed lg:relative h-screen z-50 flex flex-col transition-all duration-300',sidebarCollapsed&&window.innerWidth>=1024?'w-16':'w-[260px]']"
           class="sidebar-bg">

    <!-- Sidebar Header -->
    <div class="flex items-center justify-between p-4 border-b border-white/20 flex-shrink-0">
      <div class="flex items-center gap-3" x-show="!sidebarCollapsed || window.innerWidth<1024">
        <img src="{{ asset('feedtanstorelogo.png') }}" alt="FEEDTAN STORE" class="w-full h-12 rounded-lg flex-shrink-0 object-contain" style="max-width: 180px; filter: brightness(0) invert(1);">
      </div>
      <div x-show="sidebarCollapsed && window.innerWidth>=1024" class="w-10 h-10 rounded-lg flex items-center justify-center mx-auto">
        <img src="{{ asset('feedtanstorelogo.png') }}" alt="FEEDTAN STORE" class="w-full h-full rounded-lg object-contain" style="filter: brightness(0) invert(1);">
      </div>
      <button @click="sidebarCollapsed=!sidebarCollapsed" class="text-primary-300 hover:text-white transition-colors hidden lg:block">
        <i :class="sidebarCollapsed?'fa-solid fa-chevron-right':'fa-solid fa-chevron-left'" class="text-xs"></i>
      </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">

      <!-- Dashboard -->
      <a href="{{ route('dashboard') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-150 group {{ request()->routeIs('dashboard') ? 'bg-primary-600 text-white' : 'text-primary-200 hover:bg-white/10 hover:text-white' }}">
        <i class="fa-solid fa-gauge-high w-4 text-center flex-shrink-0"></i>
        <span x-show="!sidebarCollapsed" class="font-medium">Dashboard</span>
      </a>

      <!-- Analytics -->
      <div>
        <button @click="toggleSection('analytics')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('dashboard.*') ? 'bg-white/10 text-white' : 'text-primary-200 hover:bg-white/10 hover:text-white' }}">
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-chart-line w-4 text-center flex-shrink-0"></i>
            <span x-show="!sidebarCollapsed" class="font-medium">Analytics</span>
          </div>
          <i x-show="!sidebarCollapsed" :class="activeSection === 'analytics'?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[10px] text-primary-400"></i>
        </button>
        <div :class="activeSection === 'analytics'?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-300 ml-3" x-show="!sidebarCollapsed">
          <a href="{{ route('dashboard.sales') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('dashboard.sales') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Sales Analytics
          </a>
          <a href="{{ route('dashboard.online-orders') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('dashboard.online-orders') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Online Orders Analytics
          </a>
          <a href="{{ route('dashboard.purchases') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('dashboard.purchases') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Purchases Analytics
          </a>
          <a href="{{ route('dashboard.inventory') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('dashboard.inventory') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Inventory Analytics
          </a>
        </div>
      </div>

      <!-- Sales Management -->
      <div>
        <button @click="toggleSection('sales')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('sales.*') ? 'bg-white/10 text-white' : 'text-primary-200 hover:bg-white/10 hover:text-white' }}">
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-cash-register w-4 text-center flex-shrink-0"></i>
            <span x-show="!sidebarCollapsed" class="font-medium">Sales Management</span>
          </div>
          <i x-show="!sidebarCollapsed" :class="activeSection === 'sales'?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[10px] text-primary-400"></i>
        </button>
        <div :class="activeSection === 'sales'?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-300 ml-3" x-show="!sidebarCollapsed">
          <a href="{{ route('sales.new') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('sales.new') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            New Sale (POS)
          </a>
          <a href="{{ route('sales.history') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('sales.history') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Sales History
          </a>
          <a href="{{ route('sales.returns') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('sales.returns') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Sales Returns
          </a>
          <a href="{{ route('sales.cancelled') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('sales.cancelled') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Cancelled Sales
          </a>
          <a href="{{ route('sales.discounts') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('sales.discounts') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Discounts Approval
          </a>

          <a href="{{ route('sales.receipts') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('sales.receipts') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Customer Receipts
          </a>
          <a href="{{ route('sales.shifts') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('sales.shifts') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Shift Management
          </a>
        </div>
      </div>

      <!-- Inventory Management -->
      <div>
        <button @click="toggleSection('inventory')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('inventory.*') ? 'bg-white/10 text-white' : 'text-primary-200 hover:bg-white/10 hover:text-white' }}">
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-boxes-stacked w-4 text-center flex-shrink-0"></i>
            <span x-show="!sidebarCollapsed" class="font-medium">Inventory Management</span>
          </div>
          <i x-show="!sidebarCollapsed" :class="activeSection === 'inventory'?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[10px] text-primary-400"></i>
        </button>
        <div :class="activeSection === 'inventory'?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-300 ml-3" x-show="!sidebarCollapsed">
          <a href="{{ route('inventory.products') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('inventory.products') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Products
          </a>
          <a href="{{ route('inventory.categories') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('inventory.categories') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Categories
          </a>
          <a href="{{ route('inventory.brands') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('inventory.brands') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Brands
          </a>
          <a href="{{ route('inventory.units') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('inventory.units') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Units of Measure
          </a>
          <a href="{{ route('inventory.receiving') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('inventory.receiving') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Stock Receiving
          </a>
          <a href="{{ route('inventory.adjustments') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('inventory.adjustments') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Stock Adjustment
          </a>
          <a href="{{ route('inventory.transfers') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('inventory.transfers') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Stock Transfer
          </a>
          <a href="{{ route('inventory.count') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('inventory.count') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Stock Count
          </a>
          <a href="{{ route('inventory.low-stock') }}" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('inventory.low-stock') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <div class="flex items-center gap-2">
              <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
              Low Stock Alert
            </div>
            @if($lowStockCount > 0)
              <span class="bg-yellow-500 text-white text-[9px] font-bold rounded-full px-1.5 py-0.5">{{ $lowStockCount }}</span>
            @endif
          </a>
          <a href="{{ route('inventory.expiry') }}" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('inventory.expiry') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <div class="flex items-center gap-2">
              <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
              Expiry Management
            </div>
            @if($expiringCount > 0 || $expiredCount > 0)
              <span class="bg-orange-500 text-white text-[9px] font-bold rounded-full px-1.5 py-0.5">{{ $expiringCount + $expiredCount }}</span>
            @endif
          </a>
          <a href="{{ route('inventory.damaged') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('inventory.damaged') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Damaged Goods
          </a>
          <a href="{{ route('inventory.reports') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('inventory.reports') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Inventory Reports
          </a>
          <a href="{{ route('inventory.barcodes') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('inventory.barcodes') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Product Barcodes
          </a>
        </div>
      </div>

      <!-- Purchasing & Suppliers -->
      <div>
        <button @click="toggleSection('purchasing')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('purchasing.*') ? 'bg-white/10 text-white' : 'text-primary-200 hover:bg-white/10 hover:text-white' }}">
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-truck-fast w-4 text-center flex-shrink-0"></i>
            <span x-show="!sidebarCollapsed" class="font-medium">Purchasing & Suppliers</span>
          </div>
          <i x-show="!sidebarCollapsed" :class="activeSection === 'purchasing'?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[10px] text-primary-400"></i>
        </button>
        <div :class="activeSection === 'purchasing'?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-300 ml-3" x-show="!sidebarCollapsed">
          <a href="{{ route('purchasing.suppliers') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('purchasing.suppliers') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Suppliers
          </a>
          <a href="{{ route('purchasing.orders') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('purchasing.orders') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Purchase Orders
          </a>
          <a href="{{ route('purchasing.grn') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('purchasing.grn') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Goods Received (GRN)
          </a>
          <a href="{{ route('purchasing.payments') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('purchasing.payments') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Supplier Payments
          </a>
          <a href="{{ route('purchasing.reports') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('purchasing.reports') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Purchase Reports
          </a>
        </div>
      </div>

      <!-- Customers -->
      <div>
        <button @click="toggleSection('customers')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('customers.*') ? 'bg-white/10 text-white' : 'text-primary-200 hover:bg-white/10 hover:text-white' }}">
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-users w-4 text-center flex-shrink-0"></i>
            <span x-show="!sidebarCollapsed" class="font-medium">Customers</span>
          </div>
          <i x-show="!sidebarCollapsed" :class="activeSection === 'customers'?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[10px] text-primary-400"></i>
        </button>
        <div :class="activeSection === 'customers'?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-300 ml-3" x-show="!sidebarCollapsed">
          <a href="{{ route('customers.list') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('customers.list') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Customer List
          </a>
          <a href="{{ route('customers.groups') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('customers.groups') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Customer Groups
          </a>
          <a href="{{ route('customers.loyalty') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('customers.loyalty') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Loyalty Program
          </a>
          <a href="{{ route('customers.credit') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('customers.credit') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Customer Credit
          </a>
          <a href="{{ route('customers.history') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('customers.history') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Customer History
          </a>
        </div>
      </div>

      <!-- Finance -->
      <div>
        <button @click="toggleSection('finance')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('finance.*') ? 'bg-white/10 text-white' : 'text-primary-200 hover:bg-white/10 hover:text-white' }}">
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-sack-dollar w-4 text-center flex-shrink-0"></i>
            <span x-show="!sidebarCollapsed" class="font-medium">Finance</span>
          </div>
          <i x-show="!sidebarCollapsed" :class="activeSection === 'finance'?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[10px] text-primary-400"></i>
        </button>
        <div :class="activeSection === 'finance'?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-300 ml-3" x-show="!sidebarCollapsed">
          <a href="{{ route('finance.dashboard') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('finance.dashboard') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Dashboard
          </a>
          <a href="{{ route('finance.payments') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('finance.payments') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Payments
          </a>
          <a href="{{ route('finance.expenses') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('finance.expenses') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Expenses
          </a>
          <a href="{{ route('finance.income') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('finance.income') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Income
          </a>
          <a href="{{ route('finance.cash') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('finance.cash') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Cash Management
          </a>
          <a href="{{ route('finance.bank') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('finance.bank') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Bank Accounts
          </a>
          <a href="{{ route('finance.mobile-money-reconciliation') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('finance.mobile-money-reconciliation') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Mobile Money Reconciliation
          </a>
          <a href="{{ route('finance.capital') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('finance.capital') || request()->routeIs('finance.capital.*') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Capital
          </a>
          <a href="{{ route('finance.accounts-receivable') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('finance.accounts-receivable') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Accounts Receivable
          </a>
          <a href="{{ route('finance.accounts-payable') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('finance.accounts-payable') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Accounts Payable
          </a>
          <a href="{{ route('finance.transactions') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('finance.transactions') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Transactions
          </a>
          <a href="{{ route('finance.tax-management') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('finance.tax-management') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Tax Management
          </a>
          <a href="{{ route('finance.budgets') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('finance.budgets') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Budgets
          </a>
          <a href="{{ route('finance.assets') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('finance.assets') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Assets
          </a>
          <a href="{{ route('finance.shareholders') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('finance.shareholders') || request()->routeIs('finance.shareholders.*') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Shareholders
          </a>
          <a href="{{ route('finance.balance-sheet') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('finance.balance-sheet') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Balance Sheet
          </a>
          <a href="{{ route('finance.income-statement') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('finance.income-statement') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Income Statement
          </a>
          <a href="{{ route('finance.reports') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('finance.reports') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Financial Reports
          </a>
          <a href="{{ route('finance.settings') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('finance.settings') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Settings
          </a>
        </div>
      </div>

      <!-- Online Sales -->
      <div>
        <button @click="toggleSection('online')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('online.*') ? 'bg-white/10 text-white' : 'text-primary-200 hover:bg-white/10 hover:text-white' }}">
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-globe w-4 text-center flex-shrink-0"></i>
            <span x-show="!sidebarCollapsed" class="font-medium">Online Sales</span>
          </div>
          <i x-show="!sidebarCollapsed" :class="activeSection === 'online'?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[10px] text-primary-400"></i>
        </button>
        <div :class="activeSection === 'online'?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-300 ml-3" x-show="!sidebarCollapsed">
          <a href="{{ route('online.orders') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('online.orders') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Online Orders
          </a>
          <a href="{{ route('online.catalog') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('online.catalog') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Product Catalog
          </a>
          <a href="{{ route('online.carousel') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('online.carousel') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Carousel Management
          </a>
          <a href="{{ route('online.delivery') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('online.delivery') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Delivery Management
          </a>
          <a href="{{ route('online.riders') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('online.riders') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Delivery Riders
          </a>
          <a href="{{ route('online.payments') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('online.payments') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Online Payments
          </a>
          <a href="{{ route('online.tracking') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('online.tracking') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Order Tracking
          </a>
        </div>
      </div>

      <!-- Store Management -->
      <div>
        <button @click="toggleSection('store')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('store.*') ? 'bg-white/10 text-white' : 'text-primary-200 hover:bg-white/10 hover:text-white' }}">
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-store w-4 text-center flex-shrink-0"></i>
            <span x-show="!sidebarCollapsed" class="font-medium">Store Management</span>
          </div>
          <i x-show="!sidebarCollapsed" :class="activeSection === 'store'?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[10px] text-primary-400"></i>
        </button>
        <div :class="activeSection === 'store'?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-300 ml-3" x-show="!sidebarCollapsed">
          <a href="{{ route('store.profile') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('store.profile') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Store Profile
          </a>
          <a href="{{ route('store.branches') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('store.branches') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Branches
          </a>
          <a href="{{ route('store.locations') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('store.locations') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Store Locations
          </a>
          <a href="{{ route('store.warehouses') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('store.warehouses') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Warehouses
          </a>
          <a href="{{ route('store.settings') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('store.settings') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Store Settings
          </a>
        </div>
      </div>

      <!-- Employees & HR -->
      <div>
        <button @click="toggleSection('hr')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('hr.*') ? 'bg-white/10 text-white' : 'text-primary-200 hover:bg-white/10 hover:text-white' }}">
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-user-tie w-4 text-center flex-shrink-0"></i>
            <span x-show="!sidebarCollapsed" class="font-medium">Employees & HR</span>
          </div>
          <i x-show="!sidebarCollapsed" :class="activeSection === 'hr'?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[10px] text-primary-400"></i>
        </button>
        <div :class="activeSection === 'hr'?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-300 ml-3" x-show="!sidebarCollapsed">
          <a href="{{ route('hr.employees') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('hr.employees') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Employees
          </a>
          <a href="{{ route('hr.roles') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('hr.roles') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Roles & Permissions
          </a>
          <a href="{{ route('hr.attendance') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('hr.attendance') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Attendance
          </a>
          <a href="{{ route('hr.shifts') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('hr.shifts') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Shifts
          </a>
          <a href="{{ route('hr.activity') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('hr.activity') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Activity Logs
          </a>
        </div>
      </div>

      <!-- Security & Control -->
      <div>
        <button @click="toggleSection('security')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('security.*') ? 'bg-white/10 text-white' : 'text-primary-200 hover:bg-white/10 hover:text-white' }}">
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-shield-halved w-4 text-center flex-shrink-0"></i>
            <span x-show="!sidebarCollapsed" class="font-medium">Security & Control</span>
          </div>
          <i x-show="!sidebarCollapsed" :class="activeSection === 'security'?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[10px] text-primary-400"></i>
        </button>
        <div :class="activeSection === 'security'?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-300 ml-3" x-show="!sidebarCollapsed">
          <a href="{{ route('security.users') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('security.users') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            User Accounts
          </a>
          <a href="{{ route('security.access') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('security.access') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Access Control
          </a>
          <a href="{{ route('security.audit') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('security.audit') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Audit Logs
          </a>
          <a href="{{ route('security.logins') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('security.logins') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Login History
          </a>
          <a href="{{ route('security.devices') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('security.devices') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Device Management
          </a>
          <a href="{{ route('security.settings') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('security.settings') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Security Settings
          </a>
        </div>
      </div>

      <!-- Marketing -->
      <div>
        <button @click="toggleSection('marketing')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('marketing.*') ? 'bg-white/10 text-white' : 'text-primary-200 hover:bg-white/10 hover:text-white' }}">
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-bullhorn w-4 text-center flex-shrink-0"></i>
            <span x-show="!sidebarCollapsed" class="font-medium">Marketing</span>
          </div>
          <i x-show="!sidebarCollapsed" :class="activeSection === 'marketing'?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[10px] text-primary-400"></i>
        </button>
        <div :class="activeSection === 'marketing'?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-300 ml-3" x-show="!sidebarCollapsed">
          <a href="{{ route('marketing.promotions') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('marketing.promotions') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Promotions
          </a>
          <a href="{{ route('marketing.discounts') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('marketing.discounts') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Discounts
          </a>
          <a href="{{ route('marketing.ads') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('marketing.ads') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Advertisements
          </a>
          <a href="{{ route('marketing.campaigns') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('marketing.campaigns') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Campaigns
          </a>
          <a href="{{ route('marketing.notifications') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('marketing.notifications') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Notifications
          </a>
        </div>
      </div>

      <!-- Reports -->
      <div x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('reports.*') ? 'bg-white/10 text-white' : 'text-primary-200 hover:bg-white/10 hover:text-white' }}">
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-file-invoice w-4 text-center flex-shrink-0"></i>
            <span x-show="!sidebarCollapsed" class="font-medium">Reports</span>
          </div>
          <i x-show="!sidebarCollapsed" :class="open?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[10px] text-primary-400"></i>
        </button>
        <div :class="open?'max-h-[1000px]':'max-h-0'" class="overflow-hidden transition-all duration-300 ml-3" x-show="!sidebarCollapsed">
          <!-- Sales Reports -->
          <div x-data="{ salesOpen: false }">
            <button @click="salesOpen = !salesOpen" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('reports.sales.*') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
                Sales Reports
              </div>
              <i :class="salesOpen?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[8px]"></i>
            </button>
            <div :class="salesOpen?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-200 ml-4">
              <a href="{{ route('reports.sales.daily') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.sales.daily') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Daily Sales Summary
              </a>
              <a href="{{ route('reports.sales.by-date') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.sales.by-date') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Sales by Date
              </a>
              <a href="{{ route('reports.sales.hourly') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.sales.hourly') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Hourly Sales
              </a>
              <a href="{{ route('reports.sales.by-product') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.sales.by-product') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Sales by Product
              </a>
              <a href="{{ route('reports.sales.by-category') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.sales.by-category') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Sales by Category
              </a>
              <a href="{{ route('reports.sales.by-brand') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.sales.by-brand') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Sales by Brand
              </a>
              <a href="{{ route('reports.sales.top-selling') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.sales.top-selling') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Top Selling Products
              </a>
              <a href="{{ route('reports.sales.worst-selling') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.sales.worst-selling') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Worst Selling Products
              </a>
            </div>
          </div>

          <!-- Profit Reports -->
          <div x-data="{ profitOpen: false }">
            <button @click="profitOpen = !profitOpen" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('reports.profit.*') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
                Profit Reports
              </div>
              <i :class="profitOpen?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[8px]"></i>
            </button>
            <div :class="profitOpen?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-200 ml-4">
              <a href="{{ route('reports.profit.gross') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.profit.gross') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Gross Profit
              </a>
              <a href="{{ route('reports.profit.margin') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.profit.margin') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Profit Margin
              </a>
              <a href="{{ route('reports.profit.by-category') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.profit.by-category') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Profit by Category
              </a>
              <a href="{{ route('reports.profit.net') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.profit.net') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Net Profit
              </a>
              <a href="{{ route('reports.profit.loss') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.profit.loss') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Loss Report
              </a>
            </div>
          </div>

          <!-- Inventory Reports -->
          <div x-data="{ inventoryOpen: false }">
            <button @click="inventoryOpen = !inventoryOpen" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('reports.inventory.*') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
                Inventory Reports
              </div>
              <i :class="inventoryOpen?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[8px]"></i>
            </button>
            <div :class="inventoryOpen?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-200 ml-4">
              <a href="{{ route('reports.inventory.current-stock') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.inventory.current-stock') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Current Stock
              </a>
              <a href="{{ route('reports.inventory.valuation') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.inventory.valuation') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Inventory Valuation
              </a>
              <a href="{{ route('reports.inventory.movement') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.inventory.movement') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Stock Movement
              </a>
              <a href="{{ route('reports.inventory.stock-in') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.inventory.stock-in') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Stock In (Goods Received)
              </a>
              <a href="{{ route('reports.inventory.stock-out') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.inventory.stock-out') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Stock Out
              </a>
              <a href="{{ route('reports.inventory.transfers') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.inventory.transfers') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Stock Transfers
              </a>
              <a href="{{ route('reports.inventory.low-stock') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.inventory.low-stock') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Low Stock
              </a>
              <a href="{{ route('reports.inventory.out-of-stock') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.inventory.out-of-stock') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Out Of Stock
              </a>
              <a href="{{ route('reports.inventory.overstock') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.inventory.overstock') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Overstock
              </a>
              <a href="{{ route('reports.inventory.fast-moving') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.inventory.fast-moving') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Fast Moving Items
              </a>
              <a href="{{ route('reports.inventory.slow-moving') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.inventory.slow-moving') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Slow Moving Items
              </a>
              <a href="{{ route('reports.inventory.dead-stock') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.inventory.dead-stock') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Dead Stock
              </a>
            </div>
          </div>

          <!-- Expiry Reports -->
          <div x-data="{ expiryOpen: false }">
            <button @click="expiryOpen = !expiryOpen" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('reports.expiry.*') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
                Expiry Reports
              </div>
              <i :class="expiryOpen?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[8px]"></i>
            </button>
            <div :class="expiryOpen?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-200 ml-4">
              <a href="{{ route('reports.expiry.soon') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.expiry.soon') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Expiring Soon
              </a>
              <a href="{{ route('reports.expiry.expired') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.expiry.expired') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Expired Products
              </a>
              <a href="{{ route('reports.expiry.batch-tracking') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.expiry.batch-tracking') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Batch Tracking
              </a>
            </div>
          </div>

          <!-- Purchasing Reports -->
          <div x-data="{ purchasingOpen: false }">
            <button @click="purchasingOpen = !purchasingOpen" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('reports.purchasing.*') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
                Purchasing Reports
              </div>
              <i :class="purchasingOpen?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[8px]"></i>
            </button>
            <div :class="purchasingOpen?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-200 ml-4">
              <a href="{{ route('reports.purchasing.summary') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.purchasing.summary') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Purchase Summary
              </a>
              <a href="{{ route('reports.purchasing.by-supplier') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.purchasing.by-supplier') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Purchase by Supplier
              </a>
              <a href="{{ route('reports.purchasing.supplier-performance') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.purchasing.supplier-performance') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Supplier Performance
              </a>
              <a href="{{ route('reports.purchasing.vs-sales') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.purchasing.vs-sales') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Purchase vs Sales
              </a>
              <a href="{{ route('reports.purchasing.purchase-orders') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.purchasing.purchase-orders') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Purchase Orders
              </a>
            </div>
          </div>

          <!-- Cash & Payment Reports -->
          <div x-data="{ cashOpen: false }">
            <button @click="cashOpen = !cashOpen" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('reports.cash.*') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
                Cash & Payment Reports
              </div>
              <i :class="cashOpen?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[8px]"></i>
            </button>
            <div :class="cashOpen?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-200 ml-4">
              <a href="{{ route('reports.cash.cashier-shift') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.cash.cashier-shift') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Cashier Shift Report
              </a>
              <a href="{{ route('reports.cash.reconciliation') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.cash.reconciliation') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Cash Reconciliation
              </a>
              <a href="{{ route('reports.cash.payment-method') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.cash.payment-method') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Payment Method
              </a>
              <a href="{{ route('reports.cash.daily-flow') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.cash.daily-flow') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Daily Cash Flow
              </a>
            </div>
          </div>

          <!-- Cashier / Staff Reports -->
          <div x-data="{ staffOpen: false }">
            <button @click="staffOpen = !staffOpen" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('reports.staff.*') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
                Staff Reports
              </div>
              <i :class="staffOpen?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[8px]"></i>
            </button>
            <div :class="staffOpen?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-200 ml-4">
              <a href="{{ route('reports.staff.sales-by-cashier') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.staff.sales-by-cashier') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Sales by Cashier
              </a>
              <a href="{{ route('reports.staff.transaction-count') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.staff.transaction-count') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Transaction Count by Cashier
              </a>
              <a href="{{ route('reports.staff.activity') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.staff.activity') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Cashier Activity
              </a>
              <a href="{{ route('reports.staff.discounts') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.staff.discounts') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Discount Report
              </a>
              <a href="{{ route('reports.staff.void-transactions') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.staff.void-transactions') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Void Transaction Report
              </a>
              <a href="{{ route('reports.staff.refunds') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.staff.refunds') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Refund Report
              </a>
            </div>
          </div>

          <!-- Customer Reports -->
          <div x-data="{ customerOpen: false }">
            <button @click="customerOpen = !customerOpen" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('reports.customer.*') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
                Customer Reports
              </div>
              <i :class="customerOpen?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[8px]"></i>
            </button>
            <div :class="customerOpen?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-200 ml-4">
              <a href="{{ route('reports.customer.sales') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.customer.sales') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Customer Sales Report
              </a>
              <a href="{{ route('reports.customer.purchase-history') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.customer.purchase-history') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Customer Purchase History
              </a>
              <a href="{{ route('reports.customer.loyalty') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.customer.loyalty') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Loyalty Report
              </a>
            </div>
          </div>

          <!-- Security & Audit Reports -->
          <div x-data="{ securityOpen: false }">
            <button @click="securityOpen = !securityOpen" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('reports.security.*') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
                Security & Audit Reports
              </div>
              <i :class="securityOpen?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[8px]"></i>
            </button>
            <div :class="securityOpen?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-200 ml-4">
              <a href="{{ route('reports.security.audit-log') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.security.audit-log') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Audit Log Report
              </a>
              <a href="{{ route('reports.security.price-changes') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.security.price-changes') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Price Change Report
              </a>
              <a href="{{ route('reports.security.inventory-adjustments') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.security.inventory-adjustments') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Inventory Adjustment Report
              </a>
              <a href="{{ route('reports.security.user-activity') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.security.user-activity') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                User Activity Report
              </a>
            </div>
          </div>

          <!-- Management Dashboard Reports -->
          <div x-data="{ managementOpen: false }">
            <button @click="managementOpen = !managementOpen" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('reports.management.*') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
                Management Reports
              </div>
              <i :class="managementOpen?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[8px]"></i>
            </button>
            <div :class="managementOpen?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-200 ml-4">
              <a href="{{ route('reports.management.executive') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.management.executive') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Executive Dashboard
              </a>
              <a href="{{ route('reports.management.inventory-investment') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.management.inventory-investment') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Inventory Investment Report
              </a>
              <a href="{{ route('reports.management.inventory-turnover') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.management.inventory-turnover') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Inventory Turnover Report
              </a>
              <a href="{{ route('reports.management.stock-accuracy') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.management.stock-accuracy') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Stock Accuracy Report
              </a>
              <a href="{{ route('reports.management.business-growth') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.management.business-growth') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Business Growth Report
              </a>
            </div>
          </div>

          <!-- FeedTan Store Advanced Reports -->
          <div x-data="{ advancedOpen: false }">
            <button @click="advancedOpen = !advancedOpen" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('reports.advanced.*') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
         Advanced Reports
              </div>
              <i :class="advancedOpen?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[8px]"></i>
            </button>
            <div :class="advancedOpen?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-200 ml-4">
              <a href="{{ route('reports.advanced.branch-comparison') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.advanced.branch-comparison') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Branch Comparison Report
              </a>
              <a href="{{ route('reports.advanced.branch-profit') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.advanced.branch-profit') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Branch Profit Report
              </a>
              <a href="{{ route('reports.advanced.expansion-readiness') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.advanced.expansion-readiness') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Expansion Readiness Report
              </a>
              <a href="{{ route('reports.advanced.member-purchase') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.advanced.member-purchase') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Shareholder Purchase Report
              </a>
              <a href="{{ route('reports.advanced.supplier-credit') }}" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs transition-all duration-150 {{ request()->routeIs('reports.advanced.supplier-credit') ? 'bg-primary-500/60 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-minus text-[4px] flex-shrink-0 ml-2"></i>
                Supplier Credit Report
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- System Administration -->
      <div x-data="{ open: {{ request()->routeIs('system.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('system.*') ? 'bg-white/10 text-white' : 'text-primary-200 hover:bg-white/10 hover:text-white' }}">
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-gear w-4 text-center flex-shrink-0"></i>
            <span x-show="!sidebarCollapsed" class="font-medium">System Administration</span>
          </div>
          <i x-show="!sidebarCollapsed" :class="open?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'" class="text-[10px] text-primary-400"></i>
        </button>
        <div :class="open?'max-h-[2000px]':'max-h-0'" class="overflow-hidden transition-all duration-300 ml-3" x-show="!sidebarCollapsed">
          <a href="{{ route('system.general') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('system.general') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            General Settings
          </a>
          <a href="{{ route('system.tax') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('system.tax') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Tax Settings
          </a>
          <a href="{{ route('system.receipt') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('system.receipt') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Receipt Settings
          </a>
          <a href="{{ route('system.barcode') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('system.barcode') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Barcode Settings
          </a>
          <a href="{{ route('system.backup') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('system.backup') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Backup & Restore
          </a>
          <a href="{{ route('system.database') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('system.database') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            Database
          </a>
          <a href="{{ route('system.logs') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150 mt-0.5 {{ request()->routeIs('system.logs') ? 'bg-primary-600/80 text-white' : 'text-primary-300 hover:bg-white/10 hover:text-white' }}">
            <i class="fa-solid fa-circle text-[6px] flex-shrink-0 ml-1"></i>
            System Logs
          </a>
        </div>
      </div>

      <!-- Logout -->
      <div class="pt-2">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-primary-200 hover:bg-red-900/20 hover:text-red-300 transition-all duration-150">
            <i class="fa-solid fa-right-from-bracket w-4 text-center flex-shrink-0"></i>
            <span x-show="!sidebarCollapsed" class="font-medium">Logout</span>
          </button>
        </form>
      </div>
    </nav>
    </aside>
  </template>

  <!-- ============================================================
       MAIN CONTENT AREA
       ============================================================ -->
  <div class="flex-1 flex flex-col overflow-hidden main-content">

    <!-- TOP NAVBAR -->
    <header :class="isCashier ? 'flex items-center justify-between h-auto flex-shrink-0 relative z-30' : 'navbar-bg flex items-center justify-between px-4 h-14 flex-shrink-0 relative z-30'">
      <template x-if="!isCashier">
        <div class="w-full flex items-center justify-between">
          <!-- Left: Hamburger + Breadcrumb -->
          <div class="flex items-center gap-3">
            <button @click="sidebarOpen=!sidebarOpen" class="p-2 rounded-lg transition-colors lg:hidden text-primary-700 hover:bg-primary-50">
              <i class="fa-solid fa-bars text-sm"></i>
            </button>
            <button @click="sidebarCollapsed=!sidebarCollapsed" class="p-2 rounded-lg transition-colors hidden lg:block text-primary-700 hover:bg-primary-50">
              <i class="fa-solid fa-bars text-sm"></i>
            </button>
            <div class="hidden sm:flex items-center gap-2">
              <span class="text-xs font-medium text-primary-500">FEEDTAN STORE</span>
              <i class="fa-solid fa-chevron-right text-[10px] text-primary-300"></i>
              <span class="text-xs font-semibold text-primary-800">@yield('page-title')</span>
            </div>
          </div>

          <!-- Center: Search -->
          <div class="hidden md:flex flex-1 max-w-xs mx-4">
            <div class="relative w-full">
              <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-primary-400"></i>
              <input type="text" placeholder="Search..."
                     class="form-input input-field pl-8 text-xs py-2 bg-primary-50 border-primary-200 text-primary-900">
            </div>
          </div>

          <!-- Right: Actions -->
          <div class="flex items-center gap-2">
            <!-- Notifications -->
            <div class="relative" x-data="{open:false}">
              <button @click="open=!open" class="relative p-2 rounded-lg transition-colors text-primary-700 hover:bg-primary-50">
                <i class="fa-solid fa-bell text-sm"></i>
                @if($totalNotifications > 0)
                  <span class="absolute top-1 right-1 bg-red-500 text-white text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center">{{ $totalNotifications }}</span>
                @endif
              </button>
              <div x-show="open" @click.away="open=false" x-transition
                   class="absolute right-0 top-10 w-80 rounded-2xl shadow-2xl z-50 overflow-hidden bg-white border border-primary-100">
                <div class="p-4 border-b border-primary-100">
                  <div class="flex justify-between items-center">
                    <h3 class="font-bold text-sm text-primary-900">Notifications</h3>
                    @if($totalNotifications > 0)
                      <span class="badge badge-red text-[10px]">{{ $totalNotifications }} New</span>
                    @endif
                  </div>
                </div>
                <div class="max-h-72 overflow-y-auto">
                  <!-- Out of Stock -->
                  @if($hasOutOfStock)
                    <a href="{{ route('inventory.products') }}" class="block p-3 border-b border-primary-50 transition-colors hover:bg-primary-50">
                      <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-xs bg-red-900/40 text-red-400">
                          <i class="fa-solid fa-circle-xmark"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                          <p class="text-xs font-semibold truncate text-primary-900">Out of Stock</p>
                          <p class="text-[11px] mt-0.5 text-gray-500">{{ $outOfStockCount }} product(s) are out of stock</p>
                        </div>
                        <div class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0 mt-1"></div>
                      </div>
                    </a>
                  @endif

                  <!-- Low Stock -->
                  @if($hasLowStock)
                    <a href="{{ route('inventory.low-stock') }}" class="block p-3 border-b border-primary-50 transition-colors hover:bg-primary-50">
                      <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-xs bg-yellow-900/40 text-yellow-400">
                          <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                          <p class="text-xs font-semibold truncate text-primary-900">Low Stock Alert</p>
                          <p class="text-[11px] mt-0.5 text-gray-500">{{ $lowStockCount }} product(s) are running low</p>
                        </div>
                        <div class="w-2 h-2 rounded-full bg-yellow-500 flex-shrink-0 mt-1"></div>
                      </div>
                    </a>
                  @endif

                  <!-- Expiring Soon -->
                  @if($hasExpiring)
                    <a href="{{ route('inventory.expiry') }}" class="block p-3 border-b border-primary-50 transition-colors hover:bg-primary-50">
                      <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-xs bg-orange-900/40 text-orange-400">
                          <i class="fa-solid fa-clock"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                          <p class="text-xs font-semibold truncate text-primary-900">Expiring Soon</p>
                          <p class="text-[11px] mt-0.5 text-gray-500">{{ $expiringCount }} product(s) will expire soon</p>
                        </div>
                        <div class="w-2 h-2 rounded-full bg-orange-500 flex-shrink-0 mt-1"></div>
                      </div>
                    </a>
                  @endif

                  <!-- Expired Products -->
                  @if($hasExpired)
                    <a href="{{ route('inventory.expiry') }}" class="block p-3 border-b border-primary-50 transition-colors hover:bg-primary-50">
                      <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-xs bg-red-900/40 text-red-400">
                          <i class="fa-solid fa-skull-crossbones"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                          <p class="text-xs font-semibold truncate text-primary-900">Expired Products</p>
                          <p class="text-[11px] mt-0.5 text-gray-500">{{ $expiredCount }} product(s) have expired</p>
                        </div>
                        <div class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0 mt-1"></div>
                      </div>
                    </a>
                  @endif

                  @if($totalNotifications == 0)
                    <div class="p-8 text-center">
                      <i class="fa-solid fa-check-circle text-2xl text-green-500 mb-2"></i>
                      <p class="text-xs text-gray-500">No new notifications</p>
                    </div>
                  @endif
                </div>
              </div>
            </div>

            <!-- User Profile -->
            <div class="relative" x-data="{open:false}">
              <button @click="open=!open" class="flex items-center gap-2 p-1.5 rounded-xl transition-colors hover:bg-primary-50">
                @if(Auth::user()->profile_image)
                  <img src="{{ Storage::url(Auth::user()->profile_image) }}" 
                       alt="Profile" 
                       class="w-8 h-8 rounded-full object-cover border-2 border-primary-200">
                @else
                  <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-700 flex items-center justify-center text-white font-bold text-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                  </div>
                @endif
                <div class="hidden lg:block text-left">
                  <p class="text-xs font-semibold leading-tight text-primary-900">{{ Auth::user()->name }}</p>
                  <p class="text-[10px] text-primary-500">{{ Auth::user()->email }}</p>
                </div>
                <i class="fa-solid fa-chevron-down text-[10px] hidden lg:block text-primary-400"></i>
              </button>
              <div x-show="open" @click.away="open=false" x-transition
                   class="absolute right-0 top-11 w-56 rounded-2xl shadow-2xl z-50 py-2 bg-white border border-primary-100">
                <div class="px-4 py-2 border-b border-primary-100">
                  <p class="text-xs font-bold text-primary-900">{{ Auth::user()->name }}</p>
                  <p class="text-[11px] text-gray-500">{{ Auth::user()->email }}</p>
                </div>
                <a href="{{ route('profile.show') }}" class="w-full flex items-center gap-3 px-4 py-2.5 text-xs hover:bg-primary-50 transition-colors text-left text-gray-700">
                  <i class="fa-solid fa-user w-4"></i> My Profile
                </a>
                <div class="border-t my-1 border-primary-100"></div>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-xs text-red-500 hover:bg-red-50 transition-colors text-left">
                    <i class="fa-solid fa-right-from-bracket w-4"></i> Logout
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </template>
      <template x-if="isCashier">
        <!-- Cashier Navbar -->
        <div class="w-full flex items-center justify-between sidebar-bg text-white px-4 py-3">
          <div class="flex items-center gap-4">
            <img src="{{ asset('feedtanstorelogo.png') }}" alt="FEEDTAN STORE" class="h-10 object-contain" style="filter: brightness(0) invert(1);">
          </div>
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 text-sm">
              <i class="fa-solid fa-clock"></i>
              <span x-text="currentTime"></span>
            </div>
            <span class="px-3 py-1 bg-green-500 text-white rounded-full text-xs font-semibold">
              <i class="fa-solid fa-circle text-[8px] mr-1"></i>Shift Open
            </span>
            <div class="flex items-center gap-2 text-sm">
              <i class="fa-solid fa-user-circle"></i>
              <span x-text="currentUser.name"></span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors">
                <i class="fa-solid fa-right-from-bracket mr-1"></i>Logout
              </button>
            </form>
          </div>
        </div>
      </template>
    </header>

    <!-- PAGES CONTAINER -->
    <main class="flex-1 overflow-y-auto p-4 lg:p-6 main-bg">
      @yield('content')
    </main>
  </div>
</div>

<!-- Global Loading Script -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Get the Alpine component instance
    let alpineComponent;
    
    // Wait until Alpine is initialized
    const checkAlpine = () => {
      const mainEl = document.querySelector('[x-data]');
      if (mainEl && mainEl._x_dataStack && mainEl._x_dataStack[0]) {
        alpineComponent = mainEl._x_dataStack[0];
        
        // Add event listeners for links
        document.body.addEventListener('click', function(e) {
          const target = e.target.closest('a[href]');
          if (target && target.href.startsWith('http') && !target.target && !target.hasAttribute('download') && !target.href.startsWith('javascript:')) {
            alpineComponent.loading = true;
          }
        });
        
        // Add event listeners for form submissions
        document.body.addEventListener('submit', function(e) {
          const target = e.target.closest('form');
          if (target) {
            alpineComponent.loading = true;
          }
        });
        
        // Hide loading when page is ready
        window.addEventListener('pageshow', function() {
          alpineComponent.loading = false;
        });
        
        // Also hide on page load in case something went wrong
        setTimeout(() => {
          if (alpineComponent.loading) {
            alpineComponent.loading = false;
          }
        }, 10000); // Hide after 10 seconds max
      } else {
        setTimeout(checkAlpine, 100); // Try again in 100ms
      }
    };
    
    checkAlpine();
  });
</script>
@yield('scripts')
</body>
</html>