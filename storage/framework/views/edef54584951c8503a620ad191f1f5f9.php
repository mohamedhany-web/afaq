<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(\App\Helpers\SettingsHelper::getCompanyName()); ?> - بوابة العملاء</title>

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

    <?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>
    <style>
        :root { --brand: <?php echo e($themeColor); ?>; }
        .btn-brand { background: linear-gradient(135deg, var(--brand) 0%, color-mix(in srgb, var(--brand) 85%, #000) 100%); }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-4 font-tajawal overflow-x-hidden">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <?php
                $logoUrl = \App\Helpers\SettingsHelper::getLogoUrl();
                $companyName = \App\Helpers\SettingsHelper::getCompanyName();
            ?>

            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-6 shadow-lg"
                 style="background: linear-gradient(135deg, var(--brand) 0%, color-mix(in srgb, var(--brand) 85%, #000) 100%);">
                <?php if($logoUrl): ?>
                    <img src="<?php echo e($logoUrl); ?>" alt="Logo" class="w-full h-full object-contain rounded-xl">
                <?php else: ?>
                    <div class="text-white font-extrabold font-cairo text-3xl"><?php echo e(mb_substr($companyName,0,1)); ?></div>
                <?php endif; ?>
            </div>

            <h1 class="text-2xl sm:text-3xl font-extrabold font-cairo text-gray-900 mb-2">بوابة العملاء</h1>
            <p class="text-gray-600">لعملاء <?php echo e($companyName); ?> فقط</p>
            <div class="w-20 h-1 bg-gray-200 rounded-full mx-auto mt-4"></div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-7 border border-gray-100">
            <div class="text-center mb-7">
                <h2 class="text-xl font-extrabold text-gray-900 font-cairo">تسجيل الدخول</h2>
                <p class="mt-2 text-sm text-gray-500">أدخل بيانات حسابك للوصول إلى لوحتك</p>
            </div>

            <form method="POST" action="<?php echo e(route('client.login.submit')); ?>" class="space-y-5">
                <?php echo csrf_field(); ?>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">البريد الإلكتروني</label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 bg-red-50 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           placeholder="example@company.com">
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">كلمة المرور</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 bg-red-50 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           placeholder="••••••••">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="remember" class="rounded border-gray-300">
                        تذكرني
                    </label>
                    <a href="<?php echo e(route('website.contact')); ?>" class="text-sm font-bold" style="color:var(--brand)">
                        تواصل معنا
                    </a>
                </div>

                <button type="submit" class="w-full text-white font-extrabold py-3.5 px-6 rounded-xl btn-brand shadow-lg hover:shadow-xl transition">
                    دخول بوابة العملاء
                </button>
            </form>
        </div>

        <div class="text-center mt-7 text-xs text-gray-500">
            <a href="<?php echo e(route('website.home')); ?>" class="hover:text-gray-700">العودة إلى موقع الشركة</a>
        </div>
    </div>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\afaq\resources\views\client-auth\login.blade.php ENDPATH**/ ?>