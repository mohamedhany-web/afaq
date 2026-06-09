<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>
<form method="GET" class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-6">
    <div class="flex flex-col lg:flex-row gap-3 lg:items-end flex-wrap">
        <?php if(isset($teamMembers) && $teamMembers->isNotEmpty()): ?>
        <div class="w-full lg:w-48">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">الموظف</label>
            <select name="user_id" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">الكل</option>
                <?php $__currentLoopData = $teamMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($member->id); ?>" <?php if(request('user_id') == $member->id): echo 'selected'; endif; ?>><?php echo e($member->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <?php endif; ?>
        <div class="w-full lg:w-40">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">من تاريخ</label>
            <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
        </div>
        <div class="w-full lg:w-40">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">إلى تاريخ</label>
            <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
        </div>
        <?php if($showStatusFilter ?? false): ?>
        <div class="w-full lg:w-36">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">الحالة</label>
            <select name="status" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">الكل</option>
                <option value="draft" <?php if(request('status') === 'draft'): echo 'selected'; endif; ?>>مسودة</option>
                <option value="submitted" <?php if(request('status') === 'submitted'): echo 'selected'; endif; ?>>مرفوع</option>
            </select>
        </div>
        <?php endif; ?>
        <div class="flex gap-2">
            <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold shadow-sm font-tajawal"
                    style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">تطبيق</button>
            <?php if(request()->hasAny(['user_id', 'status', 'date_from', 'date_to'])): ?>
            <a href="<?php echo e(route('crm.daily-reports.index')); ?>" class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 font-tajawal">مسح</a>
            <?php endif; ?>
        </div>
    </div>
</form>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/daily-reports/partials/filters.blade.php ENDPATH**/ ?>