

<?php $__env->startSection('page-title', 'إدارة الاجتماعات والمؤتمرات'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">إدارة الاجتماعات والمؤتمرات</h1>
                <p class="text-gray-600">إدارة الاجتماعات والمؤتمرات والمواعيد</p>
            </div>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-meetings')): ?>
            <a href="<?php echo e(route('meetings.create')); ?>" class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition-all duration-200 flex items-center shadow-sm">
                <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                اجتماع جديد
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="<?php echo e(route('meetings.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                <input type="text" name="search" id="search" value="<?php echo e(request('search')); ?>" 
                       placeholder="البحث في العناوين والأوصاف..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">جميع الحالات</option>
                    <option value="scheduled" <?php echo e(request('status') == 'scheduled' ? 'selected' : ''); ?>>مجدول</option>
                    <option value="ongoing" <?php echo e(request('status') == 'ongoing' ? 'selected' : ''); ?>>قيد التنفيذ</option>
                    <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>مكتمل</option>
                    <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>ملغي</option>
                </select>
            </div>
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">النوع</label>
                <select name="type" id="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">جميع الأنواع</option>
                    <option value="internal" <?php echo e(request('type') == 'internal' ? 'selected' : ''); ?>>داخلي</option>
                    <option value="external" <?php echo e(request('type') == 'external' ? 'selected' : ''); ?>>خارجي</option>
                    <option value="online" <?php echo e(request('type') == 'online' ? 'selected' : ''); ?>>أونلاين</option>
                    <option value="in-person" <?php echo e(request('type') == 'in-person' ? 'selected' : ''); ?>>حضوري</option>
                    <option value="hybrid" <?php echo e(request('type') == 'hybrid' ? 'selected' : ''); ?>>هجين</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    تصفية
                </button>
            </div>
        </form>
    </div>

    <!-- Meetings List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <?php if($meetings->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المنظم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وقت البدء</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الموقع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المشاركون</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $meetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meeting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo e($meeting->title); ?></div>
                                <?php if($meeting->description): ?>
                                <div class="text-sm text-gray-500 mt-1"><?php echo e(Str::limit($meeting->description, 50)); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    <?php if($meeting->type == 'internal'): ?> داخلي
                                    <?php elseif($meeting->type == 'external'): ?> خارجي
                                    <?php elseif($meeting->type == 'online'): ?> أونلاين
                                    <?php elseif($meeting->type == 'in-person'): ?> حضور
                                    <?php elseif($meeting->type == 'hybrid'): ?> هجين
                                    <?php else: ?> <?php echo e($meeting->type); ?>

                                    <?php endif; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($meeting->organizer->name ?? 'غير محدد'); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($meeting->start_time->format('Y-m-d H:i')); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($meeting->location ?? ($meeting->meeting_link ? 'أونلاين' : 'غير محدد')); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($meeting->participants->count()); ?> مشارك
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    <?php if($meeting->status == 'completed'): ?> bg-green-100 text-green-800
                                    <?php elseif($meeting->status == 'ongoing'): ?> bg-blue-100 text-blue-800
                                    <?php elseif($meeting->status == 'cancelled'): ?> bg-red-100 text-red-800
                                    <?php else: ?> bg-yellow-100 text-yellow-800 <?php endif; ?>">
                                    <?php if($meeting->status == 'scheduled'): ?> مجدول
                                    <?php elseif($meeting->status == 'ongoing'): ?> قيد التنفيذ
                                    <?php elseif($meeting->status == 'completed'): ?> مكتمل
                                    <?php elseif($meeting->status == 'cancelled'): ?> ملغي
                                    <?php else: ?> <?php echo e($meeting->status); ?>

                                    <?php endif; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <a href="<?php echo e(route('meetings.show', $meeting)); ?>" class="text-blue-600 hover:text-blue-900">عرض</a>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-meetings')): ?>
                                    <a href="<?php echo e(route('meetings.edit', $meeting)); ?>" class="text-indigo-600 hover:text-indigo-900">تعديل</a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete-meetings')): ?>
                                    <form action="<?php echo e(route('meetings.destroy', $meeting)); ?>" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الاجتماع؟');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-900">حذف</button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <?php echo e($meetings->links()); ?>

            </div>
        <?php else: ?>
            <div class="p-12 text-center">
                <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">لا توجد اجتماعات</h3>
                <p class="text-gray-600 mb-6">ابدأ بإنشاء أول اجتماع</p>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-meetings')): ?>
                <a href="<?php echo e(route('meetings.create')); ?>" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors inline-flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    اجتماع جديد
                </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\meetings\index.blade.php ENDPATH**/ ?>