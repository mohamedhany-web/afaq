<?php
    $isEdit = isset($project);
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $sectionHeader = 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        بيانات المشروع العقاري
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="sm:col-span-2 lg:col-span-3">
            <label class="<?php echo e($label); ?>">اسم المشروع / الكمبوند *</label>
            <input name="name" value="<?php echo e(old('name', $project->name ?? '')); ?>" required class="<?php echo e($input); ?>" placeholder="مثال: كمبوند سيتي فيو">
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="sm:col-span-2 lg:col-span-3">
            <label class="<?php echo e($label); ?>">الوصف</label>
            <textarea name="description" rows="3" class="<?php echo e($input); ?>" placeholder="وصف المشروع، المرافق، المميزات..."><?php echo e(old('description', $project->description ?? '')); ?></textarea>
        </div>
        <div>
            <label class="<?php echo e($label); ?>">المدينة</label>
            <input name="city" value="<?php echo e(old('city', $project->city ?? '')); ?>" class="<?php echo e($input); ?>" placeholder="القاهرة الجديدة">
        </div>
        <div>
            <label class="<?php echo e($label); ?>">المنطقة / الموقع</label>
            <input name="location" value="<?php echo e(old('location', $project->location ?? '')); ?>" class="<?php echo e($input); ?>" placeholder="التجمع الخامس">
        </div>
        <div>
            <label class="<?php echo e($label); ?>">مساحة الأرض (م²)</label>
            <input type="number" name="land_area_m2" min="0" step="0.01" value="<?php echo e(old('land_area_m2', $project->land_area_m2 ?? '')); ?>" class="<?php echo e($input); ?>" placeholder="31000">
        </div>
        <div class="sm:col-span-2">
            <label class="<?php echo e($label); ?>">تصنيف المشروع * <span class="font-normal text-gray-400">(يمكن اختيار أكثر من تصنيف)</span></label>
            <?php
                $selectedPropertyTypes = old(
                    'property_types',
                    isset($project) ? $project->resolvedPropertyTypes() : ['residential']
                );
                if (! is_array($selectedPropertyTypes)) {
                    $selectedPropertyTypes = \App\Models\Project::normalizePropertyTypes($selectedPropertyTypes);
                }
            ?>
            <div class="mt-2 flex flex-wrap gap-2">
                <?php $__currentLoopData = \App\Models\Project::CLASSIFICATION_TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border-2 cursor-pointer text-sm font-tajawal transition
                        <?php echo e(in_array($val, $selectedPropertyTypes, true) ? 'border-current bg-opacity-10' : 'border-gray-200 bg-gray-50'); ?>"
                        style="<?php echo e(in_array($val, $selectedPropertyTypes, true) ? 'border-color: ' . ($themeColor ?? '#4f46e5') . '; background: ' . ($themeColor ?? '#4f46e5') . '12; color: ' . ($themeColor ?? '#4f46e5') : ''); ?>">
                        <input type="checkbox" name="property_types[]" value="<?php echo e($val); ?>" class="rounded border-gray-300"
                               <?php if(in_array($val, $selectedPropertyTypes, true)): echo 'checked'; endif; ?>>
                        <span><?php echo e($txt); ?></span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php $__errorArgs = ['property_types'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <?php $__errorArgs = ['property_types.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="<?php echo e($label); ?>">نوع التطوير</label>
            <select name="project_type" class="<?php echo e($input); ?>">
                <option value="">— اختر —</option>
                <?php $__currentLoopData = \App\Models\Project::DEVELOPMENT_TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>" <?php if(old('project_type', $project->project_type ?? '') === $val): echo 'selected'; endif; ?>><?php echo e($txt); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="<?php echo e($label); ?>">حالة العرض *</label>
            <select name="listing_status" required class="<?php echo e($input); ?>">
                <?php $__currentLoopData = \App\Models\Project::LISTING_STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>" <?php if(old('listing_status', $project->listing_status ?? 'active') === $val): echo 'selected'; endif; ?>><?php echo e($txt); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['listing_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>
</div>

<?php echo $__env->make('projects.partials.ownership-fields', [
    'project' => $project ?? null,
    'themeColor' => $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor(),
    'input' => $input,
    'label' => $label,
    'sectionHeader' => $sectionHeader,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        إحصائيات الوحدات
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div>
            <label class="<?php echo e($label); ?>">إجمالي الوحدات</label>
            <input type="number" name="total_units" min="0" value="<?php echo e(old('total_units', $project->total_units ?? 0)); ?>" class="<?php echo e($input); ?>">
        </div>
        <div>
            <label class="<?php echo e($label); ?>">وحدات مباعة</label>
            <input type="number" name="sold_units" min="0" value="<?php echo e(old('sold_units', $project->sold_units ?? 0)); ?>" class="<?php echo e($input); ?>">
        </div>
        <div>
            <label class="<?php echo e($label); ?>">وحدات متاحة</label>
            <input type="number" name="available_units" min="0" value="<?php echo e(old('available_units', $project->available_units ?? '')); ?>" class="<?php echo e($input); ?>" placeholder="يُحسب تلقائياً إن تُرك فارغاً">
        </div>
        <div>
            <label class="<?php echo e($label); ?>">نسبة البيع</label>
            <div class="px-4 py-3 rounded-xl bg-gray-50 border-2 border-gray-200 text-sm text-gray-600 font-tajawal">
                تُحدَّث تلقائياً من الوحدات
            </div>
        </div>
    </div>
</div>

<?php echo $__env->make('projects.partials.classification-pricing', compact('project', 'themeColor', 'input', 'label'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        فريق المبيعات والإدارة
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
        <div>
            <label class="<?php echo e($label); ?>">مدير المشروع / المبيعات</label>
            <select name="project_manager_id" class="<?php echo e($input); ?>">
                <option value="">— المستخدم الحالي —</option>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($user->id); ?>" <?php if(old('project_manager_id', $project->project_manager_id ?? auth()->id()) == $user->id): echo 'selected'; endif; ?>><?php echo e($user->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <?php echo $__env->make('partials.client-search-select', [
                'required' => false,
                'value' => old('client_id', $project->client_id ?? ''),
                'inputClass' => $input,
                'crmScope' => false,
                'placeholder' => 'ابحث عن شريك / مطور (اختياري)...',
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div class="sm:col-span-2">
            <label class="<?php echo e($label); ?>">فريق المبيعات</label>
            <select name="team_members[]" multiple class="<?php echo e($input); ?> min-h-[120px]">
                <?php $selectedTeam = old('team_members', isset($project) ? $project->teamMembers->pluck('id')->all() : []); ?>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($user->id); ?>" <?php if(in_array($user->id, $selectedTeam)): echo 'selected'; endif; ?>><?php echo e($user->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <p class="text-xs text-gray-400 mt-1 font-tajawal">اضغط Ctrl لاختيار أكثر من موظف</p>
        </div>
        <div>
            <label class="<?php echo e($label); ?>">تاريخ الإطلاق</label>
            <input type="date" name="start_date" value="<?php echo e(old('start_date', isset($project) && $project->start_date ? $project->start_date->format('Y-m-d') : date('Y-m-d'))); ?>" class="<?php echo e($input); ?>">
        </div>
        <div>
            <label class="<?php echo e($label); ?>">تاريخ التسليم المتوقع</label>
            <input type="date" name="end_date" value="<?php echo e(old('end_date', isset($project) && $project->end_date ? $project->end_date->format('Y-m-d') : '')); ?>" class="<?php echo e($input); ?>">
        </div>
    </div>
</div>

<?php echo $__env->make('projects.partials.map-picker', ['project' => $project ?? null, 'themeColor' => $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor()], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\projects\partials\form.blade.php ENDPATH**/ ?>