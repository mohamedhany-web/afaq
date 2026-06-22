<?php $__env->startSection('page-title', 'تعديل صفقة'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal'; ?>
<?php echo $__env->make('crm.partials.page-header', ['title' => 'تعديل صفقة', 'subtitle' => $sale->product_service, 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<form action="<?php echo e(route('crm.pipeline.update', $sale)); ?>" method="POST" class="max-w-3xl space-y-4">
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 sm:p-8 space-y-4">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <div><?php echo $__env->make('partials.client-search-select', ['required' => true, 'value' => old('client_id', $sale->client_id), 'label' => $sale->client ? \App\Http\Controllers\ClientSearchController::formatLabel($sale->client) : null, 'inputClass' => $input, 'crmScope' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
    <div><label class="block text-sm font-bold text-gray-700 mb-2">المشروع</label><select name="project_id" class="<?php echo e($input); ?>"><option value="">—</option><?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p->id); ?>" <?php if($sale->project_id==$p->id): echo 'selected'; endif; ?>><?php echo e($p->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
    <div><label class="block text-sm font-bold text-gray-700 mb-2">الوصف</label><input name="product_service" value="<?php echo e(old('product_service', $sale->product_service)); ?>" required class="<?php echo e($input); ?>"></div>
    <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-sm font-bold text-gray-700 mb-2">القيمة (جنيه مصري)</label><input name="estimated_value" type="number" value="<?php echo e(old('estimated_value', $sale->estimated_value)); ?>" required class="<?php echo e($input); ?>"></div>
        <div><label class="block text-sm font-bold text-gray-700 mb-2">الاحتمالية</label><input name="probability_percentage" type="number" value="<?php echo e(old('probability_percentage', $sale->probability_percentage)); ?>" required class="<?php echo e($input); ?>"></div>
    </div>
    <div><label class="block text-sm font-bold text-gray-700 mb-2">المرحلة</label><select name="stage" class="<?php echo e($input); ?>"><?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s); ?>" <?php if($sale->stage==$s): echo 'selected'; endif; ?>><?php echo e($s); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
    <button type="submit" class="px-6 py-3 rounded-xl text-white font-semibold" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">تحديث</button>
</div>
<?php echo $__env->make('crm.pipeline.partials.commission-fields', ['sale' => $sale, 'agents' => $agents, 'transactionTypes' => $transactionTypes], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\pipeline\edit.blade.php ENDPATH**/ ?>