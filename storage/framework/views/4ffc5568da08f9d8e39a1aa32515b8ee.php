<!DOCTYPE html>
<html lang="ar" dir="rtl" class="overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', \App\Helpers\SettingsHelper::getCompanyName()); ?></title>

    <?php $faviconUrl = \App\Helpers\SettingsHelper::getFaviconUrl(); ?>
    <link rel="icon" type="image/x-icon" href="<?php echo e($faviconUrl ?: '/favicon.ico'); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e($faviconUrl ?: '/favicon.ico'); ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;600;700;800;900&family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { tajawal: ['Tajawal','sans-serif'], cairo: ['Cairo','sans-serif'] } } }
        }
    </script>

    <?php
        $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
        $logoUrl = \App\Helpers\SettingsHelper::getLogoUrl();
        $companyName = \App\Helpers\SettingsHelper::getCompanyName();
        $systemName = \App\Helpers\SettingsHelper::getSystemName();
    ?>

    <style>
        :root { --brand: <?php echo e($themeColor); ?>; }
        .btn-brand { background: linear-gradient(135deg, var(--brand) 0%, color-mix(in srgb, var(--brand) 85%, #000) 100%); }
        .ring-brand:focus { box-shadow: 0 0 0 4px color-mix(in srgb, var(--brand) 25%, transparent); }
    </style>
</head>
<body class="bg-white text-gray-900 font-tajawal overflow-x-hidden">
    
    <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-3">
            
            <a href="<?php echo e(route('website.home')); ?>" class="flex items-center gap-3 min-w-0 shrink-0">
                <div class="h-10 w-10 rounded-xl overflow-hidden flex items-center justify-center border border-gray-200 bg-white shrink-0">
                    <?php if($logoUrl): ?>
                        <img src="<?php echo e($logoUrl); ?>" class="h-9 w-9 object-contain" alt="Logo">
                    <?php else: ?>
                        <div class="h-9 w-9 rounded-lg text-white flex items-center justify-center" style="background: var(--brand)">
                            <span class="font-bold"><?php echo e(mb_substr($systemName,0,1)); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="min-w-0 hidden sm:block">
                    <div class="font-extrabold font-cairo text-gray-900 truncate text-sm"><?php echo e($companyName); ?></div>
                    <div class="text-[11px] text-gray-500 truncate">Software &amp; AI Solutions</div>
                </div>
            </a>

            
            <nav class="hidden md:flex items-center gap-5 text-sm font-semibold text-gray-600">
                <a class="hover:text-gray-900 transition <?php echo e(request()->routeIs('website.home') ? 'text-gray-900' : ''); ?>" href="<?php echo e(route('website.home')); ?>">الرئيسية</a>
                <a class="hover:text-gray-900 transition <?php echo e(request()->routeIs('website.about') ? 'text-gray-900' : ''); ?>" href="<?php echo e(route('website.about')); ?>">عن الشركة</a>
                <a class="hover:text-gray-900 transition <?php echo e(request()->routeIs('website.services') ? 'text-gray-900' : ''); ?>" href="<?php echo e(route('website.services')); ?>">الخدمات</a>
                <a class="hover:text-gray-900 transition <?php echo e(request()->routeIs('website.training*') ? 'text-gray-900' : ''); ?>" href="<?php echo e(route('website.training')); ?>">التدريب</a>
                <a class="hover:text-gray-900 transition <?php echo e(request()->routeIs('website.case-studies.*') ? 'text-gray-900' : ''); ?>" href="<?php echo e(route('website.case-studies.index')); ?>">نماذج الأعمال</a>
                <a class="hover:text-gray-900 transition <?php echo e(request()->routeIs('website.pricing') ? 'text-gray-900' : ''); ?>" href="<?php echo e(route('website.pricing')); ?>">حلول الشركات</a>
                <a class="hover:text-gray-900 transition <?php echo e(request()->routeIs('website.contact') ? 'text-gray-900' : ''); ?>" href="<?php echo e(route('website.contact')); ?>">تواصل</a>
            </nav>

            
            <div class="hidden md:flex items-center gap-3 shrink-0">
                <a href="<?php echo e(route('client.login')); ?>" class="inline-flex px-5 py-2.5 rounded-xl text-white text-sm font-extrabold btn-brand shadow-md hover:shadow-lg transition">بوابة العملاء</a>
            </div>

            
            <button type="button" class="md:hidden inline-flex items-center justify-center h-10 w-10 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 transition shrink-0"
                    aria-label="فتح القائمة" id="mobileMenuBtn">
                <svg class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </header>

    
    <div id="mobileDrawer" class="fixed inset-0 z-50 hidden" style="max-width:100vw; overflow:hidden">
        
        <div id="mobileOverlay" class="absolute inset-0 bg-black/40 opacity-0 pointer-events-auto" style="transition:opacity .25s ease"></div>
        
        <div id="mobilePanel" class="absolute top-0 right-0 h-full w-[82%] max-w-xs bg-white shadow-2xl pointer-events-auto flex flex-col"
             style="transform:translateX(100%); transition:transform .3s cubic-bezier(.32,.72,0,1)"
             role="dialog" aria-modal="true" aria-label="قائمة الموقع">

            
            <div class="h-16 px-4 flex items-center justify-between border-b border-gray-100 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="h-9 w-9 rounded-lg overflow-hidden flex items-center justify-center border border-gray-200 bg-white shrink-0">
                        <?php if($logoUrl): ?>
                            <img src="<?php echo e($logoUrl); ?>" class="h-8 w-8 object-contain" alt="Logo">
                        <?php else: ?>
                            <div class="h-8 w-8 rounded-md text-white flex items-center justify-center text-sm" style="background: var(--brand)">
                                <?php echo e(mb_substr($systemName,0,1)); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="min-w-0">
                        <div class="font-extrabold font-cairo text-gray-900 truncate text-sm"><?php echo e($companyName); ?></div>
                        <div class="text-[11px] text-gray-500 truncate">Software &amp; AI Solutions</div>
                    </div>
                </div>
                <button type="button" id="mobileCloseBtn" class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 transition" aria-label="إغلاق">
                    <svg class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            
            <div class="flex-1 overflow-y-auto p-4">
                <nav class="grid gap-1.5">
                    <?php
                        $mobileLinks = [
                            ['route'=>'website.home','label'=>'الرئيسية'],
                            ['route'=>'website.about','label'=>'عن الشركة'],
                            ['route'=>'website.services','label'=>'الخدمات'],
                            ['route'=>'website.training','label'=>'التدريب'],
                            ['route'=>'website.case-studies.index','label'=>'نماذج الأعمال'],
                            ['route'=>'website.pricing','label'=>'حلول الشركات'],
                            ['route'=>'website.contact','label'=>'تواصل'],
                        ];
                    ?>
                    <?php $__currentLoopData = $mobileLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route($link['route'])); ?>"
                           class="block px-4 py-3 rounded-xl text-sm font-semibold transition
                                  <?php echo e(request()->routeIs($link['route']) ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <?php echo e($link['label']); ?>

                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </nav>
            </div>

            
            <div class="shrink-0 p-4 border-t border-gray-100">
                <div class="text-xs text-gray-500 mb-3">عميل لدى الشركة؟ ادخل إلى بوابتك</div>
                <a href="<?php echo e(route('client.login')); ?>" class="flex items-center justify-center w-full px-4 py-3 rounded-xl text-white text-sm font-extrabold btn-brand shadow-md hover:shadow-lg transition">
                    بوابة العملاء
                </a>
            </div>
        </div>
    </div>

    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <footer class="mt-16 bg-white border-t border-gray-200 text-gray-700 relative overflow-hidden">
        
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute top-0 left-0 right-0 h-[2px]" style="background: linear-gradient(90deg, transparent, var(--brand), transparent)"></div>
            <div class="absolute -top-24 right-[-70px] h-80 w-80 rounded-full blur-3xl opacity-[0.10]" style="background: var(--brand)"></div>
            <div class="absolute bottom-[-120px] left-[-90px] h-96 w-96 rounded-full blur-3xl opacity-[0.08]" style="background: var(--brand)"></div>
            <div class="absolute top-[35%] left-[10%] h-56 w-56 rounded-full blur-3xl opacity-[0.06]" style="background: var(--brand)"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="text-2xl font-extrabold font-cairo mb-3 text-gray-900"><?php echo e($companyName); ?></div>
                    <p class="text-gray-600 leading-relaxed">
                        نبني حلولًا تشغيلية وبنى تحتية رقمية للشركات — من التشغيل الداخلي إلى بوابة العميل وخدمة ما بعد البيع — بتجربة عربية حديثة ومعايير B2B.
                    </p>
                    <div class="mt-5 flex flex-col sm:flex-row gap-3">
                        <a href="<?php echo e(route('website.contact')); ?>"
                           class="inline-flex items-center justify-center px-5 py-3 rounded-2xl bg-gray-900 text-white font-extrabold hover:bg-black transition shadow-sm">
                            احجز جلسة
                        </a>
                        <a href="<?php echo e(route('client.login')); ?>"
                           class="inline-flex items-center justify-center px-5 py-3 rounded-2xl text-white font-extrabold btn-brand shadow-md hover:shadow-lg transition">
                            بوابة العملاء
                        </a>
                    </div>
                </div>

                <div>
                    <div class="font-extrabold mb-3 text-gray-900">روابط</div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <a class="block hover:text-gray-900 transition" href="<?php echo e(route('website.about')); ?>">عن الشركة</a>
                        <a class="block hover:text-gray-900 transition" href="<?php echo e(route('website.services')); ?>">الخدمات</a>
                        <a class="block hover:text-gray-900 transition" href="<?php echo e(route('website.training')); ?>">التدريب</a>
                        <a class="block hover:text-gray-900 transition" href="<?php echo e(route('website.case-studies.index')); ?>">نماذج الأعمال</a>
                        <a class="block hover:text-gray-900 transition" href="<?php echo e(route('website.pricing')); ?>">حلول الشركات</a>
                        <a class="block hover:text-gray-900 transition" href="<?php echo e(route('website.contact')); ?>">تواصل</a>
                    </div>
                </div>

                <div>
                    <div class="font-extrabold mb-3 text-gray-900">بيانات التواصل</div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div><span class="text-gray-500">هاتف:</span> <?php echo e(\App\Helpers\SettingsHelper::getCompanyPhone()); ?></div>
                        <div><span class="text-gray-500">بريد:</span> <?php echo e(\App\Helpers\SettingsHelper::getCompanyEmail()); ?></div>
                        <div><span class="text-gray-500">عنوان:</span> <?php echo e(\App\Helpers\SettingsHelper::getCompanyAddress()); ?></div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 mt-10 pt-6 text-sm text-gray-500 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div>© <?php echo e(date('Y')); ?> <?php echo e($companyName); ?>. جميع الحقوق محفوظة.</div>
                <div class="text-gray-600"><?php echo e(\App\Helpers\SettingsHelper::getSystemName()); ?></div>
            </div>
        </div>
    </footer>

    <script>
        (() => {
            const btn     = document.getElementById('mobileMenuBtn');
            const drawer  = document.getElementById('mobileDrawer');
            const overlay = document.getElementById('mobileOverlay');
            const panel   = document.getElementById('mobilePanel');
            const closeBtn= document.getElementById('mobileCloseBtn');
            if (!btn || !drawer || !overlay || !panel || !closeBtn) return;

            let isOpen = false;

            function openDrawer() {
                if (isOpen) return;
                isOpen = true;
                document.body.style.overflow = 'hidden';
                drawer.classList.remove('hidden');
                requestAnimationFrame(() => {
                    overlay.style.opacity = '1';
                    panel.style.transform = 'translateX(0)';
                });
            }

            function closeDrawer() {
                if (!isOpen) return;
                isOpen = false;
                document.body.style.overflow = '';
                overlay.style.opacity = '0';
                panel.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    drawer.classList.add('hidden');
                }, 300);
            }

            btn.addEventListener('click', openDrawer);
            closeBtn.addEventListener('click', closeDrawer);
            overlay.addEventListener('click', closeDrawer);
            panel.addEventListener('click', (e) => { if (e.target.closest('a')) closeDrawer(); });
            window.addEventListener('keydown', (e) => { if (e.key === 'Escape' && isOpen) closeDrawer(); });
        })();
    </script>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\afaq\resources\views\website\layout.blade.php ENDPATH**/ ?>