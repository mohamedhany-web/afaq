<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $routes = $routes ?? [
        'index' => route('crm.follow-ups.index'),
        'store' => route('crm.follow-ups.store'),
        'complete' => 'crm.follow-ups.complete',
        'cancel' => 'crm.follow-ups.cancel',
    ];
    $enhanced = $enhanced ?? false;
    $prevDate = $date->copy()->subDay()->toDateString();
    $nextDate = $date->copy()->addDay()->toDateString();
    $pageTitle = ($workspace ?? '') === 'operations' ? 'متابعات العمليات' : 'جدول المتابعات';
    $pageSubtitle = $enhanced
        ? 'متابعة شاملة للعملاء — تصنيف حسب حالة المتابعة والمندوب والتاريخ'
        : 'مواعيد المكالمات والمعاينات والاجتماعات — مع تذكير تلقائي';
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $pageTitle,
    'subtitle' => $pageSubtitle,
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
    'secondaryUrl' => ($workspace ?? '') === 'operations' ? route('operations.crm.index') : null,
    'secondaryLabel' => ($workspace ?? '') === 'operations' ? 'مركز CRM' : null,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?>
<div class="mb-4 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 font-tajawal"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if($errors->any()): ?>
<div class="mb-4 rounded-2xl border-2 border-red-200 bg-red-50 px-4 py-3 font-tajawal text-sm text-red-800">
    <p class="font-bold mb-1">تعذر حفظ المتابعة:</p>
    <ul class="list-disc list-inside space-y-0.5">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?>

