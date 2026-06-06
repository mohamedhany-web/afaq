<?php $__env->startSection('page-title', 'إعدادات النظام'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 sm:p-6 lg:p-8 border border-blue-100">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center">
                    <div class="h-12 w-12 sm:h-14 sm:w-14 lg:h-16 lg:w-16 bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl flex items-center justify-center ml-3 sm:ml-4 shadow-lg flex-shrink-0">
                        <svg class="h-6 w-6 sm:h-7 sm:w-7 lg:h-8 lg:w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-1">إعدادات النظام</h1>
                        <p class="text-gray-600 text-sm sm:text-base lg:text-lg">إدارة إعدادات النظام والمظهر والوظائف</p>
                    </div>
                </div>
                <button onclick="resetSettings()" class="w-full sm:w-auto bg-red-600 text-white px-5 py-2.5 sm:px-6 sm:py-3 rounded-lg hover:bg-red-700 transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl text-sm sm:text-base">
                    <svg class="h-4 w-4 sm:h-5 sm:w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    إعادة تعيين
                </button>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg" id="success-message">
            <?php echo e(session('success')); ?>

        </div>
        <script>
            // إعادة تحميل الصفحة بعد 1.5 ثانية لعرض اللوجو المرفوع
            setTimeout(function() {
                window.location.reload();
            }, 1500);
        </script>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('system-settings.update')); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <!-- General Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 sm:px-6 lg:px-8 py-4 sm:py-5 lg:py-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="h-9 w-9 sm:h-10 sm:w-10 bg-blue-100 rounded-lg flex items-center justify-center ml-3 flex-shrink-0">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900">الإعدادات العامة</h3>
                        <p class="text-xs sm:text-sm text-gray-600">إعدادات أساسية للنظام والشركة</p>
                    </div>
                </div>
            </div>
            <div class="p-4 sm:p-5 lg:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">اسم النظام</label>
                        <input type="text" name="settings[system_name]" value="<?php echo e(old('settings.system_name', isset($settings['general']) ? ($settings['general']->where('key', 'system_name')->first()?->value ?? '') : '')); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">اسم الشركة</label>
                        <input type="text" name="settings[company_name]" value="<?php echo e(old('settings.company_name', isset($settings['general']) ? ($settings['general']->where('key', 'company_name')->first()?->value ?? '') : '')); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">وصف النظام</label>
                        <textarea name="settings[system_description]" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?php echo e(old('settings.system_description', isset($settings['general']) ? ($settings['general']->where('key', 'system_description')->first()?->value ?? '') : '')); ?></textarea>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">عنوان الشركة</label>
                        <textarea name="settings[company_address]" rows="2" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?php echo e(old('settings.company_address', isset($settings['general']) ? ($settings['general']->where('key', 'company_address')->first()?->value ?? '') : '')); ?></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">هاتف الشركة</label>
                        <input type="text" name="settings[company_phone]" value="<?php echo e(old('settings.company_phone', isset($settings['general']) ? ($settings['general']->where('key', 'company_phone')->first()?->value ?? '') : '')); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">البريد الإلكتروني</label>
                        <input type="email" name="settings[company_email]" value="<?php echo e(old('settings.company_email', isset($settings['general']) ? ($settings['general']->where('key', 'company_email')->first()?->value ?? '') : '')); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>
        </div>

        <!-- Appearance Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-4 sm:px-6 lg:px-8 py-4 sm:py-5 lg:py-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="h-9 w-9 sm:h-10 sm:w-10 bg-purple-100 rounded-lg flex items-center justify-center ml-3 flex-shrink-0">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900">إعدادات المظهر</h3>
                        <p class="text-xs sm:text-sm text-gray-600">تخصيص مظهر النظام والشعار</p>
                    </div>
                </div>
            </div>
            <div class="p-4 sm:p-5 lg:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">شعار الشركة</label>
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <?php
                                    $logoUrl = \App\Helpers\SettingsHelper::getLogoUrl();
                                ?>
                                <?php if($logoUrl): ?>
                                    <img src="<?php echo e($logoUrl); ?>" alt="Logo" class="h-16 w-16 object-contain border border-gray-300 rounded-lg">
                                <?php else: ?>
                                    <div class="h-16 w-16 bg-gray-200 border border-gray-300 rounded-lg flex items-center justify-center">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <input type="file" name="settings[logo]" id="logo_input" accept="image/*" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       onchange="previewLogo(this)">
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF (أقصى حجم: 2MB)</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">أيقونة المتصفح</label>
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <?php
                                    $faviconSetting = isset($settings['appearance']) ? $settings['appearance']->where('key', 'favicon')->first() : null;
                                    $faviconPath = $faviconSetting?->value;
                                ?>
                                <?php if($faviconPath && \Storage::disk('public')->exists($faviconPath)): ?>
                                    <img src="<?php echo e(asset('storage/' . $faviconPath)); ?>" alt="Favicon" class="h-8 w-8 object-contain border border-gray-300 rounded-lg">
                                <?php else: ?>
                                    <div class="h-8 w-8 bg-gray-200 border border-gray-300 rounded-lg flex items-center justify-center">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <input type="file" name="settings[favicon]" accept="image/*" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">ICO, PNG (أقصى حجم: 100KB)</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">اللون الرئيسي</label>
                        <input type="color" name="settings[theme_color]" value="<?php echo e(old('settings.theme_color', isset($settings['appearance']) ? ($settings['appearance']->where('key', 'theme_color')->first()?->value ?? '#2563eb') : '#2563eb')); ?>" 
                               class="w-full h-10 px-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">لون الشريط الجانبي</label>
                        <input type="color" name="settings[sidebar_color]" value="<?php echo e(old('settings.sidebar_color', isset($settings['appearance']) ? ($settings['appearance']->where('key', 'sidebar_color')->first()?->value ?? '#1f2937') : '#1f2937')); ?>" 
                               class="w-full h-10 px-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">حجم اللوجو</label>
                        <?php $logoSize = isset($settings['appearance']) ? ($settings['appearance']->where('key', 'logo_size')->first()?->value ?? 'medium') : 'medium'; ?>
                        <select name="settings[logo_size]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="small" <?php echo e($logoSize == 'small' ? 'selected' : ''); ?>>صغير (32x32 بكسل)</option>
                            <option value="medium" <?php echo e($logoSize == 'medium' ? 'selected' : ''); ?>>متوسط (48x48 بكسل)</option>
                            <option value="large" <?php echo e($logoSize == 'large' ? 'selected' : ''); ?>>كبير (64x64 بكسل)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-4 sm:px-6 lg:px-8 py-4 sm:py-5 lg:py-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="h-9 w-9 sm:h-10 sm:w-10 bg-green-100 rounded-lg flex items-center justify-center ml-3 flex-shrink-0">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900">إعدادات النظام</h3>
                        <p class="text-xs sm:text-sm text-gray-600">إعدادات تقنية للنظام</p>
                    </div>
                </div>
            </div>
            <div class="p-4 sm:p-5 lg:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">المنطقة الزمنية</label>
                        <select name="settings[timezone]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <?php $timezone = isset($settings['system']) ? ($settings['system']->where('key', 'timezone')->first()?->value ?? 'Africa/Cairo') : 'Africa/Cairo'; ?>
                            <option value="Africa/Cairo" <?php echo e($timezone == 'Africa/Cairo' ? 'selected' : ''); ?>>القاهرة (GMT+2)</option>
                            <option value="Asia/Riyadh" <?php echo e($timezone == 'Asia/Riyadh' ? 'selected' : ''); ?>>الرياض (GMT+3)</option>
                            <option value="Asia/Dubai" <?php echo e($timezone == 'Asia/Dubai' ? 'selected' : ''); ?>>دبي (GMT+4)</option>
                            <option value="Asia/Kuwait" <?php echo e($timezone == 'Asia/Kuwait' ? 'selected' : ''); ?>>الكويت (GMT+3)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">لغة النظام</label>
                        <select name="settings[language]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <?php $language = isset($settings['system']) ? ($settings['system']->where('key', 'language')->first()?->value ?? 'ar') : 'ar'; ?>
                            <option value="ar" <?php echo e($language == 'ar' ? 'selected' : ''); ?>>العربية</option>
                            <option value="en" <?php echo e($language == 'en' ? 'selected' : ''); ?>>English</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">تنسيق التاريخ</label>
                        <select name="settings[date_format]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <?php $dateFormat = isset($settings['system']) ? ($settings['system']->where('key', 'date_format')->first()?->value ?? 'Y-m-d') : 'Y-m-d'; ?>
                            <option value="Y-m-d" <?php echo e($dateFormat == 'Y-m-d' ? 'selected' : ''); ?>>YYYY-MM-DD</option>
                            <option value="d-m-Y" <?php echo e($dateFormat == 'd-m-Y' ? 'selected' : ''); ?>>DD-MM-YYYY</option>
                            <option value="m/d/Y" <?php echo e($dateFormat == 'm/d/Y' ? 'selected' : ''); ?>>MM/DD/YYYY</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">تنسيق الوقت</label>
                        <select name="settings[time_format]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <?php $timeFormat = isset($settings['system']) ? ($settings['system']->where('key', 'time_format')->first()?->value ?? 'H:i') : 'H:i'; ?>
                            <option value="H:i" <?php echo e($timeFormat == 'H:i' ? 'selected' : ''); ?>>24 ساعة (HH:MM)</option>
                            <option value="h:i A" <?php echo e($timeFormat == 'h:i A' ? 'selected' : ''); ?>>12 ساعة (HH:MM AM/PM)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-orange-50 to-amber-50 px-4 sm:px-6 lg:px-8 py-4 sm:py-5 lg:py-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="h-9 w-9 sm:h-10 sm:w-10 bg-orange-100 rounded-lg flex items-center justify-center ml-3 flex-shrink-0">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM12 19c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V9m0 0l3 3m-3-3l-3 3" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900">إعدادات الإشعارات</h3>
                        <p class="text-xs sm:text-sm text-gray-600">تخصيص أنواع الإشعارات المطلوبة</p>
                    </div>
                </div>
            </div>
            <div class="p-4 sm:p-5 lg:p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900">إشعارات البريد الإلكتروني</h4>
                            <p class="text-sm text-gray-600">إرسال إشعارات عبر البريد الإلكتروني</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="settings[email_notifications]" value="1" 
                                   <?php echo e((isset($settings['notifications']) && ($settings['notifications']->where('key', 'email_notifications')->first()?->value ?? '1')) == '1' ? 'checked' : ''); ?>

                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900">إشعارات الرسائل النصية</h4>
                            <p class="text-sm text-gray-600">إرسال إشعارات عبر الرسائل النصية</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="settings[sms_notifications]" value="1" 
                                   <?php echo e((isset($settings['notifications']) && ($settings['notifications']->where('key', 'sms_notifications')->first()?->value ?? '0')) == '1' ? 'checked' : ''); ?>

                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900">الإشعارات الفورية</h4>
                            <p class="text-sm text-gray-600">عرض إشعارات فورية في المتصفح</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="settings[push_notifications]" value="1" 
                                   <?php echo e((isset($settings['notifications']) && ($settings['notifications']->where('key', 'push_notifications')->first()?->value ?? '1')) == '1' ? 'checked' : ''); ?>

                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-50 to-amber-50 px-4 sm:px-6 lg:px-8 py-4 sm:py-5 lg:py-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="h-9 w-9 sm:h-10 sm:w-10 bg-yellow-100 rounded-lg flex items-center justify-center ml-3 flex-shrink-0">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900">الإعدادات المالية</h3>
                        <p class="text-xs sm:text-sm text-gray-600">إعدادات البنوك والحسابات والمعلومات المالية</p>
                    </div>
                </div>
            </div>
            <div class="p-4 sm:p-5 lg:p-6">
                <div class="space-y-6">
                    <!-- Bank 1 -->
                    <div class="border border-gray-200 rounded-lg p-4 sm:p-6 bg-gray-50">
                        <h4 class="text-base font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="h-5 w-5 ml-2 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            البنك الأساسي
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">اسم البنك</label>
                                <input type="text" name="settings[bank_name]" value="<?php echo e(old('settings.bank_name', isset($settings['financial']) ? ($settings['financial']->where('key', 'bank_name')->first()?->value ?? '') : '')); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent" placeholder="مثال: البنك الأهلي المصري">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">رقم الحساب</label>
                                <input type="text" name="settings[bank_account_number]" value="<?php echo e(old('settings.bank_account_number', isset($settings['financial']) ? ($settings['financial']->where('key', 'bank_account_number')->first()?->value ?? '') : '')); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent" placeholder="مثال: 12345678901234567890">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">IBAN</label>
                                <input type="text" name="settings[bank_iban]" value="<?php echo e(old('settings.bank_iban', isset($settings['financial']) ? ($settings['financial']->where('key', 'bank_iban')->first()?->value ?? '') : '')); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent" placeholder="مثال: EG123456789012345678901234">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">اسم صاحب الحساب</label>
                                <input type="text" name="settings[bank_account_holder]" value="<?php echo e(old('settings.bank_account_holder', isset($settings['financial']) ? ($settings['financial']->where('key', 'bank_account_holder')->first()?->value ?? '') : '')); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent" placeholder="مثال: اسم الشركة">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Swift Code</label>
                                <input type="text" name="settings[bank_swift]" value="<?php echo e(old('settings.bank_swift', isset($settings['financial']) ? ($settings['financial']->where('key', 'bank_swift')->first()?->value ?? '') : '')); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent" placeholder="مثال: NBEGEGCX">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">فرع البنك</label>
                                <input type="text" name="settings[bank_branch]" value="<?php echo e(old('settings.bank_branch', isset($settings['financial']) ? ($settings['financial']->where('key', 'bank_branch')->first()?->value ?? '') : '')); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent" placeholder="مثال: فرع المعادي">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="border border-gray-200 rounded-lg p-4 sm:p-6 bg-gray-50">
                        <h4 class="text-base font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="h-5 w-5 ml-2 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            طرق الدفع المقبولة
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">طرق الدفع</label>
                                <textarea name="settings[payment_methods]" rows="3" 
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent" placeholder="مثال: تحويل بنكي، شيك، نقدي، بطاقة ائتمانية"><?php echo e(old('settings.payment_methods', isset($settings['financial']) ? ($settings['financial']->where('key', 'payment_methods')->first()?->value ?? '') : '')); ?></textarea>
                                <p class="text-xs text-gray-500 mt-1">اذكر طرق الدفع المقبولة (مفصولة بفواصل)</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">مدة السداد الافتراضية (أيام)</label>
                                <input type="number" name="settings[default_payment_period]" value="<?php echo e(old('settings.default_payment_period', isset($settings['financial']) ? ($settings['financial']->where('key', 'default_payment_period')->first()?->value ?? '30') : '30')); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent" min="1" max="365">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">نسبة الضريبة الافتراضية (%)</label>
                                <input type="number" name="settings[default_tax_rate]" value="<?php echo e(old('settings.default_tax_rate', isset($settings['financial']) ? ($settings['financial']->where('key', 'default_tax_rate')->first()?->value ?? '0') : '0')); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent" min="0" max="100" step="0.01">
                            </div>
                        </div>
                    </div>

                    <!-- Additional Financial Info -->
                    <div class="border border-gray-200 rounded-lg p-4 sm:p-6 bg-gray-50">
                        <h4 class="text-base font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="h-5 w-5 ml-2 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            معلومات مالية إضافية
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">الرقم الضريبي</label>
                                <input type="text" name="settings[tax_number]" value="<?php echo e(old('settings.tax_number', isset($settings['financial']) ? ($settings['financial']->where('key', 'tax_number')->first()?->value ?? '') : '')); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent" placeholder="مثال: 123456789">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">السجل التجاري</label>
                                <input type="text" name="settings[commercial_registration]" value="<?php echo e(old('settings.commercial_registration', isset($settings['financial']) ? ($settings['financial']->where('key', 'commercial_registration')->first()?->value ?? '') : '')); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent" placeholder="مثال: 123456-78">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">ملاحظات مالية للفواتير</label>
                                <textarea name="settings[invoice_financial_notes]" rows="3" 
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent" placeholder="ملاحظات مالية تظهر في الفواتير"><?php echo e(old('settings.invoice_financial_notes', isset($settings['financial']) ? ($settings['financial']->where('key', 'invoice_financial_notes')->first()?->value ?? '') : '')); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- WhatsApp Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 lg:p-8 mb-6">
            <div class="flex items-center mb-6">
                <div class="h-12 w-12 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center ml-4">
                    <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900">إعدادات الواتساب</h3>
                    <p class="text-xs sm:text-sm text-gray-600">إعدادات إرسال رسائل الواتساب التلقائي</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- WhatsApp Default Number -->
                <div>
                    <label for="whatsapp_default_number" class="block text-sm font-medium text-gray-700 mb-2">
                        رقم الواتساب الافتراضي
                    </label>
                    <input type="text" 
                           id="whatsapp_default_number" 
                           name="whatsapp_default_number" 
                           value="<?php echo e(old('whatsapp_default_number', \App\Models\SystemSetting::get('whatsapp_default_number', '201044610510'))); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 <?php $__errorArgs = ['whatsapp_default_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           placeholder="201044610510">
                    <?php $__errorArgs = ['whatsapp_default_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <p class="mt-2 text-sm text-gray-500">رقم الهاتف الافتراضي المستخدم لإرسال رسائل الواتساب</p>
                </div>

                <!-- WhatsApp Enabled -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        تفعيل إرسال الواتساب
                    </label>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="whatsapp_enabled" 
                                   value="1" 
                                   <?php echo e(\App\Models\SystemSetting::get('whatsapp_enabled', '1') === '1' ? 'checked' : ''); ?>

                                   class="text-green-600 focus:ring-green-500">
                            <span class="mr-2 text-sm text-gray-700">مفعل</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="whatsapp_enabled" 
                                   value="0" 
                                   <?php echo e(\App\Models\SystemSetting::get('whatsapp_enabled', '1') === '0' ? 'checked' : ''); ?>

                                   class="text-green-600 focus:ring-green-500">
                            <span class="mr-2 text-sm text-gray-700">معطل</span>
                        </label>
                    </div>
                </div>

                <!-- Auto Open WhatsApp -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        فتح الواتساب تلقائياً
                    </label>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="whatsapp_auto_open" 
                                   value="1" 
                                   <?php echo e(\App\Models\SystemSetting::get('whatsapp_auto_open', '1') === '1' ? 'checked' : ''); ?>

                                   class="text-green-600 focus:ring-green-500">
                            <span class="mr-2 text-sm text-gray-700">نعم</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="whatsapp_auto_open" 
                                   value="0" 
                                   <?php echo e(\App\Models\SystemSetting::get('whatsapp_auto_open', '1') === '0' ? 'checked' : ''); ?>

                                   class="text-green-600 focus:ring-green-500">
                            <span class="mr-2 text-sm text-gray-700">لا</span>
                        </label>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">فتح الواتساب تلقائياً عند الضغط على زر الإرسال</p>
                </div>

                <!-- Test WhatsApp Button -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        اختبار الإرسال
                    </label>
                    <div class="flex gap-3">
                        <button type="button" 
                                onclick="testWhatsApp()" 
                                class="flex-1 bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition-all duration-200 flex items-center justify-center">
                            <svg class="h-5 w-5 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                            </svg>
                            <span>إرسال رسالة</span>
                        </button>
                        <button type="button" 
                                onclick="openWhatsAppContact(document.getElementById('whatsapp_default_number').value, 'الرقم الافتراضي')" 
                                class="flex-1 bg-green-500 text-white px-6 py-3 rounded-xl hover:bg-green-600 transition-all duration-200 flex items-center justify-center">
                            <svg class="h-5 w-5 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                            </svg>
                            <span>فتح محادثة</span>
                        </button>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">اختبر الإعدادات: إرسال رسالة أو فتح محادثة مباشرة</p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-center mt-6 sm:mt-8">
            <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 sm:px-10 lg:px-12 py-3 sm:py-3.5 lg:py-4 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl text-sm sm:text-base">
                <svg class="h-5 w-5 sm:h-6 sm:w-6 ml-2 sm:ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-lg font-semibold">حفظ الإعدادات</span>
            </button>
        </div>
    </form>
</div>

<!-- Reset Confirmation Modal -->
<div id="resetModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-0 border-0 w-96 shadow-2xl rounded-2xl bg-white overflow-hidden">
        <div class="bg-gradient-to-r from-red-50 to-pink-50 p-8">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 shadow-lg">
                    <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mt-6 mb-4">تأكيد إعادة التعيين</h3>
                <p class="text-gray-600 leading-relaxed">
                    هل أنت متأكد من إعادة تعيين جميع الإعدادات للقيم الافتراضية؟<br>
                    <span class="text-red-600 font-semibold">هذا الإجراء لا يمكن التراجع عنه.</span>
                </p>
            </div>
        </div>
        <div class="p-6 bg-white">
            <div class="flex gap-3 justify-center">
                <button onclick="confirmReset()" class="bg-red-600 text-white px-6 py-3 rounded-xl hover:bg-red-700 transition-all duration-200 flex items-center shadow-lg hover:shadow-xl">
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    نعم، إعادة تعيين
                </button>
                <button onclick="closeResetModal()" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-xl hover:bg-gray-300 transition-all duration-200 flex items-center">
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    إلغاء
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function resetSettings() {
    document.getElementById('resetModal').classList.remove('hidden');
}

function closeResetModal() {
    document.getElementById('resetModal').classList.add('hidden');
}

function confirmReset() {
    // إنشاء form وإرساله
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo e(route("system-settings.reset")); ?>';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '<?php echo e(csrf_token()); ?>';
    
    form.appendChild(csrfToken);
    document.body.appendChild(form);
    form.submit();
}

// Test WhatsApp function
function testWhatsApp() {
    const defaultNumber = document.getElementById('whatsapp_default_number').value || '201044610510';
    const testMessage = 'هذه رسالة تجريبية من نظام إدارة الشركة للتحقق من إعدادات الواتساب.';
    
    sendToWhatsApp('المدير', 'رسالة تجريبية', testMessage, defaultNumber);
}

// Preview logo when file is selected
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        const logoPreview = input.closest('.flex').querySelector('.flex-shrink-0');
        
        reader.onload = function(e) {
            // Remove existing preview or placeholder
            const existingImg = logoPreview.querySelector('img');
            const existingDiv = logoPreview.querySelector('div');
            
            if (existingImg) {
                existingImg.src = e.target.result;
            } else if (existingDiv) {
                // Replace placeholder with image
                existingDiv.innerHTML = `<img src="${e.target.result}" alt="Logo Preview" class="h-16 w-16 object-contain border border-gray-300 rounded-lg">`;
            } else {
                // Create new image element
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Logo Preview';
                img.className = 'h-16 w-16 object-contain border border-gray-300 rounded-lg';
                logoPreview.appendChild(img);
            }
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/system-settings/index.blade.php ENDPATH**/ ?>