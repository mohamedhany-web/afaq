

<?php $__env->startSection('page-title', 'إنشاء تقرير قسم'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-6xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">إنشاء تقرير قسم</h1>
            <p class="text-gray-600">ملخص دوري + مؤشرات + مرفقات لإرساله للإدارة</p>
        </div>
        <a href="<?php echo e(route('department-manager.reports.index')); ?>" class="bg-gray-600 text-white px-6 py-3 rounded-xl hover:bg-gray-700 transition-all duration-200 shadow-sm">
            رجوع
        </a>
    </div>

    <?php if(session('error')): ?>
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('department-manager.reports.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php echo csrf_field(); ?>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">المشروع (اختياري)</label>
                    <select name="project_id" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl">
                        <option value="">—</option>
                        <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($project->id); ?>" <?php if(old('project_id') == $project->id): echo 'selected'; endif; ?>><?php echo e($project->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">من</label>
                        <input type="date" name="period_start" value="<?php echo e(old('period_start')); ?>" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">إلى</label>
                        <input type="date" name="period_end" value="<?php echo e(old('period_end')); ?>" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">ملخص التقرير <span class="text-red-500">*</span></label>
                    <textarea name="summary" rows="8" required
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['summary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                              placeholder="اكتب ما تم إنجازه، العوائق، الخطة القادمة..."><?php echo e(old('summary')); ?></textarea>
                    <?php $__errorArgs = ['summary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">KPIs (JSON اختياري)</label>
                    <textarea name="kpis" rows="4"
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl"
                              placeholder='مثال: {"tasks_completed":12,"sla_met_pct":98}'><?php echo e(old('kpis')); ?></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">مرفقات</label>
                    <input type="file" name="attachments[]" multiple class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50">
                    <?php $__errorArgs = ['attachments.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <button type="submit" name="submit" value="0" class="bg-gray-800 text-white px-6 py-3 rounded-xl hover:bg-gray-900 transition-all duration-200 shadow-sm">
                حفظ كمسودة
            </button>
            <button type="submit" name="submit" value="1" class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-all duration-200 shadow-sm">
                إرسال للإدارة
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\department-reports\manager\create.blade.php ENDPATH**/ ?>