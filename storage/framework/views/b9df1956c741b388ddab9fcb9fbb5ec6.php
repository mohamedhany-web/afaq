

<?php $__env->startSection('page-title', 'تفاصيل الاجتماع'); ?>

<?php $__env->startSection('content'); ?>
<!-- Header Section -->
<div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl p-6 text-white mb-6 shadow-lg">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <div class="h-16 w-16 bg-white/20 rounded-full flex items-center justify-center ml-4">
                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold mb-2"><?php echo e($meeting->title); ?></h1>
                <p class="text-green-100"><?php echo e($meeting->description ? Str::limit($meeting->description, 60) : 'لا يوجد وصف'); ?></p>
            </div>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-meetings')): ?>
            <a href="<?php echo e(route('meetings.edit', $meeting)); ?>" class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-lg hover:bg-white/30 transition-all duration-200">
                تعديل
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('meetings.index')); ?>" class="bg-white text-green-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition-all duration-200">
                العودة
            </a>
        </div>
    </div>
</div>

<div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Meeting Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">تفاصيل الاجتماع</h2>
                <div class="space-y-4">
                    <?php if($meeting->description): ?>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">الوصف</h3>
                        <p class="text-sm text-gray-900 whitespace-pre-wrap"><?php echo e($meeting->description); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">النوع</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                <?php if($meeting->type == 'internal'): ?> داخلي
                                <?php elseif($meeting->type == 'external'): ?> خارجي
                                <?php elseif($meeting->type == 'online'): ?> أونلاين
                                <?php elseif($meeting->type == 'in-person'): ?> حضور
                                <?php elseif($meeting->type == 'hybrid'): ?> هجين
                                <?php else: ?> <?php echo e($meeting->type); ?>

                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">الحالة</span>
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
                        </div>

                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">وقت البدء</span>
                            <span class="text-sm text-gray-900"><?php echo e($meeting->start_time->format('Y-m-d H:i')); ?></span>
                        </div>

                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">وقت الانتهاء</span>
                            <span class="text-sm text-gray-900"><?php echo e($meeting->end_time->format('Y-m-d H:i')); ?></span>
                        </div>

                        <?php if($meeting->location): ?>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">الموقع</span>
                            <span class="text-sm text-gray-900"><?php echo e($meeting->location); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if($meeting->meeting_link): ?>
                        <div class="md:col-span-2 flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">رابط الاجتماع</span>
                            <a href="<?php echo e($meeting->meeting_link); ?>" target="_blank" class="text-sm text-blue-600 hover:text-blue-800">
                                <?php echo e($meeting->meeting_link); ?>

                            </a>
                        </div>
                        <?php endif; ?>

                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">المنظم</span>
                            <span class="text-sm text-gray-900"><?php echo e($meeting->organizer->name ?? 'غير محدد'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Participants Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">المشاركون</h2>
                    <span class="text-sm text-gray-500"><?php echo e($meeting->participants->count()); ?> مشارك</span>
                </div>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-meetings')): ?>
                <!-- Add Participant Form -->
                <form action="<?php echo e(route('meetings.add-participant', $meeting)); ?>" method="POST" class="mb-6 pb-6 border-b border-gray-200">
                    <?php echo csrf_field(); ?>
                    <div class="flex gap-3">
                        <select name="user_id" id="user_id" required
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">اختر موظف للانضمام</option>
                            <?php $__currentLoopData = $availableEmployees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($employee->id); ?>"><?php echo e($employee->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            إضافة
                        </button>
                    </div>
                </form>
                <?php endif; ?>

                <!-- Participants List -->
                <?php if($meeting->participants->count() > 0): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $meeting->participants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $participant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-medium">
                                    <?php echo e(substr($participant->user->name, 0, 1)); ?>

                                </div>
                                <div>
                                    <p class="font-medium text-gray-900"><?php echo e($participant->user->name); ?></p>
                                    <?php if($participant->user->employee): ?>
                                    <p class="text-xs text-gray-500"><?php echo e($participant->user->employee->position); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-meetings')): ?>
                            <form action="<?php echo e(route('meetings.remove-participant', [$meeting, $participant])); ?>" method="POST" class="inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" onclick="return confirm('هل أنت متأكد من إزالة هذا المشارك؟');" 
                                        class="text-red-600 hover:text-red-800 text-sm">
                                    إزالة
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <p>لا يوجد مشاركون في هذا الاجتماع</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات سريعة</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">المدة</span>
                        <span class="text-sm font-medium text-gray-900"><?php echo e($meeting->start_time->diffForHumans($meeting->end_time, true)); ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">المشاركون</span>
                        <span class="text-sm font-medium text-gray-900"><?php echo e($meeting->participants->count()); ?></span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete-meetings')): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">إجراءات</h3>
                <form action="<?php echo e(route('meetings.destroy', $meeting)); ?>" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الاجتماع؟');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        حذف الاجتماع
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\meetings\show.blade.php ENDPATH**/ ?>