
<?php $__env->startSection('page-title', $client->name); ?>

<?php $__env->startSection('content'); ?>
<div data-client-page="<?php echo e($client->id); ?>" class="hidden" aria-hidden="true"></div>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $typeLabels = ['individual' => 'فرد', 'small_business' => 'شركة / منشأة'];
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $client->name,
    'subtitle' => 'مسار العميل — اسحب لتحديث المراحل وسجّل المتابعات',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
    'actionUrl' => route('crm.pipeline.create', ['client_id' => $client->id]),
    'actionLabel' => 'صفقة جديدة',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="mb-4">
    <a href="<?php echo e(route('crm.pipeline.index')); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900 font-tajawal">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        العودة لقائمة العملاء
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مرحلة الرحلة', 'value' => $stageLabels[$client->lead_stage] ?? $client->lead_stage, 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الصفقات', 'value' => $dealsCount, 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'قيمة الصفقات', 'value' => $money($dealsValue), 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'حالة العميل', 'value' => match($client->status) { 'prospect' => 'محتمل', 'active' => 'نشط', 'inactive' => 'غير نشط', 'suspended' => 'موقوف', default => $client->status }, 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<?php echo $__env->make('crm.clients.partials.journey-kanban', compact('client', 'stageLabels', 'themeColor'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
    
    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
                 style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, transparent 100%);">
                بيانات العميل
            </div>
            <div class="p-5 space-y-3 text-sm font-tajawal">
                <div>
                    <span class="text-xs font-bold text-gray-500">الهاتف</span>
                    <p class="font-medium text-gray-900" dir="ltr"><?php echo e($client->phone); ?></p>
                </div>
                <div>
                    <span class="text-xs font-bold text-gray-500">البريد</span>
                    <p class="text-gray-900" dir="ltr"><?php echo e($client->email ?? '—'); ?></p>
                </div>
                <?php if($client->company_name): ?>
                <div>
                    <span class="text-xs font-bold text-gray-500">الشركة</span>
                    <p class="text-gray-900"><?php echo e($client->company_name); ?></p>
                </div>
                <?php endif; ?>
                <div>
                    <span class="text-xs font-bold text-gray-500">نوع العميل</span>
                    <p class="text-gray-900"><?php echo e($typeLabels[$client->client_type] ?? 'فرد'); ?></p>
                </div>
                <div>
                    <span class="text-xs font-bold text-gray-500">حالة الحساب</span>
                    <div class="mt-1"><?php echo $__env->make('crm.clients.partials.status-badge', ['status' => $client->status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
                </div>
                <?php if($client->assignedEmployee): ?>
                <div>
                    <span class="text-xs font-bold text-gray-500">مسؤول المبيعات</span>
                    <p class="text-gray-900"><?php echo e(trim($client->assignedEmployee->first_name . ' ' . $client->assignedEmployee->last_name)); ?></p>
                </div>
                <?php endif; ?>
                <div>
                    <span class="text-xs font-bold text-gray-500">من أضاف العميل</span>
                    <div class="mt-1"><?php echo $__env->make('crm.clients.partials.created-by', ['client' => $client], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
                </div>
            </div>
            <div class="px-5 py-3 border-t border-gray-100 flex flex-wrap gap-2">
                <a href="<?php echo e(route('crm.clients.edit', $client)); ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white font-tajawal"
                   style="background: <?php echo e($themeColor); ?>;">تعديل البيانات</a>
                <a href="<?php echo e(route('crm.clients.show', $client)); ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold border border-gray-200 text-gray-600 font-tajawal hover:bg-gray-50">الملف الكامل</a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
                 style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, transparent 100%);">
                تسجيل متابعة
            </div>
            <form id="client-interaction-form" class="p-5 space-y-3" action="<?php echo e(route('crm.clients.log-interaction', $client)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1 font-tajawal">نوع النشاط</label>
                    <select name="interaction_type" id="interaction-type" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal" required>
                        <?php $__currentLoopData = $interactionTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 font-tajawal">تاريخ الموعد *</label>
                        <input type="date" name="scheduled_at" value="<?php echo e(now()->toDateString()); ?>" required
                               class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 font-tajawal">وقت الموعد *</label>
                        <input type="time" name="scheduled_time" value="<?php echo e(now()->format('H:i')); ?>" required
                               class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal">
                    </div>
                </div>
                <?php if($client->sales->count()): ?>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1 font-tajawal">الصفقة (اختياري)</label>
                    <select name="sale_id" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal">
                        <option value="">— بدون —</option>
                        <?php $__currentLoopData = $client->sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($sale->id); ?>"><?php echo e($sale->product_service); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1 font-tajawal">التفاصيل *</label>
                    <textarea name="notes" rows="4" required placeholder="اكتب ما تمّ مع العميل..."
                              class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal resize-none"></textarea>
                </div>
                <a href="<?php echo e(route('crm.follow-ups.index')); ?>" class="block text-center text-xs font-semibold font-tajawal" style="color:<?php echo e($themeColor); ?>;">عرض جدول المتابعات</a>
                <button type="submit" class="w-full py-2.5 rounded-xl text-sm font-bold text-white font-tajawal"
                        style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">حفظ في الجدول</button>
                <p id="interaction-feedback" class="text-xs text-center hidden font-tajawal"></p>
            </form>
        </div>

        <?php if($client->notes): ?>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5">
            <h3 class="text-xs font-bold text-gray-500 mb-2 font-tajawal">سجل الملاحظات</h3>
            <p class="text-sm text-gray-700 font-tajawal whitespace-pre-line max-h-48 overflow-y-auto"><?php echo e($client->notes); ?></p>
        </div>
        <?php endif; ?>
    </div>

    
    <div class="xl:col-span-2">
        <?php echo $__env->make('crm.pipeline.partials.client-deals-kanban', compact(
            'client', 'dealColumns', 'dealStageTotals', 'stageLabels', 'stageColors',
            'activeStages', 'closedStages', 'themeColor'
        ), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div>
<?php echo $__env->make('crm.partials.lost-reason-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php echo $__env->make('crm.partials.pipeline-kanban-scripts', [
    'updateUrl' => route('crm.pipeline.update-stage', ['sale' => '__ID__']),
    'loadMoreUrl' => '',
    'payloadKey' => 'stage',
    'itemKey' => 'dealId',
    'group' => 'client-deals-' . $client->id,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const form = document.getElementById('client-interaction-form');
    const feedback = document.getElementById('interaction-feedback');

    form?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        feedback.classList.add('hidden');
        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: new FormData(form),
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error();
            feedback.textContent = data.message || 'تم الحفظ بنجاح';
            feedback.className = 'text-xs text-center font-tajawal text-green-600';
            feedback.classList.remove('hidden');
            form.querySelector('[name="notes"]').value = '';
            setTimeout(() => location.reload(), 800);
        } catch (err) {
            feedback.textContent = 'تعذر الحفظ، حاول مرة أخرى';
            feedback.className = 'text-xs text-center font-tajawal text-red-600';
            feedback.classList.remove('hidden');
        } finally {
            btn.disabled = false;
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\pipeline\client.blade.php ENDPATH**/ ?>