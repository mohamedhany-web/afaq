<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('page-title', 'بوابة المطور'); ?> - <?php echo e(\App\Helpers\SettingsHelper::getCompanyName()); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;600;700;900&display=swap" rel="stylesheet">
    <?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>
    <style>:root{--brand:<?php echo e($themeColor); ?>;} body{font-family:Tajawal,sans-serif}</style>
</head>
<body class="bg-gray-50 min-h-screen">
<?php $account = auth('developer')->user(); $developer = $account?->developer; ?>
<div class="min-h-screen flex">
    <aside class="hidden lg:flex w-64 flex-col bg-white border-l border-gray-200">
        <div class="p-5 border-b font-extrabold text-lg"><?php echo e($developer?->name ?? 'بوابة المطور'); ?></div>
        <nav class="p-4 space-y-1 text-sm font-semibold">
            <a href="<?php echo e(route('developer.dashboard')); ?>" class="block px-4 py-3 rounded-xl <?php echo e(request()->routeIs('developer.dashboard') ? 'text-white' : 'hover:bg-gray-50'); ?>" <?php if(request()->routeIs('developer.dashboard')): ?> style="background:var(--brand)" <?php endif; ?>>لوحة التحكم</a>
            <a href="<?php echo e(route('developer.projects.index')); ?>" class="block px-4 py-3 rounded-xl <?php echo e(request()->routeIs('developer.projects.*') ? 'text-white' : 'hover:bg-gray-50'); ?>" <?php if(request()->routeIs('developer.projects.*')): ?> style="background:var(--brand)" <?php endif; ?>>مشاريعي</a>
            <a href="<?php echo e(route('developer.portfolio.index')); ?>" class="block px-4 py-3 rounded-xl <?php echo e(request()->routeIs('developer.portfolio.*') ? 'text-white' : 'hover:bg-gray-50'); ?>" <?php if(request()->routeIs('developer.portfolio.*')): ?> style="background:var(--brand)" <?php endif; ?>>سابقة الأعمال</a>
            <a href="<?php echo e(route('developer.profile.edit')); ?>" class="block px-4 py-3 rounded-xl <?php echo e(request()->routeIs('developer.profile.*') ? 'text-white' : 'hover:bg-gray-50'); ?>" <?php if(request()->routeIs('developer.profile.*')): ?> style="background:var(--brand)" <?php endif; ?>>بيانات الشركة</a>
        </nav>
        <form method="POST" action="<?php echo e(route('developer.logout')); ?>" class="mt-auto p-4"><?php echo csrf_field(); ?><button class="w-full py-2 rounded-xl border text-sm font-bold">تسجيل الخروج</button></form>
    </aside>
    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-emerald-50 text-emerald-800 text-sm font-semibold"><?php echo e(session('success')); ?></div><?php endif; ?>
        <?php if(session('error')): ?><div class="mb-4 p-4 rounded-xl bg-red-50 text-red-800 text-sm font-semibold"><?php echo e(session('error')); ?></div><?php endif; ?>
        <?php echo $__env->yieldContent('content'); ?>
    </main>
</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\layouts\developer.blade.php ENDPATH**/ ?>