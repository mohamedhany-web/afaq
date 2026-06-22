<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $scope = \App\Services\LeaveScopeService::for(auth()->user());
    $titles = [
        'admin' => ['title' => 'إدارة الإجازات', 'subtitle' => 'مراجعة واعتماد طلبات الإجازة لجميع الموظفين'],
        'operations' => ['title' => 'موافقات الإجازات — العمليات', 'subtitle' => 'مراجعة واعتماد طلبات الإجازة من العمليات'],
        'manager' => ['title' => 'إجازات الفريق', 'subtitle' => 'مراجعة طلبات فريقك وإجازاتك الشخصية'],
        'self' => ['title' => 'إجازاتي', 'subtitle' => 'تقديم طلب إجازة ومتابعة حالة الطلبات'],
    ];
    $header = $titles[$mode] ?? $titles['self'];
    $typeColors = [
        'annual' => 'bg-green-100 text-green-800',
        'sick' => 'bg-blue-100 text-blue-800',
        'emergency' => 'bg-red-100 text-red-800',
        'maternity' => 'bg-pink-100 text-pink-800',
        'paternity' => 'bg-purple-100 text-purple-800',
        'unpaid' => 'bg-gray-100 text-gray-800',
        'compensatory' => 'bg-amber-100 text-amber-800',
    ];
    $statusColors = [
        'pending' => 'bg-amber-100 text-amber-800',
        'approved' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
    ];
?>

