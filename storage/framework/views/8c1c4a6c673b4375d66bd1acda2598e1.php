<?php
    $weekdays = config('employee_schedule.weekdays', []);
    $selectedOff = old('weekly_off_days', $employee->weekly_off_days ?? config('employee_schedule.default_weekly_off_days', [5, 6]));
    if (!is_array($selectedOff)) {
        $selectedOff = [];
    }
    $workStart = old('work_start_time', isset($employee->work_start_time)
        ? \Carbon\Carbon::parse($employee->work_start_time)->format('H:i')
        : config('employee_schedule.default_work_start', '09:00'));
    $workEnd = old('work_end_time', isset($employee->work_end_time)
        ? \Carbon\Carbon::parse($employee->work_end_time)->format('H:i')
        : config('employee_schedule.default_work_end', '17:00'));
    $grace = old('late_grace_minutes', $employee->late_grace_minutes ?? config('employee_schedule.default_late_grace_minutes', 15));
?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        جدول الدوام والإجازات الأسبوعية
    </div>
    <div class="p-5 sm:p-6 space-y-5">
        <p class="text-xs text-gray-500 font-tajawal leading-relaxed">
            يُستخدم لحساب التأخير والانصراف المبكر، وتقارير الحضور، والتزام الموظف اليومي.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div>
                <label class="<?php echo e($label); ?>">بداية الدوام *</label>
                <input type="time" name="work_start_time" id="work_start_time" value="<?php echo e($workStart); ?>" required
                       class="<?php echo e($input); ?>" onchange="syncDailyHoursFromSchedule()">
                <?php $__errorArgs = ['work_start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="<?php echo e($label); ?>">نهاية الدوام *</label>
                <input type="time" name="work_end_time" id="work_end_time" value="<?php echo e($workEnd); ?>" required
                       class="<?php echo e($input); ?>" onchange="syncDailyHoursFromSchedule()">
                <?php $__errorArgs = ['work_end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="<?php echo e($label); ?>">سماح التأخير (دقيقة)</label>
                <input type="number" name="late_grace_minutes" value="<?php echo e($grace); ?>" min="0" max="60" class="<?php echo e($input); ?>">
                <p class="text-xs text-gray-400 mt-1 font-tajawal">بعدها يُسجّل تأخير</p>
                <?php $__errorArgs = ['late_grace_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="<?php echo e($label); ?>">ساعات العمل اليومية *</label>
                <input type="number" name="daily_hours" id="daily_hours" value="<?php echo e(old('daily_hours', $employee->daily_hours ?? 8)); ?>"
                       required min="1" max="12" step="0.5" class="<?php echo e($input); ?>">
                <?php $__errorArgs = ['daily_hours'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div>
            <label class="<?php echo e($label); ?> mb-2">أيام الإجازة الأسبوعية</label>
            <div class="flex flex-wrap gap-2">
                <?php $__currentLoopData = $weekdays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayNum => $dayName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border-2 cursor-pointer text-sm font-tajawal transition-colors
                    <?php echo e(in_array($dayNum, $selectedOff) ? 'border-current' : 'border-gray-200'); ?>"
                    style="<?php echo e(in_array($dayNum, $selectedOff) ? 'border-color:'.$themeColor.'; background:'.$themeColor.'12; color:'.$themeColor : ''); ?>">
                    <input type="checkbox" name="weekly_off_days[]" value="<?php echo e($dayNum); ?>" class="rounded"
                           style="accent-color: <?php echo e($themeColor); ?>;"
                           <?php if(in_array($dayNum, $selectedOff)): echo 'checked'; endif; ?>>
                    <?php echo e($dayName); ?>

                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php $__errorArgs = ['weekly_off_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <?php $__errorArgs = ['weekly_off_days.*'];
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

<script>
function syncDailyHoursFromSchedule() {
    const start = document.getElementById('work_start_time')?.value;
    const end = document.getElementById('work_end_time')?.value;
    const hoursInput = document.getElementById('daily_hours');
    if (!start || !end || !hoursInput) return;

    const [sh, sm] = start.split(':').map(Number);
    const [eh, em] = end.split(':').map(Number);
    const mins = (eh * 60 + em) - (sh * 60 + sm);
    if (mins > 0) {
        hoursInput.value = Math.round((mins / 60) * 10) / 10;
    }
}
</script>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/employees/partials/work-schedule-fields.blade.php ENDPATH**/ ?>