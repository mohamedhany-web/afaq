<!DOCTYPE html>
<?php
    $pageLocale = app()->getLocale();
    $pageDir = $pageLocale === 'en' ? 'ltr' : 'rtl';
?>
<html lang="<?php echo e(str_replace('_', '-', $pageLocale)); ?>" dir="<?php echo e($pageDir); ?>">
<head>
    <meta charset="utf-8mb4">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('page-title', \App\Helpers\SettingsHelper::getSystemName()); ?> - <?php echo e(\App\Helpers\SettingsHelper::getCompanyName()); ?></title>
    
    <!-- Favicon -->
    <?php $faviconUrl = \App\Helpers\SettingsHelper::getFaviconUrl(); ?>
    <link rel="icon" type="image/x-icon" href="<?php echo e($faviconUrl ?: '/favicon.ico'); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e($faviconUrl ?: '/favicon.ico'); ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;600;700;900&family=Amiri:wght@400;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'tajawal': ['Tajawal', 'sans-serif'],
                        'arabic': ['Tajawal', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.0/dist/axios.min.js"></script>
    <script>
        // Setup Axios defaults
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        let token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        }
    </script>
    
    <style>
        .font-tajawal {
            font-family: 'Tajawal', sans-serif;
        }
        body.ui-compact-mode .ui-compact-hidden {
            display: none !important;
        }
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.3);
            border-radius: 2px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(59, 130, 246, 0.6);
        }
        
        /* Original Dark Theme Sidebar */
        .sidebar-bg {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            border-color: #334155;
        }
        
        .sidebar-nav-bg {
            background: transparent;
        }
        
        .sidebar-user-bg {
            background: #1e293b;
            border-color: #334155;
            color: white;
        }
        
        .sidebar-user-bg * {
            color: white !important;
        }
        
        .sidebar-user-bg .text-slate-300 {
            color: #cbd5e1 !important;
        }
        
        .sidebar-bg {
            color: white;
        }
        
        .sidebar-bg h1,
        .sidebar-bg h2,
        .sidebar-bg h3,
        .sidebar-bg p,
        .sidebar-bg div,
        .sidebar-bg span {
            color: inherit;
        }
        
        .sidebar-link {
            background: transparent;
            color: #94a3b8;
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.2s ease;
        }
        
        .sidebar-link:hover {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }
        
        .sidebar-link.active {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        
        .sidebar-section-title {
            color: #64748b;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 16px 0 8px 0;
        }

        /* LTR layout (English) */
        html[dir="ltr"] #sidebar {
            border-left: none;
            border-right: 1px solid #334155;
        }

        html[dir="ltr"] .sidebar-link svg.ml-3 {
            margin-left: 0;
            margin-right: 0.75rem;
        }

        html[dir="ltr"] .sidebar-logo-gap {
            margin-left: 0;
            margin-right: 1rem;
        }

        html[dir="ltr"] .app-top-header .header-inner {
            direction: ltr;
        }

        html[dir="ltr"] .operations-locale-surface,
        html[dir="ltr"] .operations-locale-surface .stat-card,
        html[dir="ltr"] .operations-locale-surface .operations-kpi-card {
            direction: ltr;
            text-align: start;
        }

        html[dir="ltr"] .operations-locale-surface .stat-card-link-icon {
            transform: none;
        }
        
        /* Mobile Responsive Sidebar */
        @media (max-width: 768px) {
            .sidebar-bg {
                position: fixed;
                top: 0;
                right: 0;
                height: 100vh;
                z-index: 50;
                transform: translateX(100%);
                transition: transform 0.3s ease-in-out;
            }
            
            .sidebar-bg.mobile-open {
                transform: translateX(0);
            }
            
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 40;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease-in-out;
            }
            
            .sidebar-overlay.active {
                opacity: 1;
                visibility: visible;
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .main-content-mobile {
                margin-right: 0;
            }

            html[dir="ltr"] .sidebar-bg {
                right: auto;
                left: 0;
                transform: translateX(-100%);
            }

            html[dir="ltr"] .sidebar-bg.mobile-open {
                transform: translateX(0);
            }

            html[dir="ltr"] .main-content-mobile {
                margin-left: 0;
            }
        }
        
        @media (min-width: 769px) {
            .mobile-menu-btn {
                display: none;
            }
            
            .sidebar-overlay {
                display: none;
            }
        }
        
        /* Dark Theme Body */
        .dark-body {
            background: #0f172a;
        }
        
        .dark-main {
            background: #0f172a;
        }
        
        .dark-header {
            background: #ffffff;
            border-color: #e5e7eb;
            backdrop-filter: blur(10px);
        }

        .app-top-header .header-inner {
            min-height: 3rem;
        }
        @media (min-width: 640px) {
            .app-top-header .header-inner {
                min-height: 3.25rem;
            }
        }
        
        /* Time updater for dashboard */
        #dashboard-time {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }
        
        .dark-content {
            background: #0f172a;
        }
    </style>
    
    <!-- WhatsApp Automation Script -->
    <script src="<?php echo e(asset('js/whatsapp-automation.js')); ?>"></script>
