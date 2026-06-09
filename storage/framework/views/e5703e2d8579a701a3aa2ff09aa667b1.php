<?php $__env->startSection('page-title', 'تفاصيل البرنامج التدريبي'); ?>

<?php $__env->startSection('content'); ?>
<!-- Header Section -->
<div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-6 text-white mb-6 shadow-lg">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <div class="h-16 w-16 bg-white/20 rounded-full flex items-center justify-center ml-4">
                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold mb-2"><?php echo e($training->title); ?></h1>
                <p class="text-blue-100"><?php echo e($training->description ? Str::limit($training->description, 60) : 'لا يوجد وصف'); ?></p>
            </div>
        </div>
        <div class="flex flex-wrap items-center justify-end gap-2 sm:gap-3">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-training')): ?>
            <a href="<?php echo e(route('training.applications', $training)); ?>" class="inline-flex items-center gap-2 bg-amber-400 text-amber-950 px-4 py-2 rounded-lg hover:bg-amber-300 transition-all duration-200 font-bold text-sm shadow-sm">
                طلبات التسجيل (الموقع)
                <?php if(($internshipApplicationsCount ?? 0) > 0): ?>
                    <span class="min-w-[1.5rem] text-center rounded-full bg-amber-950 text-amber-100 text-xs px-2 py-0.5"><?php echo e($internshipApplicationsCount); ?></span>
                    <?php if(($pendingInternshipApplicationsCount ?? 0) > 0): ?>
                        <span class="text-xs font-normal opacity-90">(<?php echo e($pendingInternshipApplicationsCount); ?> قيد المراجعة)</span>
                    <?php endif; ?>
                <?php endif; ?>
            </a>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-training')): ?>
            <a href="<?php echo e(route('training.edit', $training)); ?>" class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-lg hover:bg-white/30 transition-all duration-200">
                تعديل
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('training.index')); ?>" class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition-all duration-200">
                العودة
            </a>
        </div>
    </div>
</div>

<div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Training Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">تفاصيل البرنامج</h2>
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">الوصف</h3>
                        <p class="text-sm text-gray-900 whitespace-pre-wrap"><?php echo e($training->description ?? 'لا يوجد وصف'); ?></p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">النوع</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                <?php if($training->type == 'internal'): ?> داخلي
                                <?php elseif($training->type == 'external'): ?> خارجي
                                <?php elseif($training->type == 'online'): ?> أونلاين
                                <?php elseif($training->type == 'workshop'): ?> ورشة عمل
                                <?php elseif($training->type == 'seminar'): ?> ندوة
                                <?php else: ?> <?php echo e($training->type); ?>

                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">الحالة</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                <?php if($training->status == 'completed'): ?> bg-green-100 text-green-800
                                <?php elseif($training->status == 'ongoing'): ?> bg-blue-100 text-blue-800
                                <?php elseif($training->status == 'cancelled'): ?> bg-red-100 text-red-800
                                <?php else: ?> bg-yellow-100 text-yellow-800 <?php endif; ?>">
                                <?php if($training->status == 'planned'): ?> مخطط
                                <?php elseif($training->status == 'ongoing'): ?> قيد التنفيذ
                                <?php elseif($training->status == 'completed'): ?> مكتمل
                                <?php elseif($training->status == 'cancelled'): ?> ملغي
                                <?php else: ?> <?php echo e($training->status); ?>

                                <?php endif; ?>
                            </span>
                        </div>

                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">تاريخ البدء</span>
                            <span class="text-sm text-gray-900"><?php echo e($training->start_date->format('Y-m-d')); ?></span>
                        </div>

                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">تاريخ الانتهاء</span>
                            <span class="text-sm text-gray-900"><?php echo e($training->end_date->format('Y-m-d')); ?></span>
                        </div>

                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">الحد الأقصى للمشاركين</span>
                            <span class="text-sm text-gray-900"><?php echo e($training->max_participants); ?></span>
                        </div>

                        <?php if($training->cost > 0): ?>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">التكلفة</span>
                            <span class="text-sm text-gray-900"><?php echo e(number_format($training->cost, 2)); ?> ج.م</span>
                        </div>
                        <?php endif; ?>

                        <?php if($training->instructor): ?>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">المدرب</span>
                            <span class="text-sm text-gray-900"><?php echo e($training->instructor->name); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Participants Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">المشاركون</h2>
                    <span class="text-sm text-gray-500"><?php echo e($training->participants->count()); ?> / <?php echo e($training->max_participants); ?></span>
                </div>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-training')): ?>
                <!-- Add Participant Form -->
                <form action="<?php echo e(route('training.add-participant', $training)); ?>" method="POST" class="mb-6 pb-6 border-b border-gray-200">
                    <?php echo csrf_field(); ?>
                    <div class="flex gap-3">
                        <select name="user_id" id="user_id" required
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">اختر موظف للانضمام</option>
                            <?php $__currentLoopData = $availableEmployees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($employee->id); ?>"><?php echo e($employee->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            إضافة
                        </button>
                    </div>
                </form>
                <?php endif; ?>

                <!-- Participants List -->
                <?php if($training->participants->count() > 0): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $training->participants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $participant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-medium">
                                    <?php echo e(substr($participant->user->name, 0, 1)); ?>

                                </div>
                                <div>
                                    <p class="font-medium text-gray-900"><?php echo e($participant->user->name); ?></p>
                                    <?php if($participant->user->employee): ?>
                                    <p class="text-xs text-gray-500"><?php echo e($participant->user->employee->position); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-training')): ?>
                            <form action="<?php echo e(route('training.remove-participant', [$training, $participant])); ?>" method="POST" class="inline">
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
                        <p>لا يوجد مشاركون في هذا البرنامج</p>
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
                        <span class="text-sm font-medium text-gray-900"><?php echo e($training->start_date->diffInDays($training->end_date) + 1); ?> يوم</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">المشاركون المسجلون</span>
                        <span class="text-sm font-medium text-gray-900"><?php echo e($training->participants->count()); ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">المقاعد المتاحة</span>
                        <span class="text-sm font-medium text-gray-900"><?php echo e($training->max_participants - $training->participants->count()); ?></span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete-training')): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">إجراءات</h3>
                <form action="<?php echo e(route('training.destroy', $training)); ?>" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا البرنامج التدريبي؟');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        حذف البرنامج
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\training\show.blade.php ENDPATH**/ ?>