<?php if($enhanced): ?>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-4">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'اليوم', 'value' => $stats['today'], 'compact' => true, 'accent' => 'theme', 'href' => $routes['index'] . '?' . http_build_query(array_merge(request()->except('page'), ['bucket' => 'today'])) . '#page-data', 'linkLabel' => 'عرض'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'متأخرة', 'value' => $stats['overdue'], 'compact' => true, 'accent' => 'red', 'href' => $routes['index'] . '?' . http_build_query(array_merge(request()->except('page'), ['bucket' => 'overdue'])) . '#page-data', 'linkLabel' => 'عرض'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'قادمة', 'value' => $stats['upcoming'], 'compact' => true, 'accent' => 'blue', 'href' => $routes['index'] . '?' . http_build_query(array_merge(request()->except('page'), ['bucket' => 'upcoming'])) . '#page-data', 'linkLabel' => 'عرض'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مكتملة', 'value' => $stats['completed'], 'compact' => true, 'accent' => 'green', 'href' => $routes['index'] . '?' . http_build_query(array_merge(request()->except('page'), ['bucket' => 'completed'])) . '#page-data', 'linkLabel' => 'عرض'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'ملغاة', 'value' => $stats['cancelled'], 'compact' => true, 'accent' => 'amber', 'href' => $routes['index'] . '?' . http_build_query(array_merge(request()->except('page'), ['bucket' => 'cancelled'])) . '#page-data', 'linkLabel' => 'عرض'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'عملاء', 'value' => $stats['clients'], 'compact' => true, 'accent' => 'purple', 'href' => $routes['index'] . '#client-breakdown', 'linkLabel' => 'عرض'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php echo $__env->make('crm.follow-ups.partials.status-tabs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php else: ?>
<div class="grid grid-cols-3 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مواعيد اليوم', 'value' => $stats['today'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />', 'href' => $routes['index'] . '?' . http_build_query(['bucket' => 'today']) . '#page-data', 'linkLabel' => 'عرض اليوم'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'متأخرة', 'value' => $stats['overdue'], 'accent' => 'red', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01" />', 'href' => $routes['index'] . '?' . http_build_query(['bucket' => 'overdue']) . '#page-data', 'linkLabel' => 'عرض المتأخرة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الأسبوع القادم', 'value' => $stats['upcoming'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />', 'href' => $routes['index'] . '?' . http_build_query(['bucket' => 'upcoming']) . '#page-data', 'linkLabel' => 'عرض القادمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 <?php echo e($enhanced ? 'xl:grid-cols-4' : 'xl:grid-cols-3'); ?> gap-6">
    <?php if($enhanced): ?>
    <div class="xl:col-span-1 order-2 xl:order-1">
        <div id="client-breakdown" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden sticky top-4">
            <div class="px-4 py-3 border-b font-bold text-sm font-tajawal" style="background:linear-gradient(135deg,<?php echo e($themeColor); ?>08,transparent)">تصنيف العملاء</div>
            <ul class="divide-y max-h-[520px] overflow-y-auto text-sm font-tajawal">
                <?php $__empty_1 = true; $__currentLoopData = $clientBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li class="p-3 hover:bg-gray-50">
                    <a href="<?php echo e($row->client->profileUrl()); ?>" class="font-semibold hover:underline" style="color:<?php echo e($themeColor); ?>"><?php echo e($row->client->name); ?></a>
                    <div class="flex flex-wrap gap-1 mt-1.5">
                        <?php if($row->overdue_cnt > 0): ?>
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-red-100 text-red-700 font-bold"><?php echo e($row->overdue_cnt); ?> متأخر</span>
                        <?php endif; ?>
                        <?php if($row->today_cnt > 0): ?>
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 font-bold"><?php echo e($row->today_cnt); ?> اليوم</span>
                        <?php endif; ?>
                        <?php if($row->completed_cnt > 0): ?>
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-green-100 text-green-700 font-bold"><?php echo e($row->completed_cnt); ?> مكتمل</span>
                        <?php endif; ?>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1"><?php echo e($row->client->lead_stage ?? $row->client->status); ?></p>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="p-6 text-center text-gray-400 text-xs">لا توجد بيانات للتصنيف</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <div class="xl:col-span-1 order-1 xl:order-2">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden sticky top-4">
            <div class="px-5 py-4 border-b font-tajawal font-bold text-gray-900"
                 style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08, transparent);">
                تسجيل متابعة جديدة
            </div>
            <form action="<?php echo e($routes['store']); ?>" method="POST" class="p-5 space-y-3 font-tajawal" id="follow-up-create-form">
                <?php echo csrf_field(); ?>
                <?php echo $__env->make('partials.client-search-select', [
                    'required' => true,
                    'value' => old('client_id'),
                    'inputClass' => 'w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal',
                    'crmScope' => true,
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php if($canAssignOthers): ?>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">الموظف المسؤول</label>
                    <select name="user_id" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm">
                        <?php $__currentLoopData = $assignableUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($u->id); ?>" <?php if($u->id === auth()->id()): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">نوع النشاط *</label>
                    <select name="interaction_type" required class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm">
                        <?php $__currentLoopData = $typeLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if(old('interaction_type') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">التاريخ *</label>
                        <input type="date" name="scheduled_at" value="<?php echo e(old('scheduled_at', now()->toDateString())); ?>" required
                               class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">الوقت *</label>
                        <input type="time" name="scheduled_time" value="<?php echo e(old('scheduled_time', now()->format('H:i'))); ?>" required
                               class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">التفاصيل *</label>
                    <textarea name="notes" rows="3" required placeholder="ملاحظات المتابعة..."
                              class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm resize-none"><?php echo e(old('notes')); ?></textarea>
                </div>
                <button type="submit" class="w-full py-2.5 rounded-xl text-sm font-bold text-white"
                        style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>, <?php echo e($themeColor); ?>dd);">
                    حفظ في الجدول
                </button>
            </form>
        </div>
    </div>

    <div class="<?php echo e($enhanced ? 'xl:col-span-2' : 'xl:col-span-2'); ?> space-y-4 order-3">
        <div class="mb-2">
            <?php if(!$bucket && $view !== 'range' && $view !== 'all'): ?>
            <div class="flex items-center gap-2 mb-3 font-tajawal">
                <a href="<?php echo e($routes['index'] . '?' . http_build_query(array_merge(request()->except('date', 'page'), ['date' => $prevDate]))); ?>"
                   class="p-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50">‹</a>
                <a href="<?php echo e($routes['index'] . '?' . http_build_query(array_merge(request()->except('date', 'page'), ['date' => now()->toDateString()]))); ?>"
                   class="text-xs font-bold px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50" style="color: <?php echo e($themeColor); ?>; border-color: <?php echo e($themeColor); ?>40;">اليوم</a>
                <a href="<?php echo e($routes['index'] . '?' . http_build_query(array_merge(request()->except('date', 'page'), ['date' => $nextDate]))); ?>"
                   class="p-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50">›</a>
                <span class="text-xs text-gray-500 mr-2">عرض يوم: <?php echo e($date->translatedFormat('l j F Y')); ?></span>
            </div>
            <?php endif; ?>

            <?php if($enhanced): ?>
            <div class="flex flex-wrap gap-2 mb-3 font-tajawal text-xs">
                <?php $__currentLoopData = ['day' => 'يوم', 'week' => 'أسبوع', 'range' => 'نطاق تاريخ', 'all' => 'بدون قيد تاريخ']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mode => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($routes['index'] . '?' . http_build_query(array_merge(request()->except('view', 'page'), ['view' => $mode]))); ?>"
                   class="px-3 py-1 rounded-lg font-semibold <?php echo e(($view ?? 'day') === $mode ? 'text-white' : 'bg-gray-100 text-gray-600'); ?>"
                   <?php if(($view ?? 'day') === $mode): ?> style="background:<?php echo e($themeColor); ?>" <?php endif; ?>><?php echo e($lbl); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

            <?php echo $__env->make('crm.partials.filter-bar', [
                'preserve' => array_filter(['bucket' => $bucket, 'view' => ($view ?? 'day') !== 'day' ? $view : null]),
                'typeLabels' => $typeLabels,
                'clientStatusOptions' => $clientStatusOptions ?? [],
                'stageLabels' => $stageLabels ?? [],
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <div id="page-data" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm font-tajawal">
                    <thead class="bg-gray-50 border-b">
                        <tr class="text-gray-600 text-xs">
                            <th class="text-right p-3 font-bold">الوقت</th>
                            <th class="text-right p-3 font-bold">النشاط</th>
                            <th class="text-right p-3 font-bold">العميل</th>
                            <?php if($enhanced): ?>
                            <th class="text-right p-3 font-bold">حالة العميل</th>
                            <?php endif; ?>
                            <th class="text-right p-3 font-bold">الموظف</th>
                            <th class="text-right p-3 font-bold">أضافه</th>
                            <th class="text-right p-3 font-bold">المتابعة</th>
                            <th class="text-right p-3 font-bold"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $followUps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $rowClass = $highlight === $item->id ? 'bg-amber-50' : '';
                            if ($item->isOverdue()) $rowClass = 'bg-red-50';
                        ?>
                        <tr class="border-t border-gray-100 hover:bg-gray-50/80 <?php echo e($rowClass); ?>">
                            <td class="p-3 whitespace-nowrap font-bold tabular-nums" dir="ltr">
                                <?php echo e($item->scheduled_at->format('H:i')); ?>

                                <div class="text-[10px] text-gray-400 font-normal"><?php echo e($item->scheduled_at->format('Y/m/d')); ?></div>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-gray-100"><?php echo e($item->typeLabel()); ?></span>
                            </td>
                            <td class="p-3">
                                <a href="<?php echo e($item->client->profileUrl()); ?>" class="font-semibold hover:underline" style="color:<?php echo e($themeColor); ?>"><?php echo e($item->client->name); ?></a>
                                <div class="text-xs text-gray-500" dir="ltr"><?php echo e($item->client->phone); ?></div>
                            </td>
                            <?php if($enhanced): ?>
                            <td class="p-3">
                                <?php echo $__env->make('crm.clients.partials.status-badge', ['status' => $item->client->status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <div class="text-[10px] text-gray-400 mt-0.5"><?php echo e($stageLabels[$item->client->lead_stage] ?? ($item->client->lead_stage ?? '—')); ?></div>
                            </td>
                            <?php endif; ?>
                            <td class="p-3 text-gray-800"><?php echo e($item->user->name); ?></td>
                            <td class="p-3 text-gray-600 text-xs"><?php echo e($item->creator->name); ?></td>
                            <td class="p-3">
                                <?php if($item->status === 'completed'): ?>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold">مكتمل</span>
                                <?php elseif($item->status === 'cancelled'): ?>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-200 text-gray-600 font-semibold">ملغى</span>
                                <?php elseif($item->isOverdue()): ?>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-semibold">متأخر</span>
                                <?php else: ?>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-semibold">مجدول</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3">
                                <?php if($item->status === 'scheduled'): ?>
                                <div class="flex gap-1">
                                    <form action="<?php echo e(route($routes['complete'], $item)); ?>" method="POST">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="text-xs px-2 py-1 rounded-lg bg-green-50 text-green-700 font-semibold hover:bg-green-100">تم</button>
                                    </form>
                                    <form action="<?php echo e(route($routes['cancel'], $item)); ?>" method="POST" onsubmit="return confirm('إلغاء الموعد؟')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="text-xs px-2 py-1 rounded-lg bg-gray-100 text-gray-600 font-semibold">إلغاء</button>
                                    </form>
                                </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr class="border-t-0 <?php echo e($rowClass); ?>">
                            <td colspan="<?php echo e($enhanced ? 8 : 7); ?>" class="px-3 pb-3 pt-0 text-xs text-gray-600"><?php echo e(Str::limit($item->notes, 160)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e($enhanced ? 8 : 7); ?>" class="p-12 text-center text-gray-400 font-tajawal">
                                لا توجد متابعات مطابقة للفلاتر
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($followUps->hasPages()): ?>
            <div class="p-4 border-t"><?php echo e($followUps->links()); ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('follow-up-create-form')?.addEventListener('submit', function (e) {
    const root = this.querySelector('.client-search-select');
    if (!root || typeof Alpine === 'undefined') return;
    const el = root.querySelector('[x-data]');
    const data = el ? Alpine.$data(el) : null;
    const hidden = root.querySelector('input[type="hidden"][name="client_id"]');
    if (data && hidden) hidden.value = data.selectedId || '';
    if (!hidden?.value) {
        e.preventDefault();
        alert('اختر العميل من نتائج البحث قبل الحفظ.');
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\follow-ups\partials\workspace.blade.php ENDPATH**/ ?>