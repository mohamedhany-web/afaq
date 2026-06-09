<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="<?php echo e(route('dashboard')); ?>">
                        <?php if (isset($component)) { $__componentOriginal8892e718f3d0d7a916180885c6f012e7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8892e718f3d0d7a916180885c6f012e7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.application-logo','data' => ['class' => 'block h-9 w-auto fill-current text-gray-800 dark:text-gray-200']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('application-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'block h-9 w-auto fill-current text-gray-800 dark:text-gray-200']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $attributes = $__attributesOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $component = $__componentOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__componentOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <?php if (isset($component)) { $__componentOriginalc295f12dca9d42f28a259237a5724830 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc295f12dca9d42f28a259237a5724830 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-link','data' => ['href' => route('dashboard'),'active' => request()->routeIs('dashboard')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('dashboard')),'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('dashboard'))]); ?>
                        <?php echo e(__('Dashboard')); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc295f12dca9d42f28a259237a5724830)): ?>
<?php $attributes = $__attributesOriginalc295f12dca9d42f28a259237a5724830; ?>
<?php unset($__attributesOriginalc295f12dca9d42f28a259237a5724830); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc295f12dca9d42f28a259237a5724830)): ?>
<?php $component = $__componentOriginalc295f12dca9d42f28a259237a5724830; ?>
<?php unset($__componentOriginalc295f12dca9d42f28a259237a5724830); ?>
<?php endif; ?>
                </div>
            </div>

            <!-- Notifications and Settings -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                <!-- Notifications Dropdown -->
                <div x-data="{ open: false, notifications: [], unreadCount: 0, lastCheckTime: null }" 
                     x-init="
                        loadNotifications();
                        setInterval(loadNotifications, 10000); // Refresh every 10 seconds
                        setInterval(checkForNewNotifications, 5000); // Check for new notifications every 5 seconds
                     " 
                     class="relative">
                    <button @click="open = !open" 
                            class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5V7a7 7 0 00-14 0v5l-5 5h5m10 0v1a3 3 0 01-6 0v-1m6 0H9" />
                        </svg>
                        <span x-show="unreadCount > 0" 
                              x-text="unreadCount" 
                              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        </span>
                    </button>

                    <!-- Notifications Dropdown Menu -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="p-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">الإشعارات</h3>
                                <button @click="markAllAsRead()" 
                                        class="text-sm text-blue-600 hover:text-blue-800">
                                    تعيين الكل كمقروء
                                </button>
                            </div>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <template x-for="notification in notifications" :key="notification.id">
                                <div class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer"
                                     @click="markAsRead(notification.id); open = false">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full" 
                                                 x-show="!notification.is_read"></div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                                            <p class="text-sm text-gray-500 mt-1" x-text="notification.message"></p>
                                            <p class="text-xs text-gray-400 mt-1" x-text="formatDate(notification.created_at)"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="notifications.length === 0" class="p-4 text-center text-gray-500">
                                لا توجد إشعارات
                            </div>
                        </div>
                        <div class="p-4 border-t border-gray-200">
                            <a href="<?php echo e(route('notifications.index')); ?>" 
                               class="block text-center text-sm text-blue-600 hover:text-blue-800">
                                عرض جميع الإشعارات
                            </a>
                        </div>
                    </div>
                </div>

            <!-- Settings Dropdown -->
                <?php if (isset($component)) { $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown','data' => ['align' => 'right','width' => '48']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'right','width' => '48']); ?>
                     <?php $__env->slot('trigger', null, []); ?> 
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div><?php echo e(Auth::user()->name); ?></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                     <?php $__env->endSlot(); ?>

                     <?php $__env->slot('content', null, []); ?> 
                        <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('profile.edit')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('profile.edit'))]); ?>
                            <?php echo e(__('Profile')); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>

                        <!-- Authentication -->
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>

                            <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('logout'),'onclick' => 'event.preventDefault();
                                                this.closest(\'form\').submit();']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('logout')),'onclick' => 'event.preventDefault();
                                                this.closest(\'form\').submit();']); ?>
                                <?php echo e(__('Log Out')); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
                        </form>
                     <?php $__env->endSlot(); ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $attributes = $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $component = $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
            </div>

            <!-- Mobile Navigation -->
            <div class="-me-2 flex items-center sm:hidden space-x-1 mobile-nav-container">
                <!-- Time Counter for Mobile -->
                <div class="mobile-time-container">
                    <!-- Clock Icon -->
                    <div class="mobile-time-icon">
                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    
                    <!-- Time Display -->
                    <div class="mobile-time-text">
                        <div id="mobile-time"><?php echo e(date('H:i')); ?></div>
                    </div>
                </div>

                <!-- Notifications for Mobile -->
                <div x-data="{ open: false, notifications: [], unreadCount: 0 }" 
                     x-init="
                        loadNotifications();
                        setInterval(loadNotifications, 10000);
                     " 
                     class="relative">
                    <button @click="open = !open" 
                            class="mobile-notification-btn text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5V7a7 7 0 00-14 0v5l-5 5h5m10 0v1a3 3 0 01-6 0v-1m6 0H9" />
                        </svg>
                        <span x-show="unreadCount > 0" 
                              x-text="unreadCount" 
                              class="mobile-notification-badge">
                        </span>
                    </button>

                    <!-- Mobile Notifications Dropdown -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                        <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">الإشعارات</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="unreadCount + ' إشعار غير مقروء'"></p>
                        </div>
                        <div class="max-h-48 overflow-y-auto">
                            <template x-for="notification in notifications" :key="notification.id">
                                <div class="p-3 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-1" x-show="!notification.is_read"></div>
                                        </div>
                                        <div class="mr-2 flex-1">
                                            <p class="text-xs font-medium text-gray-900 dark:text-white" x-text="notification.title"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="notification.message"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="notifications.length === 0" class="p-3 text-center text-gray-500 dark:text-gray-400 text-xs">
                                لا توجد إشعارات
                            </div>
                        </div>
                        <div class="p-3 border-t border-gray-200 dark:border-gray-700">
                            <a href="<?php echo e(route('notifications.index')); ?>" 
                               class="block text-center text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                عرض جميع الإشعارات
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Hamburger Menu -->
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <!-- Mobile Time Display (Full Width) -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900 dark:to-indigo-900 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
            <div x-data="{ 
                currentTime: new Date(),
                updateTime() {
                    this.currentTime = new Date();
                }
            }" 
            x-init="
                updateTime();
                setInterval(updateTime, 1000);
            " 
            class="flex items-center justify-between">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="flex items-center justify-center w-10 h-10 bg-blue-500 rounded-full">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white font-mono" x-text="currentTime.toLocaleTimeString('ar-SA', { 
                            hour: '2-digit', 
                            minute: '2-digit',
                            second: '2-digit'
                        })"></div>
                        <div class="text-sm text-gray-600 dark:text-gray-300" x-text="currentTime.toLocaleDateString('ar-SA', { 
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })"></div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500 dark:text-gray-400">الوقت الحالي</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Current Time</div>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="pt-2 pb-3 space-y-1">
            <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['href' => route('dashboard'),'active' => request()->routeIs('dashboard')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('dashboard')),'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('dashboard'))]); ?>
                <div class="flex items-center">
                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z" />
                    </svg>
                <?php echo e(__('Dashboard')); ?>

                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-lg"><?php echo e(substr(Auth::user()->name, 0, 1)); ?></span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-base text-gray-800 dark:text-gray-200 truncate"><?php echo e(Auth::user()->name); ?></div>
                        <div class="font-medium text-sm text-gray-500 truncate"><?php echo e(Auth::user()->email); ?></div>
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['href' => route('profile.edit')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('profile.edit'))]); ?>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    <?php echo e(__('Profile')); ?>

                    </div>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>

                <!-- Authentication -->
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['href' => route('logout'),'onclick' => 'event.preventDefault();
                                        this.closest(\'form\').submit();']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('logout')),'onclick' => 'event.preventDefault();
                                        this.closest(\'form\').submit();']); ?>
                        <div class="flex items-center text-red-600 dark:text-red-400">
                            <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        <?php echo e(__('Log Out')); ?>

                        </div>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript for Notifications -->
    <script>
        function loadNotifications() {
            fetch('<?php echo e(route("notifications.index")); ?>', {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json; charset=utf-8'
                }
            })
                .then(response => response.json())
                .then(data => {
                    this.notifications = data.notifications;
                    this.unreadCount = data.unread_count;
                    this.lastCheckTime = new Date();
                })
                .catch(error => console.error('Error loading notifications:', error));
        }
        
        function checkForNewNotifications() {
            fetch('<?php echo e(route("notifications.index")); ?>', {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json; charset=utf-8'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.notifications && data.notifications.length > 0) {
                        // البحث عن إشعارات جديدة
                        const newNotifications = data.notifications.filter(notification => {
                            return notification.type === 'message' && 
                                   !notification.is_read && 
                                   (!this.lastCheckTime || new Date(notification.created_at) > this.lastCheckTime);
                        });
                        
                        // إظهار إشعار منبثق لكل إشعار جديد
                        newNotifications.forEach(notification => {
                            showNotificationPopup(notification);
                        });
                    }
                    
                    // تحديث البيانات المحلية
                    this.notifications = data.notifications;
                    this.unreadCount = data.unread_count;
                    this.lastCheckTime = new Date();
                })
                .catch(error => console.error('Error checking for new notifications:', error));
        }
        
        function showNotificationPopup(notification) {
            // تشغيل صوت التنبيه
            playNotificationSound();
            
            // إنشاء عنصر الإشعار المنبثق
            const popup = document.createElement('div');
            popup.className = 'fixed top-4 right-4 bg-white border border-gray-200 rounded-lg shadow-lg p-4 z-50 max-w-sm transform transition-all duration-300 translate-x-full animate-pulse';
            popup.innerHTML = `
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center animate-bounce">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-bold text-gray-900">${notification.title}</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 animate-pulse">
                                جديد
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">${notification.message}</p>
                        <div class="mt-3 flex space-x-2">
                            <button onclick="markAsRead(${notification.id}); this.parentElement.parentElement.parentElement.remove();" 
                                    class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition-colors">
                                تمييز كمقروء
                            </button>
                            <button onclick="this.parentElement.parentElement.parentElement.remove();" 
                                    class="text-xs bg-gray-300 text-gray-700 px-3 py-1 rounded hover:bg-gray-400 transition-colors">
                                إغلاق
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // إضافة الإشعار للصفحة
            document.body.appendChild(popup);
            
            // إظهار الإشعار مع تأثير
            setTimeout(() => {
                popup.classList.remove('translate-x-full');
                popup.classList.remove('animate-pulse');
            }, 100);
            
            // إزالة الإشعار تلقائياً بعد 15 ثانية
            setTimeout(() => {
                popup.classList.add('translate-x-full');
                setTimeout(() => {
                    if (popup.parentNode) {
                        popup.remove();
                    }
                }, 300);
            }, 15000);
        }
        
        function playNotificationSound() {
            // إنشاء صوت تنبيه بسيط
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
            oscillator.frequency.setValueAtTime(800, audioContext.currentTime + 0.2);
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.3);
        }

        function markAsRead(notificationId) {
            fetch(`<?php echo e(url('notifications')); ?>/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update local state
                    const notification = this.notifications.find(n => n.id === notificationId);
                    if (notification) {
                        notification.is_read = true;
                    }
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
        }

        function markAllAsRead() {
            fetch('<?php echo e(route("notifications.mark-all-read")); ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update local state
                    this.notifications.forEach(notification => {
                        notification.is_read = true;
                    });
                    this.unreadCount = 0;
                }
            })
            .catch(error => console.error('Error marking all notifications as read:', error));
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInMinutes = Math.floor((now - date) / (1000 * 60));
            
            if (diffInMinutes < 1) return 'الآن';
            if (diffInMinutes < 60) return `منذ ${diffInMinutes} دقيقة`;
            if (diffInMinutes < 1440) return `منذ ${Math.floor(diffInMinutes / 60)} ساعة`;
            return `منذ ${Math.floor(diffInMinutes / 1440)} يوم`;
        }

        // Mobile notifications functions
        function loadNotifications() {
            fetch('<?php echo e(route("notifications.index")); ?>', {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json; charset=utf-8'
                }
            })
                .then(response => response.json())
                .then(data => {
                    // Update mobile notifications
                    const mobileNotificationElements = document.querySelectorAll('[x-data*="notifications: [], unreadCount: 0"]');
                    mobileNotificationElements.forEach(element => {
                        const alpineData = Alpine.$data(element);
                        if (alpineData) {
                            alpineData.notifications = data.notifications;
                            alpineData.unreadCount = data.unread_count;
                        }
                    });
                })
                .catch(error => console.error('Error loading mobile notifications:', error));
        }

        // Simple time update for mobile
        function updateMobileTime() {
            const timeElement = document.getElementById('mobile-time');
            if (timeElement) {
                const now = new Date();
                const timeString = now.toLocaleTimeString('ar-SA', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
                timeElement.textContent = timeString;
            }
        }

        // Update time every second
        setInterval(updateMobileTime, 1000);
    </script>

    <style>
        /* Mobile Navigation Styles */
        @media (max-width: 640px) {
            .mobile-nav-container {
                display: flex !important;
                align-items: center;
                gap: 8px;
            }
            
            .mobile-time-container {
                background: #f3f4f6;
                border-radius: 8px;
                padding: 4px 8px;
                display: flex;
                align-items: center;
                gap: 4px;
                min-width: 80px;
            }
            
            .mobile-time-container.dark {
                background: #374151;
            }
            
            .mobile-time-icon {
                width: 24px;
                height: 24px;
                background: #3b82f6;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .mobile-time-text {
                font-size: 12px;
                font-weight: bold;
                color: #1f2937;
                font-family: 'Courier New', monospace;
            }
            
            .mobile-time-text.dark {
                color: #f9fafb;
            }
            
            .mobile-notification-btn {
                background: #f3f4f6;
                border-radius: 8px;
                padding: 4px;
                position: relative;
            }
            
            .mobile-notification-btn.dark {
                background: #374151;
            }
            
            .mobile-notification-badge {
                position: absolute;
                top: -4px;
                right: -4px;
                background: #ef4444;
                color: white;
                border-radius: 50%;
                width: 16px;
                height: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 10px;
                font-weight: bold;
            }
        }
    </style>
</nav>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\layouts\navigation.blade.php ENDPATH**/ ?>