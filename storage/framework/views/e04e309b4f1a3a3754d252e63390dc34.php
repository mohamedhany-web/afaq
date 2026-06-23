<?php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $salesReps = $salesReps ?? collect();
    $selectedRepId = $selectedRepId ?? optional($selectedSalesRep ?? null)->id;
    $compact = !empty($compact);
    $filterAction = $filterAction ?? route('operations.reps.search');
    $repFieldName = $repFieldName ?? 'rep_id';
    $isDashboardFilter = $filterAction === route('operations.dashboard');
    $inputClass = $compact
        ? 'border rounded-xl px-3 py-2.5 text-sm min-w-0'
        : 'border rounded-xl px-4 py-3 text-sm min-w-0';
    $buttonClass = $compact
        ? 'px-5 py-2.5 rounded-xl text-white text-sm font-bold whitespace-nowrap shrink-0'
        : 'px-6 py-3 rounded-xl text-white text-sm font-bold whitespace-nowrap shrink-0';
?>

<div class="flex flex-col sm:flex-row flex-1 min-w-[240px] max-w-2xl gap-2 font-tajawal">
    <form method="GET" action="<?php echo e($filterAction); ?>" class="flex flex-1 gap-2 min-w-0">
        <select name="<?php echo e($repFieldName); ?>" required
                class="<?php echo e($inputClass); ?> flex-1 bg-white"
                aria-label="<?php echo e(__('operations.actions.select_sales_rep')); ?>">
            <option value="" disabled <?php if(!$selectedRepId): echo 'selected'; endif; ?>><?php echo e(__('operations.actions.select_sales_rep')); ?></option>
            <?php $__currentLoopData = $salesReps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($rep->id); ?>" <?php if((int) $selectedRepId === (int) $rep->id): echo 'selected'; endif; ?>>
                    <?php echo e($rep->name); ?><?php if($rep->employee?->department): ?> — <?php echo e($rep->employee->department->name); ?><?php endif; ?>
                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button type="submit" class="<?php echo e($buttonClass); ?>" style="background:<?php echo e($themeColor); ?>">
            <?php echo e($isDashboardFilter ? __('operations.actions.apply_filter') : __('operations.actions.open_rep_workspace')); ?>

        </button>
    </form>

    <form method="GET" action="<?php echo e(route('operations.reps.search')); ?>" class="flex flex-1 gap-2 min-w-0">
        <input type="search" name="q" value="<?php echo e($q ?? ''); ?>"
               placeholder="<?php echo e(__('operations.actions.search_sales_rep_placeholder')); ?>"
               class="<?php echo e($inputClass); ?> flex-1">
        <button type="submit" class="<?php echo e($buttonClass); ?> border border-gray-200 bg-white hover:bg-gray-50" style="color:<?php echo e($themeColor); ?>">
            <?php echo e(__('operations.actions.search')); ?>

        </button>
    </form>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/operations/partials/rep-search-form.blade.php ENDPATH**/ ?>