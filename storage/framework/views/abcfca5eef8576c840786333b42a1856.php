

<?php $__env->startSection('page-title', 'تذاكر الدعم الفني'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">تذاكر الدعم الفني</h1>
                <p class="text-gray-600">إدارة طلبات ومشاكل العملاء</p>
            </div>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-tickets')): ?>
            <a href="<?php echo e(route('tickets.create')); ?>" class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-all duration-200 flex items-center shadow-sm">
                <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                تذكرة جديدة
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Tickets -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي التذاكر</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e($stats['total']); ?></p>
                    <p class="text-xs text-blue-600 mt-1">جميع التذاكر</p>
                </div>
                <div class="p-4 bg-blue-50 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Open Tickets -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">تذاكر مفتوحة</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e($stats['open']); ?></p>
                    <p class="text-xs text-yellow-600 mt-1">يحتاج للمعالجة</p>
                </div>
                <div class="p-4 bg-yellow-50 rounded-xl">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Closed Tickets -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">تذاكر محلولة</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e($stats['closed']); ?></p>
                    <p class="text-xs text-green-600 mt-1">تم الإغلاق</p>
                </div>
                <div class="p-4 bg-green-50 rounded-xl">
                    <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- High Priority -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">أولوية عالية</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e($stats['high_priority']); ?></p>
                    <p class="text-xs text-red-600 mt-1">يحتاج لانتباه فوري</p>
                </div>
                <div class="p-4 bg-red-50 rounded-xl">
                    <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden w-full">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">قائمة التذاكر (<?php echo e($tickets->total()); ?>)</h3>
                <form method="GET" action="<?php echo e(route('tickets.index')); ?>" class="flex items-center gap-2">
                    <select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">جميع الحالات</option>
                        <option value="open" <?php echo e(request('status') == 'open' ? 'selected' : ''); ?>>مفتوح</option>
                        <option value="in_progress" <?php echo e(request('status') == 'in_progress' ? 'selected' : ''); ?>>قيد التنفيذ</option>
                        <option value="pending_client" <?php echo e(request('status') == 'pending_client' ? 'selected' : ''); ?>>في انتظار العميل</option>
                        <option value="resolved" <?php echo e(request('status') == 'resolved' ? 'selected' : ''); ?>>محلول</option>
                        <option value="closed" <?php echo e(request('status') == 'closed' ? 'selected' : ''); ?>>مغلق</option>
                    </select>
                    <select name="priority" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">جميع الأولويات</option>
                        <option value="low" <?php echo e(request('priority') == 'low' ? 'selected' : ''); ?>>منخفضة</option>
                        <option value="medium" <?php echo e(request('priority') == 'medium' ? 'selected' : ''); ?>>متوسطة</option>
                        <option value="high" <?php echo e(request('priority') == 'high' ? 'selected' : ''); ?>>عالية</option>
                        <option value="critical" <?php echo e(request('priority') == 'critical' ? 'selected' : ''); ?>>حرجة</option>
                    </select>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="البحث عن تذكرة..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">بحث</button>
                    <?php if(request('search') || request('status') || request('priority')): ?>
                    <a href="<?php echo e(route('tickets.index')); ?>" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">إلغاء</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم التذكرة</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الموضوع</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العميل</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الأولوية</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المكلف</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $statusColors = [
                            'open' => 'bg-red-100 text-red-800',
                            'in_progress' => 'bg-yellow-100 text-yellow-800',
                            'pending_client' => 'bg-orange-100 text-orange-800',
                            'resolved' => 'bg-blue-100 text-blue-800',
                            'closed' => 'bg-green-100 text-green-800',
                        ];
                        
                        $priorityColors = [
                            'low' => 'bg-green-100 text-green-800',
                            'medium' => 'bg-yellow-100 text-yellow-800',
                            'high' => 'bg-orange-100 text-orange-800',
                            'critical' => 'bg-red-100 text-red-800',
                        ];
                        
                        $priorityNames = [
                            'low' => 'منخفضة',
                            'medium' => 'متوسطة',
                            'high' => 'عالية',
                            'critical' => 'حرجة',
                        ];
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900"><?php echo e($ticket->ticket_number); ?></div>
                            <div class="text-xs text-gray-500"><?php echo e($ticket->created_at->diffForHumans()); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900"><?php echo e(Str::limit($ticket->subject, 40)); ?></div>
                            <div class="text-sm text-gray-500"><?php echo e($ticket->category_name); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($ticket->client): ?>
                            <div class="flex items-center">
                                <div class="h-8 w-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center ml-2">
                                    <span class="text-xs font-medium text-white"><?php echo e(mb_substr($ticket->client->name, 0, 1)); ?></span>
                                </div>
                                <div class="text-sm text-gray-900"><?php echo e($ticket->client->name); ?></div>
                            </div>
                            <?php else: ?>
                            <span class="text-sm text-gray-500">غير محدد</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($priorityColors[$ticket->priority] ?? 'bg-gray-100 text-gray-800'); ?>">
                                <?php echo e($priorityNames[$ticket->priority] ?? $ticket->priority); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-800'); ?>">
                                <?php echo e($ticket->status_name); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($ticket->assignedTo): ?>
                            <div class="flex items-center">
                                <div class="h-8 w-8 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center ml-2">
                                    <span class="text-xs font-medium text-white"><?php echo e(mb_substr($ticket->assignedTo->name, 0, 1)); ?></span>
                                </div>
                                <div class="text-sm text-gray-900"><?php echo e($ticket->assignedTo->name); ?></div>
                            </div>
                            <?php else: ?>
                            <span class="text-sm text-gray-500">غير مكلف</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="<?php echo e(route('tickets.show', $ticket)); ?>" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                                    <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    عرض
                                </a>
                                <a href="<?php echo e(route('tickets.edit', $ticket)); ?>" class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors duration-200">
                                    <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    تعديل
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                                <p class="text-lg font-medium">لا توجد تذاكر</p>
                                <p class="text-sm">قم بإنشاء تذكرة جديدة للبدء</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($tickets->hasPages()): ?>
        <div class="px-6 py-4 border-t border-gray-200">
            <?php echo e($tickets->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\tickets\index.blade.php ENDPATH**/ ?>