</head>
<body class="bg-gray-50 font-tajawal">
    <?php
        $webUser = Auth::guard('web')->user();
        $clientAccount = Auth::guard('client')->user();
        $isClientGuard = (bool) $clientAccount && !$webUser;
        $displayName = $webUser?->name ?? $clientAccount?->name ?? 'مستخدم';
        $displayEmail = $webUser?->email ?? $clientAccount?->email ?? null;
        $displayRole = $webUser?->roles?->first()?->name ?? ($isClientGuard ? 'عميل' : 'مستخدم');
        $workDayService = app(\App\Services\WorkDayService::class);
        $workDayStatus = $webUser ? $workDayService->statusFor($webUser) : ['show_button' => false, 'must_start' => false, 'on_leave' => false, 'required' => false];
        $themeColorWd = \App\Helpers\SettingsHelper::getThemeColor();
        $showLocaleToggle = $webUser && (
            request()->routeIs('operations.*')
            || $webUser->usesOperationsWorkspace()
        );
    ?>
    <div class="flex h-screen">
        <!-- Mobile Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <!-- Sidebar -->
        <div class="w-72 sidebar-bg border-l border-slate-700 shadow-xl flex flex-col" id="sidebar" style="background-color: <?php echo e(\App\Helpers\SettingsHelper::getSidebarColor()); ?>">
            <!-- Logo -->
            <div class="p-6 border-b border-slate-700 bg-slate-800 shadow-sm">
                <div class="flex items-center">
                    <?php
                        $logoUrl = \App\Helpers\SettingsHelper::getLogoUrl();
                        $logoSize = \App\Helpers\SettingsHelper::getLogoSizePixels();
                    ?>
                    
                    <?php if($logoUrl): ?>
                        <!-- Custom Logo -->
                        <div class="<?php echo e($logoSize); ?> rounded-xl overflow-hidden ml-4 sidebar-logo-gap shadow-xl">
                            <img src="<?php echo e($logoUrl); ?>" alt="Logo" class="w-full h-full object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        </div>
                    <?php else: ?>
                        <!-- Default Logo with Enhanced Design -->
                        <div class="<?php echo e($logoSize); ?> rounded-xl flex items-center justify-center ml-4 sidebar-logo-gap shadow-xl overflow-hidden relative" style="background: linear-gradient(135deg, <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?> 0%, <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>cc 100%);">
                            <div class="relative">
                                <!-- Gear Icon -->
                                <svg class="h-6 w-6 text-white absolute top-0 right-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                </svg>
                                <!-- Search Icon -->
                                <svg class="h-5 w-5 text-white absolute bottom-0 left-0 opacity-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <!-- Subtle glow effect -->
                            <div class="absolute inset-0 rounded-xl opacity-20" style="background: radial-gradient(circle at center, white 0%, transparent 70%);"></div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="flex-1">
                        <?php if($isClientGuard): ?>
                            <h1 class="text-xl font-bold text-white drop-shadow-sm tracking-wide">بوابة العميل</h1>
                            <p class="text-sm text-slate-300 leading-relaxed"><?php echo e(\App\Helpers\SettingsHelper::getCompanyName()); ?></p>
                        <?php else: ?>
                            <h1 class="text-xl font-bold text-white drop-shadow-sm tracking-wide"><?php echo e(\App\Helpers\SettingsHelper::getSystemName()); ?></h1>
                            <p class="text-sm text-slate-300 leading-relaxed"><?php echo e(\App\Helpers\SettingsHelper::getSystemDescription()); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Navigation - Scrollable -->
            <nav class="flex-1 overflow-y-auto sidebar-scroll sidebar-nav-bg">
                <div class="p-3">
                    <div class="space-y-1">
                        <?php echo $__env->make('layouts.partials.sidebar-realestate', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php if(false): ?> 
                        <?php if(!$isClientGuard): ?>
                        <a href="<?php echo e(route('dashboard')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                            <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                            </svg>
                            لوحة التحكم
                        </a>
                        <?php endif; ?>

                        
                        <?php if(Auth::guard('client')->check()): ?>
                        <?php $cPortal = Auth::guard('client')->user(); ?>
                        <div class="mt-6">
                            <h3 class="sidebar-section-title px-4">بوابة العميل</h3>
                            <a href="<?php echo e(route('client.dashboard')); ?>"
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('client.dashboard') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 1.656-1.791 3-4 3s-4-1.344-4-3 1.791-3 4-3 4 1.344 4 3zm8 0c0 1.656-1.791 3-4 3s-4-1.344-4-3 1.791-3 4-3 4 1.344 4 3z" />
                                </svg>
                                لوحة العميل
                            </a>
                            <a href="<?php echo e(route('client.notifications')); ?>"
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('client.notifications*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                الإشعارات
                            </a>
                            <a href="<?php echo e(route('client.projects')); ?>"
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('client.projects') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                                </svg>
                                مشاريعي
                            </a>
                            <?php if($cPortal && $cPortal->canAccessBilling()): ?>
                            <a href="<?php echo e(route('client.invoices')); ?>"
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('client.invoices') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                فواتيري
                            </a>
                            <?php endif; ?>
                            <a href="<?php echo e(route('client.service-reports')); ?>"
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('client.service-reports', 'client.service-reports.download') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                تقارير الخدمة
                            </a>
                            <a href="<?php echo e(route('client.documents')); ?>"
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('client.documents*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                المستندات المشتركة
                            </a>
                            <?php if($cPortal && $cPortal->canAccessTechnicalRequests()): ?>
                            <a href="<?php echo e(route('client.website-issues.index')); ?>"
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('client.website-issues.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                بلاغات الموقع
                            </a>
                            <a href="<?php echo e(route('client.meeting-requests.index')); ?>"
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('client.meeting-requests.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                طلبات الاجتماعات
                            </a>
                            <a href="<?php echo e(route('client.calendar')); ?>"
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('client.calendar') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                التقويم
                            </a>
                            <?php endif; ?>
                            <a href="<?php echo e(route('client.support.tickets')); ?>"
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('client.support.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-1.414-1.414a2 2 0 00-2.828 0L7 11.343V15h3.657l7.707-7.707a2 2 0 000-2.828z" />
                                </svg>
                                ما بعد البيع
                            </a>
                            <a href="<?php echo e(route('client.help')); ?>"
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('client.help') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                المساعدة
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <!-- الرسائل - web users only -->
                        <?php if(!$isClientGuard): ?>
                        <a href="<?php echo e(route('messages.index')); ?>" 
                           class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('messages.*') ? 'active' : ''); ?>">
                            <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            الرسائل
                            <span id="unread-messages-count" class="mr-auto bg-red-500 text-white text-xs rounded-full px-2 py-1 hidden">0</span>
                        </a>
                        <?php endif; ?>
                        
                        <!-- الإشعارات - web users only -->
                        <?php if(!$isClientGuard): ?>
                        <a href="<?php echo e(route('notifications.index')); ?>" 
                           class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('notifications.*') ? 'active' : ''); ?>">
                            <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            الإشعارات
                            <span id="unread-notifications-count" class="mr-auto bg-red-500 text-white text-xs rounded-full px-2 py-1 hidden">0</span>
                        </a>
                        <?php endif; ?>
                        
                        <!-- Administration Section -->
                        <?php if($webUser && ($webUser->can('view-users') || $webUser->can('view-reports') || $webUser->can('view-departments') || $webUser->can('view-settings') || $webUser->can('manage-roles'))): ?>
                        <div class="mt-6">
                            <h3 class="sidebar-section-title px-4">الإدارة العليا</h3>
                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-users')): ?>
                            <a href="<?php echo e(route('users.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                                المستخدمين
                            </a>
                            <?php endif; ?>
                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-roles')): ?>
                            <a href="<?php echo e(route('roles.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('roles.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                الأدوار والصلاحيات
                            </a>
                            <?php endif; ?>
                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-reports')): ?>
                            <a href="<?php echo e(route('admin.system-reports.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('admin.system-reports.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-7M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                                </svg>
                                تقارير النظام
                            </a>
                            <a href="<?php echo e(route('reports.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('reports.*') && !request()->routeIs('admin.system-reports.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                التقارير والتحليل
                            </a>

                            <a href="<?php echo e(route('admin.department-oversight.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('admin.department-oversight.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4V7M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" />
                                </svg>
                                متابعة الأقسام
                            </a>

                            <a href="<?php echo e(route('admin.department-reports.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('admin.department-reports.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                تقارير الأقسام
                            </a>

                            <a href="<?php echo e(route('admin.auto-penalties.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('admin.auto-penalties.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.93 4.93l14.14 14.14M12 3a9 9 0 100 18 9 9 0 000-18z" />
                                </svg>
                                خصومات وعقوبات تلقائية
                            </a>
                            <?php endif; ?>
                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-departments')): ?>
                            <a href="<?php echo e(route('departments.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('departments.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                الأقسام
                            </a>
                            <?php endif; ?>
                            
                            <?php if($webUser && ($webUser->can('view-users') || $webUser->can('manage-roles'))): ?>
                            <a href="<?php echo e(route('login-activity.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('login-activity.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                سجل عمليات تسجيل الدخول
                            </a>
                            <?php endif; ?>
                            
                            <?php if($webUser && ($webUser->can('view-users') || $webUser->can('manage-roles'))): ?>
                            <a href="<?php echo e(route('system-monitoring.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('system-monitoring.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                نظام المراقبة الشامل
                            </a>
                            <?php endif; ?>
                
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-settings')): ?>
                            <a href="<?php echo e(route('system-settings.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('system-settings.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                إعدادات النظام
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Advanced HR Management -->
                        <?php if($webUser && ($webUser->can('view-employees') || $webUser->can('view-attendance') || $webUser->can('view-leaves') || $webUser->can('view-salaries'))): ?>
                        <div class="mt-6">
                            <h3 class="sidebar-section-title px-4">إدارة الموارد البشرية المتقدمة</h3>
                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-employees')): ?>
                            <a href="<?php echo e(route('employees.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('employees.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                الموظفين
                            </a>
                            <?php endif; ?>
                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-attendance')): ?>
                            <a href="<?php echo e(route('attendances.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('attendances.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                الحضور والانصراف
                            </a>
                            <?php endif; ?>
                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-leaves')): ?>
                            <a href="<?php echo e(route('leaves.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('leaves.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 2z" />
                                </svg>
                                الإجازات
                            </a>
                            <?php endif; ?>
                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-salaries')): ?>
                            <a href="<?php echo e(route('salaries.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('salaries.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                                الرواتب
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Real Estate Projects Section -->
                        <?php if($webUser && ($webUser->can('view-own-projects') || $webUser->can('view-all-projects'))): ?>
                        <div class="mt-6">
                            <h3 class="sidebar-section-title px-4">المشاريع العقارية</h3>
                            <a href="<?php echo e(route('projects.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('projects.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                المشاريع العقارية
                            </a>
                        </div>
                        <?php endif; ?>

                        <!-- Training & Development Section -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-training')): ?>
                        <div class="mt-6">
                            <h3 class="sidebar-section-title px-4">التدريب والتطوير</h3>
                            
                            <a href="<?php echo e(route('training.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('training.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                إدارة التدريب
                            </a>
                        </div>
                        <?php endif; ?>

                        <!-- Meetings & Conferences Section -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-meetings')): ?>
                        <div class="mt-6">
                            <h3 class="sidebar-section-title px-4">الاجتماعات والمؤتمرات</h3>
                            
                            <a href="<?php echo e(route('meetings.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('meetings.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                إدارة الاجتماعات
                            </a>
                        </div>
                        <?php endif; ?>

                        <!-- Assets & Properties Section -->
                        <?php if($webUser && $webUser->can('view-assets')): ?>
                        <div class="mt-6">
                            <h3 class="sidebar-section-title px-4">الأصول والممتلكات</h3>
                            
                            <a href="<?php echo e(route('assets.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('assets.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                إدارة الأصول
                            </a>
                        </div>
                        <?php endif; ?>

                        <!-- CRM System -->
                        <?php if($webUser && ($webUser->can('view-clients') || $webUser->can('view-sales') || $webUser->can('view-contracts') || $webUser->can('view-invoices'))): ?>
                        <div class="mt-6">
                            <h3 class="sidebar-section-title px-4">نظام إدارة العملاء (CRM)</h3>
                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-clients')): ?>
                            <a href="<?php echo e(route('clients.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('clients.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                العملاء
                            </a>
                            <a href="<?php echo e(route('client-service-reports.index')); ?>"
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('client-service-reports.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                تقارير العملاء
                            </a>
                            <a href="<?php echo e(route('client-shared-documents.index')); ?>"
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('client-shared-documents.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                مستندات العملاء (بوابة)
                            </a>
                            <?php endif; ?>

                            <a href="<?php echo e(route('client-accounts.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('client-accounts.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 1.656-1.791 3-4 3s-4-1.344-4-3 1.791-3 4-3 4 1.344 4 3zm8 0c0 1.656-1.791 3-4 3s-4-1.344-4-3 1.791-3 4-3 4 1.344 4 3z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 20v-1a5 5 0 0110 0v1M14 20v-1a5 5 0 0110 0v1" />
                                </svg>
                                حسابات العملاء
                            </a>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-developers')): ?>
                            <a href="<?php echo e(route('admin.developers.index')); ?>"
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('admin.developers.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                المطورون العقاريون
                            </a>
                            <?php endif; ?>
                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-sales')): ?>
                            <a href="<?php echo e(route('sales.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('sales.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                المبيعات
                            </a>
                            <?php endif; ?>
                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-contracts')): ?>
                            <a href="<?php echo e(route('contracts.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('contracts.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                العقود
                            </a>
                            <?php endif; ?>
                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-invoices')): ?>
                            <a href="<?php echo e(route('invoices.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('invoices.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                الفواتير
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Technical Support Section -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-tickets')): ?>
                        <div class="mt-6">
                            <h3 class="sidebar-section-title px-4">الدعم الفني</h3>
                            
                            <a href="<?php echo e(route('tickets.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('tickets.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                                تذاكر الدعم
                            </a>

                            <a href="<?php echo e(route('client-website-issues.index')); ?>"
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('client-website-issues.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                بلاغات عملاء الموقع
                            </a>

                            <a href="<?php echo e(route('client-meeting-requests.index')); ?>"
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('client-meeting-requests.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                طلبات اجتماعات العملاء
                            </a>

                            <a href="<?php echo e(route('support.contact-requests.index')); ?>"
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('support.contact-requests.*') ? 'active' : ''); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                                نماذج التواصل
                            </a>
                        </div>
                        <?php endif; ?>

                        <?php
                            $financeInCrmSidebar = $webUser && (
                                $webUser->usesCrmWorkspace()
                                || ($webUser->canAccessCrm() && !$webUser->usesCrmWorkspace())
                            );
                        ?>
                        <?php if(!$financeInCrmSidebar): ?>
                            <?php echo $__env->make('layouts.partials.sidebar-accounting', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?>

                        <!-- Legal Section -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-contracts')): ?>
                        <div class="mt-6">
                            <h3 class="sidebar-section-title px-4">الشؤون القانونية</h3>
                            
                            <a href="<?php echo e(route('contracts.index')); ?>" 
                               class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('contracts.*') ? 'bg-indigo-100 text-indigo-800 shadow-md' : 'sidebar-link'); ?>">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                العقود
                            </a>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?> 

                    </div>
                </div>
            </nav>

            <!-- User Info & Logout -->
            <div class="p-6 border-t border-slate-700 sidebar-user-bg shadow-sm">
                <?php if($webUser || $clientAccount): ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-blue-500 opacity-50"></div>
                                <span class="text-lg font-bold text-white relative z-10"><?php echo e(substr($displayName, 0, 1)); ?></span>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-white"><?php echo e($displayName); ?></div>
                                <div class="text-xs text-slate-300"><?php echo e($displayRole); ?></div>
                            </div>
                        </div>
                        <form method="POST" action="<?php echo e($isClientGuard ? route('client.logout') : route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="text-slate-400 hover:text-white transition duration-200 p-2 hover:bg-slate-700 rounded-lg">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden main-content-mobile">
            <!-- Top Header -->
            <header class="app-top-header bg-white shadow-sm border-b border-gray-200 overflow-visible sticky top-0 z-30">
                <div class="px-3 sm:px-4 lg:px-5 py-2 header-container header-inner">
                    <div class="flex items-center justify-between gap-2 sm:gap-3">
                        <!-- Left Side - Menu Button & Title -->
                        <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0">
                            <!-- Mobile Menu Button -->
                            <button class="mobile-menu-btn p-1.5 rounded-lg transition-all duration-200 flex-shrink-0 hover:bg-gray-100" 
                                    style="color: <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>"
                                    id="mobileMenuBtn"
                                    onmouseover="this.style.backgroundColor='<?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>10'"
                                    onmouseout="this.style.backgroundColor='transparent'">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <div class="min-w-0 flex-1 flex items-center gap-2 sm:gap-3">
                                <h2 class="text-base sm:text-lg font-bold text-gray-900 truncate font-tajawal leading-tight shrink-0 max-w-[45vw] sm:max-w-none"><?php echo $__env->yieldContent('page-title', 'لوحة التحكم'); ?></h2>
                                <span class="hidden md:inline text-gray-300 select-none" aria-hidden="true">|</span>
                                <?php if($isClientGuard): ?>
                                    <p class="hidden md:block text-xs text-gray-500 truncate font-tajawal min-w-0">
                                        بوابة العميل — <?php echo e(\App\Helpers\SettingsHelper::getCompanyName()); ?>

                                    </p>
                                <?php else: ?>
                                    <p class="hidden lg:block text-xs text-gray-500 truncate font-tajawal min-w-0 max-w-[12rem] xl:max-w-xs">
                                        <?php echo e(\App\Helpers\SettingsHelper::getSystemName()); ?>

                                    </p>
                                <?php endif; ?>
                                <div class="hidden sm:flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-gray-50 text-[11px] text-gray-600 shrink-0 ms-auto sm:ms-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span id="current-date"><?php echo e(now()->format('Y/m/d')); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Side - Actions -->
                        <div class="flex items-center gap-1.5 sm:gap-2">
                            <?php if($showLocaleToggle): ?>
                                <?php echo $__env->make('layouts.partials.locale-toggle', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php endif; ?>
                            <?php if (! empty(trim($__env->yieldContent('header-actions')))): ?>
                                <?php echo $__env->yieldContent('header-actions'); ?>
                            <?php endif; ?>
                            <?php if($isClientGuard): ?>
                                <?php $cHdr = $clientAccount; ?>
                                <details class="relative z-50 group/client-actions">
                                    <summary class="flex items-center gap-1.5 cursor-pointer select-none list-none rounded-lg border border-gray-200 bg-white px-2.5 sm:px-3 py-1.5 text-xs font-extrabold text-gray-800 shadow-sm hover:bg-gray-50 transition max-w-[10.5rem] sm:max-w-none [&::-webkit-details-marker]:hidden">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 shrink-0 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        <span class="truncate">طلب جديد</span>
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0 text-gray-500 mr-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </summary>
                                    <div class="absolute end-0 top-[calc(100%+6px)] w-[min(18rem,calc(100vw-2rem))] rounded-xl border border-gray-200 bg-white py-1 shadow-xl text-sm font-semibold text-gray-800 overflow-hidden">
                                        <a href="<?php echo e(route('client.support.tickets.create')); ?>"
                                           class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-white shrink-0" style="background: linear-gradient(135deg, <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?> 0%, <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>cc 100%);">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                                            </span>
                                            <span class="text-right leading-snug">افتح تذكرة دعم</span>
                                        </a>
                                        <?php if($cHdr && $cHdr->canAccessTechnicalRequests()): ?>
                                        <a href="<?php echo e(route('client.website-issues.create')); ?>"
                                           class="flex items-center gap-3 px-4 py-3 hover:bg-amber-50/80 border-b border-gray-100 text-amber-950">
                                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-amber-800 shrink-0">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                            </span>
                                            <span class="text-right leading-snug">بلاغ عن الموقع</span>
                                        </a>
                                        <a href="<?php echo e(route('client.meeting-requests.create')); ?>"
                                           class="flex items-center gap-3 px-4 py-3 hover:bg-cyan-50/80 text-cyan-950">
                                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-cyan-100 text-cyan-800 shrink-0">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            </span>
                                            <span class="text-right leading-snug">طلب اجتماع</span>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </details>
                            <?php else: ?>
                            <?php if(($workDayStatus['show_button'] ?? false)): ?>
                            <?php if(($workDayStatus['on_leave'] ?? false)): ?>
                            <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold font-tajawal bg-blue-50 text-blue-800 border border-blue-200">
                                إجازة اليوم
                            </span>
                            <?php else: ?>
                            <!-- Start Day Button (إلزامي لموظفي المبيعات) -->
                            <button id="startDayBtn" 
                                    class="hidden sm:flex items-center gap-2 px-3 py-1.5 text-white rounded-lg transition-all duration-200 text-xs font-medium shadow-sm hover:shadow-md font-tajawal <?php echo e(($workDayStatus['must_start'] ?? false) ? 'ring-2 ring-amber-400 ring-offset-1 animate-pulse' : ''); ?>"
                                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"
                                    onclick="toggleWorkTimer()"
                                    title="المدة المطلوبة: <?php echo e($workDayStatus['daily_hours'] ?? 8); ?> ساعة">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span id="startDayText" class="font-bold whitespace-nowrap">بدء اليوم</span>
                                <span id="workTimer" class="font-mono opacity-90 whitespace-nowrap" style="display: none;">00:00:00</span>
                                <span id="workTimerTarget" class="text-[10px] opacity-80 whitespace-nowrap hidden"></span>
                            </button>
                            <?php endif; ?>
                            <?php endif; ?>

                            <!-- Notifications -->
                            <div class="relative flex-shrink-0" id="top-bar-notifications-wrap">
                                <button type="button"
                                        id="top-bar-notifications-btn"
                                        onclick="toggleNotifications(event)"
                                        class="relative p-2 rounded-lg transition-all duration-200 hover:shadow-sm flex items-center justify-center"
                                        style="background: <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>15; color: <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>"
                                        onmouseover="this.style.background='<?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>25'"
                                        onmouseout="this.style.background='<?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>15'"
                                        aria-label="الإشعارات"
                                        title="الإشعارات">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    <span id="top-bar-notifications-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden font-bold border-2 border-white shadow-lg"></span>
                                </button>
                            </div>

                            <!-- Messages -->
                            <a href="<?php echo e(route('messages.index')); ?>" 
                               class="hidden sm:flex relative p-2 rounded-lg transition-all duration-200 hover:shadow-sm items-center justify-center flex-shrink-0"
                               style="background: <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>15; color: <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>"
                               onmouseover="this.style.background='<?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>25'"
                               onmouseout="this.style.background='<?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>15'">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <span id="top-bar-messages-count" class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden font-bold border-2 border-white shadow-lg"></span>
                            </a>

                            <!-- Settings -->
                            <a href="<?php echo e(route('system-settings.index')); ?>" 
                               class="hidden sm:flex p-2 rounded-lg transition-all duration-200 hover:shadow-sm items-center justify-center flex-shrink-0"
                               style="background: <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>15; color: <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>"
                               onmouseover="this.style.background='<?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>25'"
                               onmouseout="this.style.background='<?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>15'">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                            <?php endif; ?>

                            <!-- User Profile Dropdown -->
                            <div class="relative overflow-visible user-dropdown-container">
                                <button onclick="toggleUserDropdown()" 
                                        class="flex items-center gap-2 rounded-lg p-1 sm:p-1.5 transition-all duration-200 hover:shadow-sm group"
                                        style="background: <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>10"
                                        onmouseover="this.style.background='<?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>20'"
                                        onmouseout="this.style.background='<?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>10'">
                                    <!-- User info - hidden on small screens -->
                                    <div class="text-right hidden md:block">
                                        <div class="text-xs font-bold text-gray-900 truncate max-w-24 font-tajawal leading-tight"><?php echo e($displayName); ?></div>
                                        <div class="text-[10px] text-gray-500 truncate max-w-24 font-tajawal leading-tight"><?php echo e($displayRole); ?></div>
                                    </div>
                                    <!-- Profile picture - always visible -->
                                    <?php if($webUser && $webUser->profile_picture): ?>
                                        <img src="<?php echo e(asset('storage/' . Auth::user()->profile_picture)); ?>" 
                                             alt="Profile Picture" 
                                             class="h-8 w-8 sm:h-9 sm:w-9 rounded-lg object-cover shadow-sm transition-all duration-200 border border-white flex-shrink-0"
                                             style="border-color: <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>30;">
                                    <?php else: ?>
                                        <div class="h-8 w-8 sm:h-9 sm:w-9 rounded-lg flex items-center justify-center shadow-sm transition-all duration-200 border border-white flex-shrink-0"
                                             style="background: linear-gradient(135deg, <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?> 0%, <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>dd 100%); border-color: <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>30;">
                                            <span class="text-xs sm:text-sm font-bold text-white"><?php echo e(substr($displayName, 0, 1)); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <!-- Dropdown arrow - hidden on small screens -->
                                    <svg class="w-3 h-3 hidden md:block transition-transform duration-200 group-hover:rotate-180 flex-shrink-0" 
                                         style="color: <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>" 
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                            
                                <!-- Dropdown Menu -->
                                <div id="userDropdown" class="absolute top-full left-0 mt-2 w-56 sm:w-64 bg-white rounded-lg shadow-xl border border-gray-200 z-[9999] hidden user-dropdown">
                                                <div class="py-2">
                                                    <!-- Profile Header -->
                                                    <div class="px-4 py-3 border-b border-gray-100">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                                            <?php if($webUser && $webUser->profile_picture): ?>
                                                                <img src="<?php echo e(asset('storage/' . Auth::user()->profile_picture)); ?>" alt="Profile Picture" class="h-12 w-12 rounded-full object-cover shadow-sm">
                                                            <?php else: ?>
                                                                <div class="h-12 w-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-sm">
                                                                    <span class="text-lg font-bold text-white"><?php echo e(substr($displayName, 0, 1)); ?></span>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="flex-1">
                                                                <div class="text-sm font-semibold text-gray-900"><?php echo e($displayName); ?></div>
                                                                <div class="text-xs text-gray-500"><?php echo e($displayEmail); ?></div>
                                                                <div class="text-xs text-blue-600"><?php echo e($displayRole); ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Menu Items -->
                                                    <div class="py-1">
                                                        <?php if(!$isClientGuard): ?>
                                                        <a href="<?php echo e(route('profile.edit')); ?>" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition duration-150">
                                                            <svg class="ml-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                            الملف الشخصي
                                                        </a>
                                                        <?php endif; ?>
                                                        
                                                        <?php if(!$isClientGuard): ?>
                                                        <a href="<?php echo e(route('system-settings.index')); ?>" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition duration-150">
                                                            <svg class="ml-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                            </svg>
                                                            الإعدادات
                                                        </a>
                                                        <?php endif; ?>
                                                        
                                                        <?php if(!$isClientGuard && ($workDayStatus['show_button'] ?? false) && !($workDayStatus['on_leave'] ?? false)): ?>
                                                        <!-- Mobile-only items (staff) -->
                                                        <div class="sm:hidden">
                                                            <div class="border-t border-gray-100 my-1"></div>
                                                            
                                                            <button onclick="toggleWorkTimer()" class="w-full flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition duration-150">
                                                                <svg class="ml-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                <div class="flex flex-col items-start">
                                                                    <span id="startDayTextMobile">بدء اليوم</span>
                                                                    <span id="workTimerMobile" class="text-xs text-gray-500" style="display: none;">00:00:00</span>
                                                                </div>
                                                            </button>
                                                            
                                                            <a href="<?php echo e(route('notifications.index')); ?>" class="w-full flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition duration-150">
                                                                <svg class="ml-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                                </svg>
                                                                الإشعارات
                                                            </a>
                                                        </div>
                                                        <?php else: ?>
                                                        <!-- Mobile-only (client portal) -->
                                                        <div class="sm:hidden">
                                                            <div class="border-t border-gray-100 my-1"></div>
                                                            <a href="<?php echo e(route('client.notifications')); ?>" class="w-full flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition duration-150">
                                                                <svg class="ml-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                                </svg>
                                                                إشعارات البوابة
                                                            </a>
                                                        </div>
                                                        <?php endif; ?>
                                                        
                                                        <div class="border-t border-gray-100 my-1"></div>
                                                        
                                                        <form method="POST" action="<?php echo e($isClientGuard ? route('client.logout') : route('logout')); ?>">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="w-full flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition duration-150">
                                                                <svg class="ml-3 h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                                </svg>
                                                                تسجيل الخروج
                                                            </button>
                                                        </form>
                                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="<?php echo e(request()->routeIs('messages.*') || request()->routeIs('notifications.*') || request()->routeIs('users.create', 'users.edit') || request()->routeIs('system-monitoring.*') || request()->routeIs('system-settings.*') || request()->routeIs('client-service-reports.*') || request()->routeIs('client.dashboard', 'client.projects', 'client.invoices', 'client.service-reports', 'client.service-reports.download', 'client.notifications*', 'client.documents*', 'client.calendar', 'client.help', 'client.support.*', 'client.website-issues.*', 'client.meeting-requests.*') || request()->routeIs('client-website-issues.*') || request()->routeIs('client-meeting-requests.*') || request()->routeIs('projects.*') || request()->routeIs('crm.*') || request()->routeIs('accounting.*') || request()->routeIs('financial-invoices.*') || request()->routeIs('invoices.*') || request()->routeIs('payments.*') || request()->routeIs('expenses.*') || request()->routeIs('admin.auto-penalties.*') || request()->routeIs('leaves.*') ? 'w-full max-w-full px-3 sm:px-4 lg:px-6 xl:px-8 py-4 sm:py-6 min-h-0' : 'container mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6'); ?>">
                    <?php if(session('success')): ?>
                        <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-r-lg shadow-sm">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>
                    <?php if(session('error')): ?>
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r-lg shadow-sm">
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>
                    <?php if(($workDayStatus['must_start'] ?? false) && !($workDayStatus['on_leave'] ?? false)): ?>
                    <div id="work-day-must-start-banner" class="mb-4 rounded-xl border-2 border-amber-300 bg-amber-50 px-4 py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 font-tajawal">
                        <div class="flex items-start gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-200 text-amber-900">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <div>
                                <p class="text-sm font-bold text-amber-950">بدء يوم العمل مطلوب</p>
                                <p class="text-xs text-amber-800 mt-0.5">اضغط «بدء اليوم» لتسجيل <strong><?php echo e($workDayStatus['daily_hours'] ?? 8); ?></strong> ساعات عمل. عند انتهاء المدة يُوقَف التايمر تلقائياً إن نسيت الإيقاف.</p>
                            </div>
                        </div>
                        <button type="button" onclick="toggleWorkTimer()" class="shrink-0 px-4 py-2 rounded-lg text-white text-xs font-bold shadow-sm"
                                style="background: linear-gradient(135deg, <?php echo e($themeColorWd); ?> 0%, <?php echo e($themeColorWd); ?>dd 100%);">
                            بدء اليوم الآن
                        </button>
                    </div>
                    <?php elseif(($workDayStatus['on_leave'] ?? false)): ?>
                    <div class="mb-4 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900 font-tajawal">
                        أنت في <strong>إجازة معتمدة</strong> اليوم — لا يلزم تسجيل بدء يوم العمل.
                    </div>
                    <?php endif; ?>
                    <?php echo $__env->make('layouts.partials.workspace-switcher', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </main>
        </div>
    </div>

                <script>
                    // Add CSS for dropdown positioning
                    const style = document.createElement('style');
                    style.textContent = `
                        .user-dropdown-container {
                            position: relative;
                            overflow: visible !important;
                        }
                        .user-dropdown {
                            position: absolute;
                            z-index: 9999 !important;
                            transform: translateZ(0);
                            will-change: transform;
                        }
                        .header-container {
                            overflow: visible !important;
                            position: relative;
                            z-index: 40;
                        }
                    `;
                    document.head.appendChild(style);
                    
                    // Mobile Sidebar Toggle
                    document.addEventListener('DOMContentLoaded', function() {
                        if (workDayConfig.show_button) {
                            loadTimerState();
                            setInterval(function () {
                                if (isWorkTimerRunning) {
                                    fetch('/attendances/current-work-time')
                                        .then(r => r.json())
                                        .then(data => processWorkDayApiData(data))
                                        .catch(() => {});
                                }
                            }, 60000);
                        }
                        
                        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
                        const sidebar = document.getElementById('sidebar');
                        const sidebarOverlay = document.getElementById('sidebarOverlay');
                        
                        function openSidebar() {
                            sidebar.classList.add('mobile-open');
                            sidebarOverlay.classList.add('active');
                            document.body.style.overflow = 'hidden';
                        }
                        
                        function closeSidebar() {
                            sidebar.classList.remove('mobile-open');
                            sidebarOverlay.classList.remove('active');
                            document.body.style.overflow = '';
                        }
                        
                        // Toggle sidebar when menu button is clicked
                        mobileMenuBtn.addEventListener('click', function() {
                            if (sidebar.classList.contains('mobile-open')) {
                                closeSidebar();
                            } else {
                                openSidebar();
                            }
                        });
                        
                        // Close sidebar when overlay is clicked
                        sidebarOverlay.addEventListener('click', function() {
                            closeSidebar();
                        });
                        
                        // Close sidebar when clicking on sidebar links (mobile)
                        const sidebarLinks = sidebar.querySelectorAll('.sidebar-link');
                        sidebarLinks.forEach(link => {
                            link.addEventListener('click', function() {
                                if (window.innerWidth <= 768) {
                                    closeSidebar();
                                }
                            });
                        });
                        
                        // Handle window resize
                        window.addEventListener('resize', function() {
                            if (window.innerWidth > 768) {
                                closeSidebar();
                            }
                        });
                    });

                    // Navigation Bar Functions
                    const workDayConfig = <?php echo json_encode($workDayStatus, 15, 512) ?>;
                    let isWorkTimerRunning = false;
                    let startTime = null;
                    let timerInterval = null;
                    let totalWorkTime = 0; // in seconds

                    let checkInDateTime = null;
                    let currentDate = new Date().toDateString();
                    let autoCheckoutInFlight = false;

                    function formatTargetHours(hours) {
                        const totalSec = Math.round((hours || 8) * 3600);
                        const hh = Math.floor(totalSec / 3600);
                        const mm = Math.floor((totalSec % 3600) / 60);
                        const ss = totalSec % 60;
                        return `${String(hh).padStart(2, '0')}:${String(mm).padStart(2, '0')}:${String(ss).padStart(2, '0')}`;
                    }

                    function updateWorkTimerTarget(data) {
                        const el = document.getElementById('workTimerTarget');
                        if (!el || !workDayConfig.show_button) return;
                        if (data.is_working) {
                            const required = data.required_daily_hours || data.work_day?.daily_hours || workDayConfig.daily_hours || 8;
                            el.textContent = `/ ${formatTargetHours(required)}`;
                            el.classList.remove('hidden');
                        } else {
                            el.classList.add('hidden');
                        }
                    }

                    function hideMustStartBanner() {
                        const banner = document.getElementById('work-day-must-start-banner');
                        if (banner) banner.remove();
                    }

                    function triggerAutoCheckoutIfNeeded(data) {
                        if (!data.should_auto_checkout || autoCheckoutInFlight) return false;
                        autoCheckoutInFlight = true;
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        fetch('/attendances/auto-check-out', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        })
                        .then(r => r.json())
                        .then(result => {
                            autoCheckoutInFlight = false;
                            if (result.success) {
                                showNotification(result.message || 'تم إيقاف يوم العمل تلقائياً', 'info');
                                loadTimerState();
                            }
                        })
                        .catch(() => { autoCheckoutInFlight = false; });
                        return true;
                    }

                    function processWorkDayApiData(data) {
                        updateWorkTimerTarget(data);
                        if (data.is_working || data.current_status === 'completed') {
                            hideMustStartBanner();
                        }
                        if (triggerAutoCheckoutIfNeeded(data)) {
                            return true;
                        }
                        return false;
                    }
                    
                    // Load saved timer state from localStorage and sync with attendance
                    function loadTimerState() {
                        // Always check attendance status from server first
                        fetch('/attendances/current-work-time')
                        .then(response => response.json())
                        .then(data => {
                            if (processWorkDayApiData(data)) {
                                return;
                            }
                            // Check if date has changed - reset timer if new day
                            const today = new Date().toDateString();
                            if (today !== currentDate) {
                                currentDate = today;
                                checkInDateTime = null;
                                isWorkTimerRunning = false;
                                startTime = null;
                                totalWorkTime = 0;
                                if (timerInterval) {
                                    clearInterval(timerInterval);
                                    timerInterval = null;
                                }
                            }
                            
                            // Check if working - use is_working OR check if has check_in and no check_out
                            const isWorking = data.is_working || (data.check_in_time && !data.check_out_time && data.current_status !== 'completed');
                            
                            if (isWorking && (data.check_in_datetime || data.check_in_time)) {
                                // Employee is currently working according to attendance system
                                isWorkTimerRunning = true;
                                
                                // Get check_in datetime - try multiple formats
                                if (data.check_in_datetime) {
                                    checkInDateTime = data.check_in_datetime;
                                    startTime = new Date(data.check_in_datetime);
                                } else if (data.check_in_time) {
                                    // Build datetime from check_in_time and today's date
                                    const todayDate = new Date();
                                    const [hours, minutes, seconds] = data.check_in_time.split(':');
                                    startTime = new Date(todayDate.getFullYear(), todayDate.getMonth(), todayDate.getDate(), parseInt(hours), parseInt(minutes), parseInt(seconds || 0));
                                    checkInDateTime = startTime.toISOString();
                                }
                                
                                // Update UI to show timer is running
                                const startDayText = document.getElementById('startDayText');
                                const workTimer = document.getElementById('workTimer');
                                const startDayBtn = document.getElementById('startDayBtn');
                                
                                if (startDayText) {
                                    startDayText.textContent = 'إيقاف التايمر';
                                }
                                
                                if (workTimer) {
                                    // Remove hidden class and force display
                                    workTimer.classList.remove('hidden');
                                    workTimer.style.display = 'block';
                                    workTimer.style.visibility = 'visible';
                                    workTimer.style.opacity = '0.9';
                                    workTimer.textContent = data.work_time || '00:00:00';
                                }
                                
                                if (startDayBtn) {
                                    // Update button style to red (stop timer)
                                    startDayBtn.style.background = 'linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)';
                                    startDayBtn.classList.remove('from-green-600', 'to-green-700', 'hover:from-green-700', 'hover:to-green-800');
                                    startDayBtn.classList.add('from-red-600', 'to-red-700', 'hover:from-red-700', 'hover:to-red-800');
                                }
                                
                                // Update mobile button
                                const mobileStartDayText = document.getElementById('startDayTextMobile');
                                const mobileTimer = document.getElementById('workTimerMobile');
                                if (mobileStartDayText) {
                                    mobileStartDayText.textContent = 'إيقاف التايمر';
                                }
                                if (mobileTimer) {
                                    mobileTimer.classList.remove('hidden');
                                    mobileTimer.style.display = 'block';
                                    mobileTimer.style.visibility = 'visible';
                                    mobileTimer.textContent = data.work_time || '00:00:00';
                                }
                                
                                // Start the timer display immediately
                                if (timerInterval) {
                                    clearInterval(timerInterval);
                                }
                                // Update immediately first
                                updateTimerDisplay();
                                // Then start interval
                                timerInterval = setInterval(updateTimerDisplay, 1000);
                            } else if (data.current_status === 'completed') {
                                // Employee has checked out today
                                isWorkTimerRunning = false;
                                startTime = null;
                                checkInDateTime = null;
                                
                                if (timerInterval) {
                                    clearInterval(timerInterval);
                                    timerInterval = null;
                                }
                                
                                // Update UI
                                const startDayText = document.getElementById('startDayText');
                                const workTimer = document.getElementById('workTimer');
                                const startDayBtn = document.getElementById('startDayBtn');
                                
                                if (startDayText) startDayText.textContent = 'بدء اليوم';
                                if (workTimer) workTimer.classList.add('hidden');
                                if (startDayBtn) {
                                    startDayBtn.classList.remove('from-red-600', 'to-red-700', 'hover:from-red-700', 'hover:to-red-800');
                                    startDayBtn.classList.add('from-green-600', 'to-green-700', 'hover:from-green-700', 'hover:to-green-800');
                                }
                                
                                // Update mobile button
                                const mobileStartDayText = document.getElementById('startDayTextMobile');
                                const mobileTimer = document.getElementById('workTimerMobile');
                                if (mobileStartDayText) {
                                    mobileStartDayText.textContent = 'بدء اليوم';
                                }
                                if (mobileTimer) {
                                    mobileTimer.classList.add('hidden');
                                }
                            } else {
                                // Employee is not working
                                isWorkTimerRunning = false;
                                startTime = null;
                                checkInDateTime = null;
                                
                                if (timerInterval) {
                                    clearInterval(timerInterval);
                                    timerInterval = null;
                                }
                                
                                // Update UI
                                const startDayText = document.getElementById('startDayText');
                                const workTimer = document.getElementById('workTimer');
                                const startDayBtn = document.getElementById('startDayBtn');
                                
                                if (startDayText) startDayText.textContent = 'بدء اليوم';
                                if (workTimer) workTimer.classList.add('hidden');
                                if (startDayBtn) {
                                    startDayBtn.classList.remove('from-red-600', 'to-red-700', 'hover:from-red-700', 'hover:to-red-800');
                                    startDayBtn.classList.add('from-green-600', 'to-green-700', 'hover:from-green-700', 'hover:to-green-800');
                                }
                                
                                // Update mobile button
                                const mobileStartDayText = document.getElementById('startDayTextMobile');
                                const mobileTimer = document.getElementById('workTimerMobile');
                                if (mobileStartDayText) {
                                    mobileStartDayText.textContent = 'بدء اليوم';
                                }
                                if (mobileTimer) {
                                    mobileTimer.classList.add('hidden');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error checking attendance status:', error);
                            // If error, don't show timer
                            isWorkTimerRunning = false;
                            startTime = null;
                            checkInDateTime = null;
                        });
                    }

                    // Save timer state to localStorage
                    function saveTimerState() {
                        const state = {
                            isRunning: isWorkTimerRunning,
                            startTime: startTime ? startTime.toISOString() : null,
                            totalWorkTime: totalWorkTime
                        };
                        localStorage.setItem('workTimerState', JSON.stringify(state));
                    }

                    function toggleWorkTimer() {
                        if (workDayConfig.on_leave) {
                            showNotification('أنت في إجازة معتمدة اليوم — لا يلزم بدء يوم العمل.', 'info');
                            return;
                        }
                        if (!isWorkTimerRunning) {
                            checkIn();
                        } else {
                            checkOut();
                        }
                    }
                    
                    // Check in function
                    function checkIn() {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (!csrfToken) {
                            showNotification('خطأ في التوكن، يرجى إعادة تحميل الصفحة', 'error');
                            return;
                        }

                        fetch('/attendances/check-in', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (response.status === 419) {
                                showNotification('انتهت صلاحية الجلسة، يرجى إعادة تحميل الصفحة', 'error');
                                setTimeout(() => window.location.reload(), 2000);
                                return;
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (!data) return;
                            if (data.success) {
                                // Reload timer state from server to get accurate check_in time
                                loadTimerState();
                                
                                // Show notification
                                showNotification(data.message, 'success');
                            } else {
                                // If already checked in, check current status and show timer
                                if (data.error && data.error.includes('تم تسجيل الحضور مسبقاً')) {
                                    // Load timer state to show the timer
                                    loadTimerState();
                                    // Don't show error, just show that timer is running
                                    showNotification('تم تسجيل الحضور مسبقاً - التايمر يعمل', 'info');
                                } else {
                                    showNotification(data.error || 'حدث خطأ في تسجيل الحضور', 'error');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error checking in:', error);
                            // On error, try to load timer state anyway in case attendance exists
                            loadTimerState();
                            showNotification('حدث خطأ في تسجيل الحضور. يرجى المحاولة مرة أخرى أو إعادة تحميل الصفحة', 'error');
                        });
                    }
                    
                    // Check out function
                    function checkOut() {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (!csrfToken) {
                            showNotification('خطأ في التوكن، يرجى إعادة تحميل الصفحة', 'error');
                            return;
                        }

                        fetch('/attendances/check-out', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (response.status === 419) {
                                showNotification('انتهت صلاحية الجلسة، يرجى إعادة تحميل الصفحة', 'error');
                                setTimeout(() => window.location.reload(), 2000);
                                return;
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (!data) return;
                            if (data.success) {
                                // Stop timer
                                isWorkTimerRunning = false;
                                startTime = null;
                                checkInDateTime = null;
                                
                                if (timerInterval) {
                                    clearInterval(timerInterval);
                                    timerInterval = null;
                                }
                                
                                // Update UI
                                const startDayText = document.getElementById('startDayText');
                                const workTimer = document.getElementById('workTimer');
                                const startDayBtn = document.getElementById('startDayBtn');
                                
                                if (startDayText) startDayText.textContent = 'بدء اليوم';
                                if (workTimer) workTimer.classList.add('hidden');
                                if (startDayBtn) {
                                    startDayBtn.classList.remove('from-red-600', 'to-red-700', 'hover:from-red-700', 'hover:to-red-800');
                                    startDayBtn.classList.add('from-green-600', 'to-green-700', 'hover:from-green-700', 'hover:to-green-800');
                                }
                                
                                // Update mobile button
                                const mobileStartDayText = document.getElementById('startDayTextMobile');
                                const mobileTimer = document.getElementById('workTimerMobile');
                                if (mobileStartDayText) {
                                    mobileStartDayText.textContent = 'بدء اليوم';
                                }
                                if (mobileTimer) {
                                    mobileTimer.classList.add('hidden');
                                }
                                
                                // Show notification
                                showNotification(data.message, 'success');
                                
                                // Reload page after 2 seconds to show updated data
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            } else {
                                showNotification(data.error || 'حدث خطأ في تسجيل الانصراف', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error checking out:', error);
                            showNotification('حدث خطأ في تسجيل الانصراف. يرجى المحاولة مرة أخرى أو إعادة تحميل الصفحة', 'error');
                        });
                    }

                    function startTimer() {
                        startTime = new Date();
                        isWorkTimerRunning = true;
                        
                        // Update button text and style
                        document.getElementById('startDayText').textContent = 'إيقاف التايمر';
                        document.getElementById('workTimer').classList.remove('hidden');
                        document.getElementById('startDayBtn').classList.remove('from-green-600', 'to-green-700', 'hover:from-green-700', 'hover:to-green-800');
                        document.getElementById('startDayBtn').classList.add('from-red-600', 'to-red-700', 'hover:from-red-700', 'hover:to-red-800');
                        
                        // Update mobile button
                        const mobileStartDayText = document.getElementById('startDayTextMobile');
                        const mobileTimer = document.getElementById('workTimerMobile');
                        if (mobileStartDayText) {
                            mobileStartDayText.textContent = 'إيقاف التايمر';
                        }
                        if (mobileTimer) {
                            mobileTimer.classList.remove('hidden');
                        }
                        
                        // Start the timer display
                        timerInterval = setInterval(updateTimerDisplay, 1000);
                        
                        // Save state
                        saveTimerState();
                        
                        showNotification('تم بدء التايمر بنجاح', 'success');
                    }

                    function stopTimer() {
                        if (timerInterval) {
                            clearInterval(timerInterval);
                            timerInterval = null;
                        }
                        
                        // Calculate work time
                        const endTime = new Date();
                        const sessionTime = Math.floor((endTime - startTime) / 1000);
                        totalWorkTime += sessionTime;
                        
                        isWorkTimerRunning = false;
                        
                        // Update button text and style
                        document.getElementById('startDayText').textContent = 'بدء اليوم';
                        document.getElementById('workTimer').classList.add('hidden');
                        document.getElementById('startDayBtn').classList.remove('from-red-600', 'to-red-700', 'hover:from-red-700', 'hover:to-red-800');
                        document.getElementById('startDayBtn').classList.add('from-green-600', 'to-green-700', 'hover:from-green-700', 'hover:to-green-800');
                        
                        // Update mobile button
                        const mobileStartDayText = document.getElementById('startDayTextMobile');
                        const mobileTimer = document.getElementById('workTimerMobile');
                        if (mobileStartDayText) {
                            mobileStartDayText.textContent = 'بدء اليوم';
                        }
                        if (mobileTimer) {
                            mobileTimer.classList.add('hidden');
                        }
                        
                        // Save state
                        saveTimerState();
                        
                        // Show work session summary
                        const sessionHours = Math.floor(sessionTime / 3600);
                        const sessionMinutes = Math.floor((sessionTime % 3600) / 60);
                        const sessionSeconds = sessionTime % 60;
                        
                        const totalHours = Math.floor(totalWorkTime / 3600);
                        const totalMinutes = Math.floor((totalWorkTime % 3600) / 60);
                        
                        showNotification(`جلسة العمل: ${sessionHours.toString().padStart(2, '0')}:${sessionMinutes.toString().padStart(2, '0')}:${sessionSeconds.toString().padStart(2, '0')} | إجمالي: ${totalHours}:${totalMinutes.toString().padStart(2, '0')}`, 'info');
                    }

                    function updateTimerDisplay() {
                        // Always get time from attendance API to ensure accuracy
                        fetch('/attendances/current-work-time')
                        .then(response => response.json())
                        .then(data => {
                            if (processWorkDayApiData(data)) {
                                return;
                            }
                            // Check if date has changed
                            const today = new Date().toDateString();
                            if (today !== currentDate) {
                                currentDate = today;
                                checkInDateTime = null;
                                isWorkTimerRunning = false;
                                startTime = null;
                                
                                if (timerInterval) {
                                    clearInterval(timerInterval);
                                    timerInterval = null;
                                }
                                
                                const workTimer = document.getElementById('workTimer');
                                const mobileTimer = document.getElementById('workTimerMobile');
                                if (workTimer) {
                                    workTimer.textContent = '00:00:00';
                                    workTimer.classList.add('hidden');
                                }
                                if (mobileTimer) {
                                    mobileTimer.textContent = '00:00:00';
                                    mobileTimer.classList.add('hidden');
                                }
                                return;
                            }
                            
                            if (data.is_working && data.work_time) {
                                // Update timer from server data
                                const workTimer = document.getElementById('workTimer');
                                const mobileTimer = document.getElementById('workTimerMobile');
                                
                                if (workTimer) {
                                    workTimer.textContent = data.work_time;
                                    workTimer.classList.remove('hidden');
                                    workTimer.style.display = 'block';
                                    workTimer.style.visibility = 'visible';
                                    workTimer.style.opacity = '0.9';
                                }
                                if (mobileTimer) {
                                    mobileTimer.textContent = data.work_time;
                                    mobileTimer.classList.remove('hidden');
                                    mobileTimer.style.display = 'block';
                                    mobileTimer.style.visibility = 'visible';
                                }
                                
                                // Update check-in datetime if changed
                                if (data.check_in_datetime && data.check_in_datetime !== checkInDateTime) {
                                    checkInDateTime = data.check_in_datetime;
                                    startTime = new Date(data.check_in_datetime);
                                }
                                
                                // Ensure button text shows "إيقاف التايمر"
                                const startDayText = document.getElementById('startDayText');
                                if (startDayText) {
                                    startDayText.textContent = 'إيقاف التايمر';
                                }
                                
                                // Ensure button style is red
                                const startDayBtn = document.getElementById('startDayBtn');
                                if (startDayBtn) {
                                    startDayBtn.style.background = 'linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)';
                                }
                            } else if (data.current_status === 'completed' || !data.is_working) {
                                // Timer stopped or completed
                                isWorkTimerRunning = false;
                                startTime = null;
                                checkInDateTime = null;
                                
                                if (timerInterval) {
                                    clearInterval(timerInterval);
                                    timerInterval = null;
                                }
                                
                                const workTimer = document.getElementById('workTimer');
                                const mobileTimer = document.getElementById('workTimerMobile');
                                if (workTimer) {
                                    workTimer.classList.add('hidden');
                                }
                                if (mobileTimer) {
                                    mobileTimer.classList.add('hidden');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error updating timer:', error);
                        });
                    }

                    // Keep the old function for backward compatibility
                    function startDay() {
                        toggleWorkTimer();
                    }

                    function toggleUserDropdown() {
                        const dropdown = document.getElementById('userDropdown');
                        const isHidden = dropdown.classList.contains('hidden');
                        
                        dropdown.classList.toggle('hidden');
                        
                        if (!isHidden) {
                            // Position dropdown to stay within viewport
                            positionDropdown(dropdown);
                        }
                        
                        // Close dropdown when clicking outside
                        document.addEventListener('click', function(event) {
                            if (!event.target.closest('.relative')) {
                                dropdown.classList.add('hidden');
                            }
                        });
                    }

                    function positionDropdown(dropdown) {
                        const rect = dropdown.getBoundingClientRect();
                        const viewportWidth = window.innerWidth;
                        const viewportHeight = window.innerHeight;
                        
                        // Check if dropdown goes beyond right edge
                        if (rect.right > viewportWidth) {
                            dropdown.classList.remove('left-0');
                            dropdown.classList.add('right-0');
                        } else {
                            dropdown.classList.remove('right-0');
                            dropdown.classList.add('left-0');
                        }
                        
                        // Check if dropdown goes beyond bottom edge
                        if (rect.bottom > viewportHeight) {
                            dropdown.classList.remove('top-full');
                            dropdown.classList.add('bottom-full');
                        } else {
                            dropdown.classList.remove('bottom-full');
                            dropdown.classList.add('top-full');
                        }
                    }

                    function openProfile() {
                        // Redirect to profile page
                        window.location.href = '/profile';
                    }

                    function closeNotificationsDropdown() {
                        document.querySelectorAll('.notifications-dropdown').forEach(el => el.remove());
                    }

                    function positionNotificationsDropdown(dropdown, btn) {
                        const panel = dropdown.querySelector('.notifications-dropdown-panel');
                        const rect = btn.getBoundingClientRect();
                        const panelWidth = panel ? panel.offsetWidth : 352;
                        let left = rect.left + (rect.width / 2) - (panelWidth / 2);
                        left = Math.max(12, Math.min(left, window.innerWidth - panelWidth - 12));
                        dropdown.style.position = 'fixed';
                        dropdown.style.top = (rect.bottom + 8) + 'px';
                        dropdown.style.left = left + 'px';
                        dropdown.style.zIndex = '9999';
                    }

                    window.markNotificationRead = function (notificationId, btn) {
                        fetch(`/notifications/${notificationId}/mark-read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (!data.success) return;
                            const row = document.querySelector(`[data-notification-id="${notificationId}"]`);
                            if (row) {
                                row.style.background = '';
                                row.style.borderColor = '';
                                row.classList.remove('border-r-4');
                                row.querySelectorAll('button').forEach(b => b.remove());
                                row.querySelector('.flex-shrink-0.relative span.rounded-full')?.remove();
                            }
                            if (typeof updateUnreadNotificationsCount === 'function') {
                                updateUnreadNotificationsCount();
                            }
                        })
                        .catch(err => console.error(err));
                    };

                    window.markAllNotificationsDropdownRead = function (btn) {
                        btn.disabled = true;
                        fetch('<?php echo e(route('notifications.mark-all-read')); ?>', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            body: new URLSearchParams({ filter: 'unread', _token: document.querySelector('meta[name="csrf-token"]').content }),
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                closeNotificationsDropdown();
                                if (typeof updateUnreadNotificationsCount === 'function') {
                                    updateUnreadNotificationsCount();
                                }
                            } else {
                                btn.disabled = false;
                            }
                        })
                        .catch(() => { btn.disabled = false; });
                    };

                    function toggleNotifications(event) {
                        if (event) {
                            event.preventDefault();
                            event.stopPropagation();
                        }

                        const existing = document.querySelector('.notifications-dropdown');
                        if (existing) {
                            closeNotificationsDropdown();
                            return;
                        }

                        const btn = document.getElementById('top-bar-notifications-btn');
                        if (!btn) {
                            window.location.href = '<?php echo e(route("notifications.index")); ?>';
                            return;
                        }

                        const dropdown = document.createElement('div');
                        dropdown.className = 'notifications-dropdown font-tajawal';
                        dropdown.innerHTML = '<div class="notifications-dropdown-panel w-[min(22rem,calc(100vw-1.25rem))] rounded-2xl border border-gray-200 bg-white shadow-2xl p-6 text-center text-sm text-gray-500">جاري التحميل...</div>';
                        document.body.appendChild(dropdown);
                        positionNotificationsDropdown(dropdown, btn);
                        btn.setAttribute('aria-expanded', 'true');

                        fetch('<?php echo e(route("notifications.index")); ?>?dropdown=1', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                            credentials: 'same-origin',
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('HTTP ' + response.status);
                            return response.text();
                        })
                        .then(html => {
                            dropdown.innerHTML = html;
                            positionNotificationsDropdown(dropdown, btn);
                            setTimeout(() => {
                                document.addEventListener('click', function closeDropdown(e) {
                                    if (!dropdown.contains(e.target) && !e.target.closest('#top-bar-notifications-wrap')) {
                                        closeNotificationsDropdown();
                                        btn.setAttribute('aria-expanded', 'false');
                                        document.removeEventListener('click', closeDropdown);
                                    }
                                });
                            }, 100);
                        })
                        .catch(error => {
                            console.error('Error fetching notifications:', error);
                            closeNotificationsDropdown();
                            window.location.href = '<?php echo e(route("notifications.index")); ?>';
                        });
                    }
                    
                    function formatTime(date) {
                        const now = new Date();
                        const diff = now - date;
                        const seconds = Math.floor(diff / 1000);
                        const minutes = Math.floor(seconds / 60);
                        const hours = Math.floor(minutes / 60);
                        const days = Math.floor(hours / 24);
                        
                        if (days > 0) return `منذ ${days} يوم`;
                        if (hours > 0) return `منذ ${hours} ساعة`;
                        if (minutes > 0) return `منذ ${minutes} دقيقة`;
                        return 'الآن';
                    }

                    function openSettings() {
                        // Create settings modal
                        const modal = document.createElement('div');
                        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                        modal.innerHTML = `
                            <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">الإعدادات</h3>
                                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-700">الوضع الليلي</span>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-700">الإشعارات</span>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" checked class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 mt-6">
                                    <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 text-gray-600 hover:text-gray-800">إلغاء</button>
                                    <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">حفظ</button>
                                </div>
                            </div>
                        `;
                        
                        document.body.appendChild(modal);
                        
                        // Close modal when clicking outside
                        modal.addEventListener('click', function(e) {
                            if (e.target === modal) {
                                modal.remove();
                            }
                        });
                    }

                    function showNotification(message, type = 'info') {
                        const notification = document.createElement('div');
                        const colors = {
                            success: 'bg-green-500',
                            error: 'bg-red-500',
                            info: 'bg-blue-500',
                            warning: 'bg-yellow-500'
                        };
                        
                        notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300`;
                        notification.textContent = message;
                        
                        document.body.appendChild(notification);
                        
                        // Animate in
                        setTimeout(() => {
                            notification.classList.remove('translate-x-full');
                        }, 100);
                        
                        // Remove after 3 seconds
                        setTimeout(() => {
                            notification.classList.add('translate-x-full');
                            setTimeout(() => {
                                notification.remove();
                            }, 300);
                        }, 3000);
                    }
                </script>

<script>
// Update unread messages count in sidebar
function updateUnreadMessagesCount() {
    fetch('<?php echo e(route("messages.unread-count")); ?>', {
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json; charset=utf-8'
        }
    })
        .then(response => response.json())
        .then(data => {
            const countElement = document.getElementById('unread-messages-count');
            if (data.count > 0) {
                countElement.textContent = data.count;
                countElement.classList.remove('hidden');
            } else {
                countElement.classList.add('hidden');
            }
        })
        .catch(error => console.error('Error updating unread messages count:', error));
}

// Global notification system
let lastNotificationCheck = 0;

// Update unread notifications count in sidebar and top bar, and show popup if new
function updateUnreadNotificationsCount() {
    fetch('<?php echo e(route("notifications.unread-count")); ?>', {
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json; charset=utf-8'
        }
    })
        .then(response => response.json())
        .then(data => {
            // تحديث عداد الإشعارات في السايدبار
            const sidebarCountElement = document.getElementById('unread-notifications-count');
            if (sidebarCountElement) {
                if (data.count > 0) {
                    sidebarCountElement.textContent = data.count;
                    sidebarCountElement.classList.remove('hidden');
                } else {
                    sidebarCountElement.classList.add('hidden');
                }
            }
            
            // تحديث عداد الإشعارات في الشريط العلوي
            const topBarCountElement = document.getElementById('top-bar-notifications-count');
            if (topBarCountElement) {
                if (data.count > 0) {
                    topBarCountElement.textContent = data.count;
                    topBarCountElement.classList.remove('hidden');
                } else {
                    topBarCountElement.classList.add('hidden');
                }
            }
            
            if (data.count > lastNotificationCheck && data.latest) {
                showGlobalNotification(data.latest.title, data.latest.message, data.latest.url);
                lastNotificationCheck = data.count;
            } else if (data.count > lastNotificationCheck) {
                showGlobalNotification('إشعار جديد!', 'لديك إشعار جديد في النظام', '<?php echo e(route("notifications.index")); ?>');
                lastNotificationCheck = data.count;
            }
        })
        .catch(error => console.error('Error updating unread notifications count:', error));
}

function showGlobalNotification(title, message, url) {
    url = url || '<?php echo e(route("notifications.index")); ?>';
    const popup = document.createElement('div');
    popup.className = 'fixed top-4 right-4 bg-gradient-to-r from-blue-500 to-indigo-500 text-white px-6 py-4 rounded-lg shadow-lg z-[100] max-w-sm transform transition-all duration-300 translate-x-full cursor-pointer font-tajawal';
    popup.innerHTML = `
        <div class="flex items-start space-x-3" role="button" tabindex="0">
            <div class="flex-shrink-0">
                <div class="h-8 w-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5V7a7 7 0 00-14 0v5l-5 5h5m10 0v1a3 3 0 01-6 0v-1m6 0H9" />
                    </svg>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold">${title}</p>
                <p class="text-sm opacity-90 mt-1 line-clamp-2">${message}</p>
            </div>
            <button type="button" class="popup-close text-white hover:text-gray-200 transition-colors shrink-0">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    `;
    popup.querySelector('.popup-close')?.addEventListener('click', function (e) {
        e.stopPropagation();
        popup.remove();
    });
    popup.addEventListener('click', function () { window.location.href = url; });
    
    document.body.appendChild(popup);
    
    // إظهار الإشعار
    setTimeout(() => {
        popup.classList.remove('translate-x-full');
    }, 100);
    
    // إزالة الإشعار بعد 5 ثوان
    setTimeout(() => {
        popup.classList.add('translate-x-full');
        setTimeout(() => {
            if (popup.parentNode) {
                popup.remove();
            }
        }, 300);
    }, 5000);
}

// Update counts on page load and every 10 seconds
updateUnreadMessagesCount();
updateUnreadNotificationsCount();
setInterval(updateUnreadMessagesCount, 10000);
setInterval(updateUnreadNotificationsCount, 10000);
</script>

<?php echo $__env->make('partials.client-search-select-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.developer-search-select-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.ui-compact-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\xampp\htdocs\afaq\resources\views\layouts\app.blade.php ENDPATH**/ ?>