<?php $__env->startSection('page-title', $header['title']); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('crm.partials.page-header', [
    'title' => $header['title'],
    'subtitle' => $header['subtitle'],
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(!$employee && $mode === 'self'): ?>
<div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900 font-tajawal">
    لا يوجد ملف موظف مرتبط بحسابك. تواصل مع الإدارة لربط حسابك قبل تقديم طلب إجازة.
</div>
<?php endif; ?>

<div class="grid grid-cols-2 lg:grid-cols-<?php echo e($mode === 'admin' ? '4' : '5'); ?> gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', [
        'label' => $mode === 'self' ? 'طلباتي المعلقة' : 'طلبات معلقة',
        'value' => $stats['pending'],
        'accent' => 'amber',
        'compact' => true,
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'href' => route('leaves.index', ['status' => 'pending']) . '#page-data',
        'linkLabel' => 'عرض المعلّقة',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', [
        'label' => 'موافق عليها (الشهر)',
        'value' => $stats['approved_month'],
        'accent' => 'green',
        'compact' => true,
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'href' => route('leaves.index', ['status' => 'approved']) . '#page-data',
        'linkLabel' => 'عرض الموافق عليها',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', [
        'label' => 'مرفوضة (الشهر)',
        'value' => $stats['rejected_month'],
        'accent' => 'red',
        'compact' => true,
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'href' => route('leaves.index', ['status' => 'rejected']) . '#page-data',
        'linkLabel' => 'عرض المرفوضة',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', [
        'label' => $mode === 'admin' ? 'أيام معتمدة (السنة)' : 'أيامي المعتمدة',
        'value' => $stats['total_days_year'],
        'accent' => 'blue',
        'compact' => true,
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
        'href' => route('leaves.index') . '#page-data',
        'linkLabel' => 'عرض الطلبات',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php if($mode !== 'admin' && $stats['remaining_annual'] !== null): ?>
    <?php echo $__env->make('crm.partials.stat-card', [
        'label' => 'رصيد السنوية',
        'value' => $stats['remaining_annual'] . ' يوم',
        'accent' => 'theme',
        'compact' => true,
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />',
        'footer' => '<span class="text-gray-500">من ' . config('leaves.annual_limit_days', 21) . ' يوم سنوياً</span>',
        'href' => route('leaves.index') . '#page-data',
        'linkLabel' => 'عرض الرصيد',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
</div>

<div id="page-data" class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
    <div class="px-5 py-4 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-3" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        <h3 class="text-lg font-bold text-gray-900 font-tajawal">طلبات الإجازة</h3>
        <div class="flex flex-wrap items-center gap-2">
            <form method="GET" action="<?php echo e(route('leaves.index')); ?>" class="flex items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-xl text-sm font-tajawal focus:ring-2 focus:border-transparent" style="focus-ring-color: <?php echo e($themeColor); ?>;">
                    <option value="">جميع الحالات</option>
                    <?php $__currentLoopData = config('leaves.status_labels', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>" <?php if(request('status') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </form>
            <?php if($canRequest): ?>
            <button id="newLeaveBtn" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white text-sm font-semibold shadow-md hover:shadow-lg transition-all font-tajawal"
                    style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                طلب إجازة
            </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm font-tajawal">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <?php if($mode !== 'self'): ?>
                    <th class="px-5 py-3 text-right">الموظف</th>
                    <?php endif; ?>
                    <th class="px-5 py-3 text-right">النوع</th>
                    <th class="px-5 py-3 text-right">من</th>
                    <th class="px-5 py-3 text-right">إلى</th>
                    <th class="px-5 py-3 text-right">الأيام</th>
                    <th class="px-5 py-3 text-right">السبب</th>
                    <th class="px-5 py-3 text-right">الحالة</th>
                    <?php if($canApprove): ?>
                    <th class="px-5 py-3 text-right">إجراء</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $leaves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50/80">
                    <?php if($mode !== 'self'): ?>
                    <td class="px-5 py-4">
                        <div class="font-semibold text-gray-900"><?php echo e($leave->employee?->first_name); ?> <?php echo e($leave->employee?->last_name); ?></div>
                        <div class="text-xs text-gray-500"><?php echo e($leave->employee?->position); ?></div>
                    </td>
                    <?php endif; ?>
                    <td class="px-5 py-4">
                        <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($typeColors[$leave->leave_type] ?? 'bg-gray-100 text-gray-800'); ?>">
                            <?php echo e($leave->leave_type_name); ?>

                        </span>
                    </td>
                    <td class="px-5 py-4 text-gray-900"><?php echo e($leave->start_date->format('Y/m/d')); ?></td>
                    <td class="px-5 py-4 text-gray-900"><?php echo e($leave->end_date->format('Y/m/d')); ?></td>
                    <td class="px-5 py-4 font-semibold"><?php echo e($leave->total_days); ?></td>
                    <td class="px-5 py-4 text-gray-600 max-w-[12rem] truncate" title="<?php echo e($leave->reason); ?>"><?php echo e($leave->reason); ?></td>
                    <td class="px-5 py-4">
                        <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($statusColors[$leave->status] ?? 'bg-gray-100 text-gray-800'); ?>">
                            <?php echo e(config('leaves.status_labels.' . $leave->status, $leave->status)); ?>

                        </span>
                        <?php if($leave->status === 'rejected' && $leave->rejection_reason): ?>
                        <div class="text-xs text-red-600 mt-1 max-w-[10rem] truncate" title="<?php echo e($leave->rejection_reason); ?>"><?php echo e($leave->rejection_reason); ?></div>
                        <?php endif; ?>
                    </td>
                    <?php if($canApprove): ?>
                    <td class="px-5 py-4">
                        <?php if($scope->canApproveLeave($leave)): ?>
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="approveLeave(<?php echo e($leave->id); ?>)" class="p-2 text-green-600 hover:bg-green-50 rounded-lg" title="موافقة">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            <button type="button" onclick="rejectLeave(<?php echo e($leave->id); ?>)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="رفض">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <?php else: ?>
                        <span class="text-xs text-gray-400">—</span>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="<?php echo e(($mode !== 'self' ? 7 : 6) + ($canApprove ? 1 : 0)); ?>" class="px-5 py-16 text-center text-gray-500">
                        لا توجد طلبات إجازة للعرض
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($leaves->hasPages()): ?>
    <div class="px-5 py-4 border-t"><?php echo e($leaves->links()); ?></div>
    <?php endif; ?>
</div>

<?php if($canRequest): ?>
<div id="newLeaveModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/50" onclick="closeNewLeaveModal()"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl border overflow-hidden">
            <div class="px-5 py-4 border-b font-bold font-tajawal" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>10 0%, <?php echo e($themeColor); ?>05 100%);">طلب إجازة جديد</div>
            <form id="newLeaveForm" class="p-5 space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5 font-tajawal">نوع الإجازة</label>
                    <select name="leave_type" required class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-tajawal">
                        <option value="">اختر النوع</option>
                        <?php $__currentLoopData = $leaveTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5 font-tajawal">من</label>
                        <input type="date" name="start_date" required min="<?php echo e(now()->toDateString()); ?>" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5 font-tajawal">إلى</label>
                        <input type="date" name="end_date" required min="<?php echo e(now()->toDateString()); ?>" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5 font-tajawal">السبب</label>
                    <textarea name="reason" required rows="3" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-tajawal" placeholder="اذكر سبب طلب الإجازة"></textarea>
                </div>
                <?php if($stats['remaining_annual'] !== null): ?>
                <p class="text-xs text-gray-500 font-tajawal">رصيد الإجازة السنوية المتبقي: <strong><?php echo e($stats['remaining_annual']); ?></strong> يوم</p>
                <?php endif; ?>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeNewLeaveModal()" class="px-4 py-2 rounded-xl border text-sm font-tajawal">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-semibold" style="background: <?php echo e($themeColor); ?>;">تقديم الطلب</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php endif; ?>

<?php if($canApprove): ?>
<div id="rejectLeaveModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/50" onclick="closeRejectLeaveModal()"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl border overflow-hidden">
            <div class="px-5 py-4 border-b font-bold font-tajawal text-red-700 bg-red-50">رفض طلب الإجازة</div>
            <form id="rejectLeaveForm" class="p-5 space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="leave_id" id="rejectLeaveId">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5 font-tajawal">سبب الرفض</label>
                    <textarea name="rejection_reason" required rows="3" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-tajawal"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeRejectLeaveModal()" class="px-4 py-2 rounded-xl border text-sm font-tajawal">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white text-sm font-semibold">رفض الطلب</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if($canRequest || $canApprove): ?>
<script>
let isSubmittingLeave = false;

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('newLeaveBtn')?.addEventListener('click', () => {
        document.getElementById('newLeaveModal').classList.remove('hidden');
    });

    document.getElementById('newLeaveForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!isSubmittingLeave) submitLeaveRequest();
    });

    document.getElementById('rejectLeaveForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        submitRejectLeave();
    });
});

