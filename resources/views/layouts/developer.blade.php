<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'بوابة المطور') - {{ \App\Helpers\SettingsHelper::getCompanyName() }}</title>

    @php
        $faviconUrl = \App\Helpers\SettingsHelper::getFaviconUrl();
        $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
        $sidebarColor = \App\Helpers\SettingsHelper::getSidebarColor();
        $logoUrl = \App\Helpers\SettingsHelper::getLogoUrl();
        $logoSize = \App\Helpers\SettingsHelper::getLogoSizePixels();
        $devAccount = auth('developer')->user();
        $developer = $devAccount?->developer;
        $displayName = $devAccount?->name ?? $developer?->name ?? 'مطور';
        $displayRole = \App\Models\DeveloperAccount::ROLES[$devAccount?->portalRole() ?? 'owner'] ?? 'بوابة المطور';
    @endphp

    <link rel="icon" type="image/x-icon" href="{{ $faviconUrl ?: '/favicon.ico' }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ $faviconUrl ?: '/favicon.ico' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { tajawal: ['Tajawal', 'sans-serif'] }
                }
            }
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.0/dist/axios.min.js"></script>
    <script>
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        const token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    </script>
    <style>
        :root { --brand: {{ $themeColor }}; }
        body { font-family: 'Tajawal', sans-serif; }
        .font-tajawal { font-family: 'Tajawal', sans-serif; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(156,163,175,.3); border-radius: 2px; }
        .sidebar-bg { color: #fff; border-color: #334155; }
        .sidebar-link {
            background: transparent;
            color: #94a3b8;
            border-radius: 8px;
            margin: 2px 0;
            transition: all .2s ease;
        }
        .sidebar-link:hover {
            background: rgba(255,255,255,.08);
            color: #e2e8f0;
        }
        .sidebar-link.active {
            background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}cc 100%);
            color: #fff;
            box-shadow: 0 4px 12px {{ $themeColor }}40;
        }
        .sidebar-section-title {
            color: #64748b;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin: 16px 0 8px;
        }
        .sidebar-user-bg {
            background: #1e293b;
            border-color: #334155;
            color: #fff;
        }
        @media (max-width: 768px) {
            .sidebar-bg {
                position: fixed;
                top: 0;
                right: 0;
                height: 100vh;
                z-index: 50;
                transform: translateX(100%);
                transition: transform .3s ease-in-out;
            }
            .sidebar-bg.mobile-open { transform: translateX(0); }
            .sidebar-overlay {
                position: fixed; inset: 0;
                background: rgba(0,0,0,.5);
                z-index: 40;
                opacity: 0;
                visibility: hidden;
                transition: all .3s ease-in-out;
            }
            .sidebar-overlay.active { opacity: 1; visibility: visible; }
            .mobile-menu-btn { display: block; }
        }
        @media (min-width: 769px) {
            .mobile-menu-btn { display: none; }
            .sidebar-overlay { display: none; }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 font-tajawal min-h-screen">
<div class="flex h-screen">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="w-72 sidebar-bg border-l border-slate-700 shadow-xl flex flex-col" id="sidebar" style="background-color: {{ $sidebarColor }}">
        <div class="p-6 border-b border-slate-700 bg-slate-800 shadow-sm">
            <div class="flex items-center gap-4">
                @if($logoUrl)
                    <div class="{{ $logoSize }} rounded-xl overflow-hidden ml-0 shrink-0 shadow-xl">
                        <img src="{{ $logoUrl }}" alt="Logo" class="w-full h-full object-contain" onerror="this.style.display='none'">
                    </div>
                @else
                    <div class="{{ $logoSize }} rounded-xl flex items-center justify-center shrink-0 shadow-xl text-white font-extrabold text-xl"
                         style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}cc 100%);">
                        {{ mb_substr($developer?->name ?? 'م', 0, 1) }}
                    </div>
                @endif
                <div class="min-w-0 flex-1">
                    <h1 class="text-lg font-bold text-white truncate">بوابة المطور</h1>
                    <p class="text-sm text-slate-300 truncate">{{ $developer?->name ?? \App\Helpers\SettingsHelper::getCompanyName() }}</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto sidebar-scroll p-3">
            @include('layouts.partials.sidebar-developer')
        </nav>

        <div class="p-5 border-t border-slate-700 sidebar-user-bg">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="h-10 w-10 rounded-full flex items-center justify-center text-white font-bold shrink-0 shadow-lg"
                         style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}cc 100%);">
                        {{ mb_substr($displayName, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-white truncate">{{ $displayName }}</div>
                        <div class="text-xs text-slate-300 truncate">{{ $displayRole }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('developer.logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-white p-2 hover:bg-slate-700 rounded-lg transition" title="تسجيل الخروج">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
            <div class="px-3 sm:px-5 py-2.5 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                    <button type="button" class="mobile-menu-btn p-1.5 rounded-lg hover:bg-gray-100 shrink-0" id="mobileMenuBtn" style="color: {{ $themeColor }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 truncate font-tajawal">@yield('page-title', 'لوحة التحكم')</h2>
                        <p class="hidden sm:block text-xs text-gray-500 truncate font-tajawal">{{ $developer?->name }}</p>
                    </div>
                </div>
                <div class="hidden sm:flex items-center gap-1.5 px-2 py-1 rounded-md bg-gray-50 text-[11px] text-gray-600 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ now()->locale('ar')->translatedFormat('l، d F Y') }}</span>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto">
            <div class="p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto w-full">
                @if(session('success'))
                <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal">{{ session('error') }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const btn = document.getElementById('mobileMenuBtn');
    if (!sidebar || !overlay || !btn) return;

    function openSidebar() {
        sidebar.classList.add('mobile-open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    btn.addEventListener('click', function () {
        sidebar.classList.contains('mobile-open') ? closeSidebar() : openSidebar();
    });
    overlay.addEventListener('click', closeSidebar);
    sidebar.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 768) closeSidebar();
        });
    });
});
</script>
@stack('scripts')
</body>
</html>
