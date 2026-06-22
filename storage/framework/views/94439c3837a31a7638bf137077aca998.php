<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8mb4">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(\App\Helpers\SettingsHelper::getSystemName()); ?> - التحقق من الكود</title>
    
    <!-- Favicon -->
    <?php $faviconUrl = \App\Helpers\SettingsHelper::getFaviconUrl(); ?>
    <link rel="icon" type="image/x-icon" href="<?php echo e($faviconUrl ?: '/favicon.ico'); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e($faviconUrl ?: '/favicon.ico'); ?>">

    <!-- Arabic Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;600;700;900&family=Cairo:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.0/dist/axios.min.js"></script>
    <script>
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        let token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        }
    </script>

    <style>
        body {
            font-family: 'Tajawal', 'Cairo', sans-serif;
        }
        .font-tajawal {
            font-family: 'Tajawal', sans-serif;
        }
        .font-cairo {
            font-family: 'Cairo', sans-serif;
        }
        
        /* Custom animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }
        
        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        .animate-pulse-slow {
            animation: pulse 2s ease-in-out infinite;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-4 font-tajawal">
    <!-- Main Container -->
    <div class="w-full max-w-md animate-fadeInUp">
        <?php
            $logoPath = \App\Helpers\SettingsHelper::getLogoPath();
            $systemName = \App\Helpers\SettingsHelper::getSystemName();
            $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
        ?>
        
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl ml-4 mb-6 shadow-lg" 
                 style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
                <?php if($logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath)): ?>
                    <!-- Custom Logo -->
                    <img src="<?php echo e(asset('storage/' . $logoPath)); ?>" alt="Logo" class="w-full h-full object-contain rounded-xl">
                <?php else: ?>
                    <!-- Default Logo -->
                    <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                <?php endif; ?>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-3 font-cairo"><?php echo e($systemName); ?></h1>
            <p class="text-gray-600 text-lg font-medium">التحقق من الكود</p>
            <div class="w-24 h-1 bg-gray-300 rounded-full mx-auto mt-4"></div>
        </div>

        <!-- Verification Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <!-- Form Header -->
            <div class="text-center mb-8">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-4 animate-float"
                     style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>15 0%, <?php echo e($themeColor); ?>25 100%);">
                    <svg class="h-8 w-8" style="color: <?php echo e($themeColor); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3 font-cairo">أدخل كود التحقق</h2>
                <p class="text-gray-600 text-sm font-tajawal">
                    تم إرسال كود التحقق إلى بريدك الإلكتروني
                </p>
                <?php if(auth()->guard()->check()): ?>
                <p class="mt-2 text-xs text-gray-500 font-tajawal">
                    <?php
                        $user = auth()->user();
                        $email = $user->email ?? null;
                        if (empty($email) && $user->employee && !empty($user->employee->email)) {
                            $email = $user->employee->email;
                        }
                        $maskedEmail = $email ? substr($email, 0, 3) . str_repeat('*', max(0, strpos($email, '@') - 3)) . substr($email, strpos($email, '@')) : 'البريد الإلكتروني المسجل';
                    ?>
                    البريد: <span class="font-medium text-gray-700"><?php echo e($maskedEmail); ?></span>
                </p>
                <?php endif; ?>
                <div class="w-16 h-0.5 bg-gray-300 rounded-full mx-auto mt-3"></div>
            </div>

            <!-- Success Message -->
            <?php if(session('success')): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl font-tajawal">
                <div class="flex items-center">
                    <svg class="w-5 h-5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span><?php echo e(session('success')); ?></span>
                </div>
            </div>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if($errors->has('error') || session('error')): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl font-tajawal">
                <div class="flex items-center">
                    <svg class="w-5 h-5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span><?php echo e($errors->first('error') ?: session('error')); ?></span>
                </div>
            </div>
            <?php endif; ?>

            <!-- Verification Form -->
            <form method="POST" action="<?php echo e(route('verification.verify')); ?>" class="space-y-6">
                <?php echo csrf_field(); ?>

                <!-- Code Input Field -->
                <div class="space-y-2">
                    <label for="code" class="block text-sm font-medium text-gray-700 font-tajawal">
                        الكود المكون من 6 أرقام
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input 
                            id="code" 
                            name="code" 
                            type="text" 
                            maxlength="6" 
                            pattern="[0-9]{6}"
                            autocomplete="one-time-code"
                            required 
                            autofocus
                            class="w-full pr-12 pl-4 py-5 bg-gray-50 border-2 border-gray-200 rounded-xl placeholder-gray-400 text-center text-3xl font-bold tracking-widest focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-tajawal transition-all duration-300 <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 bg-red-50 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="000000"
                            inputmode="numeric"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)"
                        >
                    </div>
                    <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-600 text-sm font-tajawal flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <?php echo e($message); ?>

                        </p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <p class="text-xs text-gray-500 text-center font-tajawal mt-2">
                        تحقق من بريدك الإلكتروني للحصول على الكود
                    </p>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-xl active:scale-95 font-cairo text-lg relative overflow-hidden group"
                    style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
                    <span class="relative z-10 flex items-center justify-center">
                        <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        التحقق من الكود
                    </span>
                    <div class="absolute inset-0 bg-white bg-opacity-20 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </button>
            </form>

            <!-- Resend Code -->
            <div class="mt-6 text-center">
                <form method="POST" action="<?php echo e(route('verification.resend')); ?>">
                    <?php echo csrf_field(); ?>
                    <button 
                        type="submit" 
                        class="text-sm font-medium font-tajawal transition-colors duration-200 hover:underline"
                        style="color: <?php echo e($themeColor); ?>"
                        onmouseover="this.style.color='<?php echo e($themeColor); ?>dd'"
                        onmouseout="this.style.color='<?php echo e($themeColor); ?>'">
                        <span class="flex items-center justify-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            إعادة إرسال الكود
                        </span>
                    </button>
                </form>
            </div>

            <!-- Info Box -->
            <div class="mt-6 p-4 rounded-xl font-tajawal" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>10 0%, <?php echo e($themeColor); ?>20 100%); border: 1px solid <?php echo e($themeColor); ?>30;">
                <div class="flex items-start">
                    <svg class="h-5 w-5 ml-2 flex-shrink-0 mt-0.5" style="color: <?php echo e($themeColor); ?>" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm" style="color: <?php echo e($themeColor); ?>cc;">
                        <p class="font-medium mb-1">ملاحظة:</p>
                        <p>الكود صالح لمدة 10 دقائق فقط. إذا لم تستلم الكود، تحقق من مجلد الرسائل غير المرغوب فيها.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <div class="flex items-center justify-center space-x-4 space-x-reverse mb-4">
                <div class="w-8 h-0.5 bg-gray-300 rounded-full"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                <div class="w-8 h-0.5 bg-gray-300 rounded-full"></div>
            </div>
            <p class="text-gray-500 font-tajawal text-sm">
                &copy; <?php echo e(date('Y')); ?> <?php echo e(\App\Helpers\SettingsHelper::getCompanyName()); ?>. جميع الحقوق محفوظة.
            </p>
            <p class="text-gray-400 font-tajawal text-xs mt-2">
                نظام إدارة متكامل ومتطور
            </p>
        </div>
    </div>

    <script>
        // Auto-focus and input handling
        document.addEventListener('DOMContentLoaded', function() {
            const codeInput = document.getElementById('code');
            
            // Focus on load
            codeInput.focus();
            
            // Keep only numbers
            codeInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
                
                // Auto-submit when 6 digits are entered (optional)
                if (this.value.length === 6) {
                    // Uncomment the following line to enable auto-submit
                    // this.form.submit();
                }
            });
            
            // Paste support
            codeInput.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasted = (e.clipboardData || window.clipboardData).getData('text');
                const numbers = pasted.replace(/[^0-9]/g, '').slice(0, 6);
                this.value = numbers;
                if (numbers.length === 6) {
                    // Uncomment to auto-submit on paste
                    // this.form.submit();
                }
            });
        });
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\auth\verify-code.blade.php ENDPATH**/ ?>