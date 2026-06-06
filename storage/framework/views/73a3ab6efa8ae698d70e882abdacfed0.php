<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $task = $task ?? null;
    $priorityColors = config('crm_tasks.priority_colors', []);
    $selectedPriority = old('priority', $task?->priority ?? 'medium');
?>


<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
        <h2 class="font-bold text-lg text-gray-900 font-tajawal">تفاصيل المهمة</h2>
        <p class="text-xs text-gray-500 mt-1">عنوان واضح ووصف يحدد المخرجات المتوقعة</p>
    </div>
    <div class="p-5 sm:p-6 space-y-4">
        <div>
            <label class="<?php echo e($label); ?>">عنوان المهمة *</label>
            <input type="text" name="title" value="<?php echo e(old('title', $task?->title)); ?>" class="<?php echo e($input); ?>" required maxlength="255"
                   placeholder="مثال: متابعة 20 عميلاً جديداً — اتصال أولي">
            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="<?php echo e($label); ?>">الوصف والمخرجات المتوقعة</label>
            <textarea name="description" rows="4" class="<?php echo e($input); ?> resize-none"
                      placeholder="ما الذي يجب إنجازه؟ كيف يُقاس النجاح؟"><?php echo e(old('description', $task?->description)); ?></textarea>
            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>
</div>


<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
        <h2 class="font-bold text-lg text-gray-900 font-tajawal">التعيين والجدولة</h2>
    </div>
    <div class="p-5 sm:p-6 space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="<?php echo e($label); ?>">تعيين إلى *</label>
                <select name="assigned_to" class="<?php echo e($input); ?>" required>
                    <?php $__currentLoopData = $assignableUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($u->id); ?>" <?php if(old('assigned_to', $task?->assigned_to) == $u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['assigned_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="sm:col-span-2">
                <label class="<?php echo e($label); ?>">الأولوية *</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-1">
                    <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $pColor = $priorityColors[$key] ?? $themeColor; ?>
                    <label class="relative cursor-pointer rounded-xl border-2 p-3 text-center transition-all"
                           :class="priority === '<?php echo e($key); ?>' ? 'shadow-md' : 'border-gray-200 hover:border-gray-300'"
                           :style="priority === '<?php echo e($key); ?>' ? 'border-color:<?php echo e($pColor); ?>; background:<?php echo e($pColor); ?>10' : ''">
                        <input type="radio" name="priority" value="<?php echo e($key); ?>" class="sr-only" x-model="priority">
                        <span class="block w-3 h-3 rounded-full mx-auto mb-1.5" style="background:<?php echo e($pColor); ?>"></span>
                        <span class="text-xs font-bold text-gray-800"><?php echo e($text); ?></span>
                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="<?php echo e($label); ?>">التصنيف *</label>
                <select name="category" class="<?php echo e($input); ?>" required>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if(old('category', $task?->category ?? 'follow_ups') === $key): echo 'selected'; endif; ?>><?php echo e($text); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="<?php echo e($label); ?>">موعد الاستحقاق *</label>
                <input type="datetime-local" name="due_at"
                       value="<?php echo e(old('due_at', $task?->due_at?->format('Y-m-d\TH:i') ?? now()->addDay()->setHour(17)->setMinute(0)->format('Y-m-d\TH:i'))); ?>"
                       class="<?php echo e($input); ?>" required>
                <?php $__errorArgs = ['due_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <?php if(!$task): ?>
            <div class="sm:col-span-2 flex items-start gap-3 p-4 rounded-xl bg-gray-50 border border-gray-100">
                <input type="checkbox" name="requires_acceptance" value="1" id="requires_acceptance"
                       class="mt-1 rounded border-gray-300" <?php if(old('requires_acceptance')): echo 'checked'; endif; ?>>
                <label for="requires_acceptance" class="text-sm text-gray-700 cursor-pointer">
                    <span class="font-bold block">يتطلب قبول الموظف</span>
                    <span class="text-xs text-gray-500">لن تبدأ المهمة حتى يضغط المكلف «قبول»</span>
                </label>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
        <h2 class="font-bold text-lg text-gray-900 font-tajawal">الربط بالنشاط التجاري</h2>
        <p class="text-xs text-gray-500 mt-1">اختياري — يسهّل الوصول للعميل والصفقة من صفحة المهمة</p>
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="md:col-span-2 lg:col-span-1">
            <label class="<?php echo e($label); ?>">العميل</label>
            <?php echo $__env->make('partials.client-search-select', [
                'name' => 'client_id',
                'value' => old('client_id', $task?->client_id),
                'crmScope' => true,
                'inputClass' => $input,
                'placeholder' => 'ابحث عن عميل...',
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php $__errorArgs = ['client_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="<?php echo e($label); ?>">صفقة في المسار</label>
            <select name="sale_id" class="<?php echo e($input); ?>">
                <option value="">— بدون —</option>
                <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s->id); ?>" <?php if(old('sale_id', $task?->sale_id) == $s->id): echo 'selected'; endif; ?>>
                        <?php echo e($s->client?->name ?? 'صفقة #' . $s->id); ?> — <?php echo e($s->stage); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="<?php echo e($label); ?>">مشروع عقاري</label>
            <select name="project_id" class="<?php echo e($input); ?>">
                <option value="">— بدون —</option>
                <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($p->id); ?>" <?php if(old('project_id', $task?->project_id) == $p->id): echo 'selected'; endif; ?>><?php echo e($p->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/tasks/partials/form.blade.php ENDPATH**/ ?>