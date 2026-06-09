<?php $__env->startSection('page-title', $sale->product_service); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $sale->product_service,
    'subtitle' => 'تفاصيل الصفقة — ' . ($sale->client?->name ?? ''),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />',
    'actionUrl' => route('crm.pipeline.edit', $sale),
    'actionLabel' => 'تعديل',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 space-y-4 text-sm">
        <h3 class="font-bold text-gray-900 font-tajawal border-b pb-2">بيانات الصفقة</h3>
        <p><span class="text-gray-500">العميل:</span> <a href="<?php echo e(route('crm.clients.show', $sale->client)); ?>" class="font-semibold" style="color: <?php echo e($themeColor); ?>;"><?php echo e($sale->client?->name); ?></a></p>
        <p><span class="text-gray-500">المشروع:</span> <span class="text-gray-900"><?php echo e($sale->project?->name ?? '—'); ?></span></p>
        <p><span class="text-gray-500">المرحلة:</span> <span class="px-2 py-1 rounded-lg text-xs font-medium" style="background: <?php echo e($themeColor); ?>15; color: <?php echo e($themeColor); ?>;"><?php echo e($sale->stage); ?></span></p>
        <p><span class="text-gray-500">القيمة:</span> <strong class="text-gray-900"><?php echo e(\App\Helpers\SettingsHelper::formatMoney($sale->estimated_value)); ?></strong></p>
        <p><span class="text-gray-500">نوع الوحدة:</span> <?php echo e($sale->unit_type ?? '—'); ?></p>
        <p><span class="text-gray-500">معاينة:</span> <?php echo e($sale->viewing_date?->format('Y-m-d') ?? '—'); ?></p>
        <p><span class="text-gray-500">مندوب المبيعات:</span> <?php echo e($sale->salesRep?->name); ?></p>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
        <h3 class="font-bold mb-4 text-gray-900 font-tajawal">تحديث المرحلة</h3>
        <form action="<?php echo e(route('crm.pipeline.update-stage', $sale)); ?>" method="POST" class="space-y-3" id="deal-stage-form">
            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
            <div class="flex gap-2">
                <select name="stage" id="deal-stage-select" class="flex-1 border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal">
                    <?php $stageLabels = ['lead'=>'عميل محتمل','prospect'=>'مهتم','proposal'=>'عرض سعر','negotiation'=>'تفاوض','closed_won'=>'تم البيع','closed_lost'=>'خسارة']; ?>
                    <?php $__currentLoopData = ['lead','prospect','proposal','negotiation','closed_won','closed_lost']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s); ?>" <?php if($sale->stage==$s): echo 'selected'; endif; ?>><?php echo e($stageLabels[$s] ?? $s); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button type="submit" class="px-5 py-3 rounded-xl text-white font-semibold font-tajawal" style="background: <?php echo e($themeColor); ?>;">تحديث</button>
            </div>
            <div id="deal-lost-fields" class="hidden space-y-2 p-4 rounded-xl bg-red-50 border border-red-100">
                <label class="block text-xs font-bold text-red-700 font-tajawal">سبب الخسارة *</label>
                <select name="lost_reason" class="w-full border-2 border-red-200 rounded-xl px-4 py-2 text-sm font-tajawal">
                    <option value="">— اختر —</option>
                    <?php $__currentLoopData = config('crm_intelligence.lost_reasons'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if($sale->lost_reason==$key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <textarea name="lost_reason_notes" rows="2" class="w-full border-2 border-red-200 rounded-xl px-4 py-2 text-sm font-tajawal" placeholder="تفاصيل إضافية"><?php echo e($sale->lost_reason_notes); ?></textarea>
            </div>
        </form>
        <?php if($sale->lost_reason): ?>
            <p class="mt-3 text-sm text-red-600 font-tajawal">سبب الخسارة: <?php echo e(config('crm_intelligence.lost_reasons')[$sale->lost_reason] ?? $sale->lost_reason); ?></p>
        <?php endif; ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sel = document.getElementById('deal-stage-select');
            const box = document.getElementById('deal-lost-fields');
            function toggle() { box?.classList.toggle('hidden', sel?.value !== 'closed_lost'); }
            sel?.addEventListener('change', toggle);
            toggle();
        });
        </script>
        <?php if($sale->notes): ?><p class="mt-4 text-sm text-gray-600 p-4 bg-gray-50 rounded-xl"><?php echo e($sale->notes); ?></p><?php endif; ?>
    </div>
</div>

<?php if($sale->stage === 'closed_won' || $sale->company_commission_amount): ?>
<?php $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v); ?>
<div class="mt-6 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b font-bold font-tajawal flex justify-between" style="background:linear-gradient(135deg,<?php echo e($themeColor); ?>08 0%,<?php echo e($themeColor); ?>03 100%);">
        <span>عمولة الوكيل (Freelance Scheme)</span>
        <?php if($sale->commission_collected): ?><span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800 font-semibold">محصّلة</span><?php else: ?><span class="text-xs px-2 py-1 rounded-full bg-amber-100 text-amber-800 font-semibold">معلّقة</span><?php endif; ?>
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-2 lg:grid-cols-4 gap-4 text-sm font-tajawal">
        <div><span class="text-xs text-gray-500 block">نوع العملية</span><strong><?php echo e($sale->transactionTypeLabel()); ?></strong></div>
        <div><span class="text-xs text-gray-500 block">عمولة الشركة</span><strong><?php echo e($money($sale->company_commission_amount ?? 0)); ?></strong></div>
        <div><span class="text-xs text-gray-500 block">حصة الوكلاء</span><strong><?php echo e($money($commissionPreview['agent_total'] ?? 0)); ?></strong></div>
        <div><span class="text-xs text-gray-500 block">باقي الشركة</span><strong><?php echo e($money($commissionPreview['company_retained'] ?? 0)); ?></strong></div>
    </div>
    <?php if(!empty($commissionPreview['splits'])): ?>
    <div class="border-t divide-y">
        <?php $__currentLoopData = $commissionPreview['splits']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $split): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $u = \App\Models\User::find($split['user_id']); ?>
        <div class="px-5 py-3 flex justify-between text-sm font-tajawal">
            <span><?php echo e($u?->name ?? 'وكيل'); ?> <span class="text-xs text-gray-400">(<?php echo e($split['agent_role']); ?>)</span></span>
            <span class="font-bold"><?php echo e($money($split['amount'])); ?> <span class="text-xs text-gray-500">(<?php echo e($split['percent']); ?>%)</span></span>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
    <?php if(!$sale->commission_collected && auth()->user()?->hasRole(['super_admin','admin','sales_manager'])): ?>
    <div class="p-4 border-t">
        <form method="POST" action="<?php echo e(route('crm.pipeline.commission-collected', $sale)); ?>"><?php echo csrf_field(); ?>
            <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-semibold font-tajawal" style="background:<?php echo e($themeColor); ?>">تسجيل تحصيل عمولة الشركة</button>
        </form>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\pipeline\show.blade.php ENDPATH**/ ?>