function closeNewLeaveModal() {
    isSubmittingLeave = false;
    document.getElementById('newLeaveModal')?.classList.add('hidden');
    document.getElementById('newLeaveForm')?.reset();
}

function closeRejectLeaveModal() {
    document.getElementById('rejectLeaveModal')?.classList.add('hidden');
    document.getElementById('rejectLeaveForm')?.reset();
}

<?php if($canRequest): ?>
function submitLeaveRequest() {
    if (isSubmittingLeave) return;
    isSubmittingLeave = true;
    const form = document.getElementById('newLeaveForm');
    const btn = form.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = 'جاري الإرسال...'; }

    fetch('<?php echo e(route("leaves.store")); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: new FormData(form)
    })
    .then(async r => {
        const data = await r.json().catch(() => ({}));
        if (!r.ok) {
            throw new Error(data.error || data.message || 'تعذر تقديم الطلب');
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            notify(data.message, 'success');
            closeNewLeaveModal();
            setTimeout(() => location.reload(), 800);
        } else {
            isSubmittingLeave = false;
            if (btn) { btn.disabled = false; btn.textContent = 'تقديم الطلب'; }
            notify(data.error || 'حدث خطأ', 'error');
        }
    })
    .catch((err) => {
        isSubmittingLeave = false;
        if (btn) { btn.disabled = false; btn.textContent = 'تقديم الطلب'; }
        notify(err.message || 'حدث خطأ في تقديم الطلب', 'error');
    });
}
<?php endif; ?>

<?php if($canApprove): ?>
function approveLeave(id) {
    if (!confirm('الموافقة على هذا الطلب؟')) return;
    fetch(`/leaves/${id}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { notify(data.message, 'success'); setTimeout(() => location.reload(), 800); }
        else notify(data.error, 'error');
    });
}

function rejectLeave(id) {
    document.getElementById('rejectLeaveId').value = id;
    document.getElementById('rejectLeaveModal').classList.remove('hidden');
}

function submitRejectLeave() {
    const form = document.getElementById('rejectLeaveForm');
    const id = form.querySelector('[name=leave_id]').value;
    fetch(`/leaves/${id}/reject`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: new FormData(form)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { notify(data.message, 'success'); closeRejectLeaveModal(); setTimeout(() => location.reload(), 800); }
        else notify(data.error, 'error');
    });
}
<?php endif; ?>

function notify(message, type) {
    const colors = { success: 'bg-green-600', error: 'bg-red-600', info: 'bg-blue-600' };
    const el = document.createElement('div');
    el.className = `fixed top-4 left-4 ${colors[type] || colors.info} text-white px-5 py-3 rounded-xl shadow-lg z-[100] font-tajawal text-sm`;
    el.textContent = message;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 3000);
}
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\leaves\index.blade.php ENDPATH**/ ?>