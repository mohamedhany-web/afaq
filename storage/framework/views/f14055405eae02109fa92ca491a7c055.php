<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('page-title', 'بوابة العملاء'); ?> - <?php echo e(\App\Helpers\SettingsHelper::getCompanyName()); ?></title>

    <?php $faviconUrl = \App\Helpers\SettingsHelper::getFaviconUrl(); ?>
    <link rel="icon" type="image/x-icon" href="<?php echo e($faviconUrl ?: '/favicon.ico'); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e($faviconUrl ?: '/favicon.ico'); ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;600;700;900&family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { tajawal: ['Tajawal','sans-serif'], cairo: ['Cairo','sans-serif'] } } }
        }
    </script>

    <style>
        :root { --brand: <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>; }
        .btn-brand { background: linear-gradient(135deg, var(--brand) 0%, color-mix(in srgb, var(--brand) 85%, #000) 100%); }
    </style>
</head>
<body class="h-full bg-gray-50 text-gray-900 font-tajawal overflow-x-hidden">
<?php
    $account = \Illuminate\Support\Facades\Auth::guard('client')->user();
    $client = $account?->client;
    $cPortal = $account;
?>

<div class="min-h-screen flex">
    
    <aside class="hidden lg:flex w-72 shrink-0 flex-col border-l border-gray-200 bg-white">
        <div class="h-16 px-5 flex items-center justify-between border-b border-gray-100">
            <a href="<?php echo e(route('client.dashboard')); ?>" class="flex items-center gap-3 min-w-0">
                <div class="h-10 w-10 rounded-xl overflow-hidden flex items-center justify-center border border-gray-200 bg-white shrink-0">
                    <?php $logoUrl = \App\Helpers\SettingsHelper::getLogoUrl(); ?>
                    <?php if($logoUrl): ?>
                        <img src="<?php echo e($logoUrl); ?>" class="h-9 w-9 object-contain" alt="Logo">
                    <?php else: ?>
                        <div class="h-9 w-9 rounded-lg text-white flex items-center justify-center" style="background: var(--brand)">
                            <span class="font-bold"><?php echo e(mb_substr(\App\Helpers\SettingsHelper::getCompanyName(),0,1)); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="min-w-0">
                    <div class="font-extrabold font-cairo truncate"><?php echo e(\App\Helpers\SettingsHelper::getCompanyName()); ?></div>
                    <div class="text-xs text-gray-500 truncate">بوابة عملاء Solvesta</div>
                </div>
            </a>
        </div>

        <div class="p-5 border-b border-gray-100">
            <div class="text-xs text-gray-500">مرحباً</div>
            <div class="font-extrabold text-gray-900 truncate"><?php echo e($client?->name ?? $account?->name ?? 'عميل'); ?></div>
            <div class="text-xs text-gray-500 truncate"><?php echo e($account?->email); ?></div>
        </div>

        <nav class="p-4 space-y-2 text-sm font-semibold">
            <a href="<?php echo e(route('client.dashboard')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 transition <?php echo e(request()->routeIs('client.dashboard') ? 'ring-2 ring-black/5 text-gray-900' : 'text-gray-700'); ?>">
                <span class="inline-flex h-2.5 w-2.5 rounded-full" style="background:var(--brand)"></span>
                لوحة العميل
            </a>
            <a href="<?php echo e(route('client.notifications')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 transition <?php echo e(request()->routeIs('client.notifications*') ? 'ring-2 ring-black/5 text-gray-900' : 'text-gray-700'); ?>">
                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-slate-500"></span>
                الإشعارات
            </a>
            <a href="<?php echo e(route('client.projects')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 transition <?php echo e(request()->routeIs('client.projects') ? 'ring-2 ring-black/5 text-gray-900' : 'text-gray-700'); ?>">
                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                المشاريع
            </a>
            <?php if($cPortal && $cPortal->canAccessBilling()): ?>
            <a href="<?php echo e(route('client.invoices')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 transition <?php echo e(request()->routeIs('client.invoices') ? 'ring-2 ring-black/5 text-gray-900' : 'text-gray-700'); ?>">
                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                الفواتير
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('client.service-reports')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 transition <?php echo e(request()->routeIs('client.service-reports', 'client.service-reports.download') ? 'ring-2 ring-black/5 text-gray-900' : 'text-gray-700'); ?>">
                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-violet-500"></span>
                تقارير الخدمة
            </a>
            <a href="<?php echo e(route('client.documents')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 transition <?php echo e(request()->routeIs('client.documents*') ? 'ring-2 ring-black/5 text-gray-900' : 'text-gray-700'); ?>">
                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-indigo-500"></span>
                المستندات المشتركة
            </a>
            <?php if($cPortal && $cPortal->canAccessTechnicalRequests()): ?>
            <a href="<?php echo e(route('client.website-issues.index')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 transition <?php echo e(request()->routeIs('client.website-issues.*') ? 'ring-2 ring-black/5 text-gray-900' : 'text-gray-700'); ?>">
                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-orange-500"></span>
                بلاغات الموقع
            </a>
            <a href="<?php echo e(route('client.meeting-requests.index')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 transition <?php echo e(request()->routeIs('client.meeting-requests.*') ? 'ring-2 ring-black/5 text-gray-900' : 'text-gray-700'); ?>">
                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-cyan-500"></span>
                طلبات الاجتماعات
            </a>
            <a href="<?php echo e(route('client.calendar')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 transition <?php echo e(request()->routeIs('client.calendar') ? 'ring-2 ring-black/5 text-gray-900' : 'text-gray-700'); ?>">
                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-teal-500"></span>
                التقويم
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('client.support.tickets')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 transition <?php echo e(request()->routeIs('client.support.*') ? 'ring-2 ring-black/5 text-gray-900' : 'text-gray-700'); ?>">
                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                الدعم الفني
            </a>
            <a href="<?php echo e(route('client.help')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 transition <?php echo e(request()->routeIs('client.help') ? 'ring-2 ring-black/5 text-gray-900' : 'text-gray-700'); ?>">
                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-gray-400"></span>
                المساعدة
            </a>
        </nav>

        <div class="mt-auto p-4 border-t border-gray-100">
            <form method="POST" action="<?php echo e(route('client.logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 font-extrabold text-gray-800 transition">
                    تسجيل الخروج
                </button>
            </form>
            <a href="<?php echo e(route('website.home')); ?>" class="mt-3 block text-center text-xs text-gray-500 hover:text-gray-700">
                العودة إلى موقع الشركة
            </a>
        </div>
    </aside>

    
    <div class="flex-1 min-w-0">
        
        <div class="sticky top-0 z-30 bg-white/85 backdrop-blur border-b border-gray-100">
            <div class="h-16 px-4 sm:px-6 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <button type="button" class="lg:hidden inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-white hover:bg-gray-50 transition"
                            aria-label="فتح القائمة" id="clientMenuBtn">
                        <svg class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="min-w-0">
                        <div class="font-extrabold font-cairo truncate"><?php echo $__env->yieldContent('page-title', 'بوابة العملاء'); ?></div>
                        <div class="text-xs text-gray-500 truncate"><?php echo e($client?->company ?? 'Solvesta Client'); ?></div>
                    </div>
                </div>
                <div class="flex items-center shrink-0">
                    <details class="relative z-50">
                        <summary class="flex items-center gap-2 cursor-pointer select-none list-none rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-extrabold text-gray-800 shadow-sm hover:bg-gray-50 [&::-webkit-details-marker]:hidden">
                            <svg class="w-4 h-4 shrink-0 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            <span>جديد</span>
                            <svg class="w-3.5 h-3.5 shrink-0 text-gray-500 mr-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </summary>
                        <div class="absolute end-0 top-[calc(100%+6px)] w-56 rounded-xl border border-gray-200 bg-white py-1 shadow-xl text-sm font-semibold text-gray-800 overflow-hidden">
                            <a href="<?php echo e(route('client.support.tickets.create')); ?>" class="flex items-center gap-2 px-3 py-2.5 hover:bg-gray-50 border-b border-gray-100">تذكرة</a>
                            <?php if($cPortal && $cPortal->canAccessTechnicalRequests()): ?>
                            <a href="<?php echo e(route('client.website-issues.create')); ?>" class="flex items-center gap-2 px-3 py-2.5 hover:bg-amber-50 border-b border-gray-100 text-amber-950">بلاغ موقع</a>
                            <a href="<?php echo e(route('client.meeting-requests.create')); ?>" class="flex items-center gap-2 px-3 py-2.5 hover:bg-cyan-50 text-cyan-950">طلب اجتماع</a>
                            <?php endif; ?>
                        </div>
                    </details>
                </div>
            </div>
        </div>

        <main class="p-4 sm:p-6 lg:p-8">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
</div>


<div id="clientDrawer" class="fixed inset-0 z-50 hidden" style="max-width:100vw; overflow:hidden">
    <div id="clientOverlay" class="absolute inset-0 bg-black/40 opacity-0" style="transition:opacity .25s ease"></div>
    <div id="clientPanel" class="absolute top-0 right-0 h-full w-[86%] max-w-sm bg-white shadow-2xl border-l border-gray-100"
         style="transform:translateX(100%); transition:transform .3s cubic-bezier(.32,.72,0,1)">
        <div class="h-16 px-4 flex items-center justify-between border-b border-gray-100">
            <div class="font-extrabold font-cairo">القائمة</div>
            <button type="button" id="clientCloseBtn" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-white hover:bg-gray-50 transition" aria-label="إغلاق">
                <svg class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="p-4 space-y-2 text-sm font-semibold text-gray-700">
            <a href="<?php echo e(route('client.dashboard')); ?>" class="block px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 <?php echo e(request()->routeIs('client.dashboard') ? 'ring-2 ring-black/5 text-gray-900' : ''); ?>">لوحة العميل</a>
            <a href="<?php echo e(route('client.notifications')); ?>" class="block px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 <?php echo e(request()->routeIs('client.notifications*') ? 'ring-2 ring-black/5 text-gray-900' : ''); ?>">الإشعارات</a>
            <a href="<?php echo e(route('client.projects')); ?>" class="block px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 <?php echo e(request()->routeIs('client.projects') ? 'ring-2 ring-black/5 text-gray-900' : ''); ?>">المشاريع</a>
            <?php if($cPortal && $cPortal->canAccessBilling()): ?>
            <a href="<?php echo e(route('client.invoices')); ?>" class="block px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 <?php echo e(request()->routeIs('client.invoices') ? 'ring-2 ring-black/5 text-gray-900' : ''); ?>">الفواتير</a>
            <?php endif; ?>
            <a href="<?php echo e(route('client.service-reports')); ?>" class="block px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 <?php echo e(request()->routeIs('client.service-reports', 'client.service-reports.download') ? 'ring-2 ring-black/5 text-gray-900' : ''); ?>">تقارير الخدمة</a>
            <a href="<?php echo e(route('client.documents')); ?>" class="block px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 <?php echo e(request()->routeIs('client.documents*') ? 'ring-2 ring-black/5 text-gray-900' : ''); ?>">المستندات المشتركة</a>
            <?php if($cPortal && $cPortal->canAccessTechnicalRequests()): ?>
            <a href="<?php echo e(route('client.website-issues.index')); ?>" class="block px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 <?php echo e(request()->routeIs('client.website-issues.*') ? 'ring-2 ring-black/5 text-gray-900' : ''); ?>">بلاغات الموقع</a>
            <a href="<?php echo e(route('client.meeting-requests.index')); ?>" class="block px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 <?php echo e(request()->routeIs('client.meeting-requests.*') ? 'ring-2 ring-black/5 text-gray-900' : ''); ?>">طلبات الاجتماعات</a>
            <a href="<?php echo e(route('client.calendar')); ?>" class="block px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 <?php echo e(request()->routeIs('client.calendar') ? 'ring-2 ring-black/5 text-gray-900' : ''); ?>">التقويم</a>
            <?php endif; ?>
            <a href="<?php echo e(route('client.support.tickets')); ?>" class="block px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 <?php echo e(request()->routeIs('client.support.*') ? 'ring-2 ring-black/5 text-gray-900' : ''); ?>">الدعم الفني</a>
            <a href="<?php echo e(route('client.help')); ?>" class="block px-4 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 <?php echo e(request()->routeIs('client.help') ? 'ring-2 ring-black/5 text-gray-900' : ''); ?>">المساعدة</a>

            <a href="<?php echo e(route('website.home')); ?>" class="mt-4 block text-center text-xs text-gray-500 hover:text-gray-700">العودة إلى موقع الشركة</a>
            <form method="POST" action="<?php echo e(route('client.logout')); ?>" class="mt-3">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full px-4 py-3 rounded-2xl text-white font-extrabold btn-brand shadow-md hover:shadow-lg transition">تسجيل الخروج</button>
            </form>
        </div>
    </div>
</div>

<script>
    (() => {
        const btn = document.getElementById('clientMenuBtn');
        const drawer = document.getElementById('clientDrawer');
        const overlay = document.getElementById('clientOverlay');
        const panel = document.getElementById('clientPanel');
        const closeBtn = document.getElementById('clientCloseBtn');
        if (!btn || !drawer || !overlay || !panel || !closeBtn) return;

        let open = false;
        const openDrawer = () => {
            if (open) return;
            open = true;
            document.body.style.overflow = 'hidden';
            drawer.classList.remove('hidden');
            requestAnimationFrame(() => {
                overlay.style.opacity = '1';
                panel.style.transform = 'translateX(0)';
            });
        };
        const closeDrawer = () => {
            if (!open) return;
            open = false;
            document.body.style.overflow = '';
            overlay.style.opacity = '0';
            panel.style.transform = 'translateX(100%)';
            setTimeout(() => drawer.classList.add('hidden'), 300);
        };

        btn.addEventListener('click', openDrawer);
        closeBtn.addEventListener('click', closeDrawer);
        overlay.addEventListener('click', closeDrawer);
        panel.addEventListener('click', (e) => { if (e.target.closest('a')) closeDrawer(); });
        window.addEventListener('keydown', (e) => { if (e.key === 'Escape' && open) closeDrawer(); });
    })();
</script>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\afaq\resources\views\layouts\client.blade.php ENDPATH**/ ?>