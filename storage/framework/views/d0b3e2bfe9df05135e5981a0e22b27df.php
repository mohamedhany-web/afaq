
<?php $__env->startSection('page-title', 'ملف الموظف — ' . ($personal['full_name'] ?? '')); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $tabs = [
        'personal' => 'البيانات الشخصية',
        'employment' => 'بيانات التوظيف',
        'cv' => 'السيرة الذاتية',
        'documents' => 'المستندات',
        'attendance' => 'الحضور والانصراف',
        'performance' => 'تقييم الأداء',
        'notes' => 'ملاحظات إدارية',
    ];
    $employmentLabels = ['full_time' => 'دوام كامل', 'part_time' => 'دوام جزئي', 'contract' => 'عقد', 'intern' => 'متدرب'];
    $statusLabels = ['active' => 'نشط', 'inactive' => 'غير نشط', 'on_leave' => 'في إجازة', 'terminated' => 'منتهي الخدمة'];
    $dossierUrl = route('employees.dossier', array_merge(['employee' => $employee], $listQuery));
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'ملف الموظف — ' . $personal['full_name'],
    'subtitle' => ($roleMeta['label'] ?? '') . ' · ' . ($employment['department'] ?? '') . ' · ' . ($employment['employee_id'] ?? ''),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
    'actionUrl' => auth()->user()?->can('edit-employees') ? route('employees.edit', array_merge(['employee' => $employee], $listQuery)) : null,
    'actionLabel' => 'تعديل البيانات',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div><?php endif; ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'أيام حضور (الفترة)', 'value' => $attendance_summary['present'] + $attendance_summary['late'], 'accent' => 'green', 'href' => $dossierUrl . '?tab=attendance#dossier-content', 'linkLabel' => 'سجل الحضور'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'المستندات', 'value' => $documents->count(), 'accent' => 'blue', 'href' => $dossierUrl . '?tab=documents#dossier-content', 'linkLabel' => 'عرض المستندات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'تقييم الأداء', 'value' => ($performance['compliance']['overall_score'] ?? '—') . (isset($performance['compliance']['overall_score']) ? '%' : ''), 'accent' => 'theme', 'href' => $dossierUrl . '?tab=performance#dossier-content', 'linkLabel' => 'التفاصيل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'ملاحظات إدارية', 'value' => $notes->count(), 'accent' => 'amber', 'href' => $dossierUrl . '?tab=notes#dossier-content', 'linkLabel' => 'عرض الملاحظات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="bg-white rounded-2xl border shadow-lg overflow-hidden font-tajawal mb-6">
    <div class="flex flex-wrap gap-1 p-2 border-b bg-gray-50/80">
        <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($dossierUrl); ?>?tab=<?php echo e($key); ?>#dossier-content"
           class="px-4 py-2 rounded-xl text-sm font-bold transition <?php echo e($activeTab === $key ? 'text-white' : 'text-gray-600 hover:bg-gray-100'); ?>"
           <?php if($activeTab === $key): ?> style="background:<?php echo e($themeColor); ?>" <?php endif; ?>><?php echo e($label); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div id="dossier-content" class="p-5 sm:p-6">
        <?php if($activeTab === 'personal'): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php $__currentLoopData = [
                ['الاسم الكامل', $personal['full_name']],
                ['البريد', $personal['email']],
                ['الهاتف', $personal['phone']],
                ['رقم الهوية', $personal['national_id'] ?? '—'],
                ['العنوان', $personal['address'] ?? '—'],
                ['جهة طوارئ', $personal['emergency_contact'] ?? '—'],
                ['هاتف الطوارئ', $personal['emergency_phone'] ?? '—'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$lbl, $val]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div><p class="text-xs font-bold text-gray-500 mb-1"><?php echo e($lbl); ?></p><p class="text-sm font-medium text-gray-900"><?php echo e($val ?: '—'); ?></p></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php elseif($activeTab === 'employment'): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php $__currentLoopData = [
                ['الرقم التوظيفي', $employment['employee_id']],
                ['المنصب', $employment['position'] ?? $roleMeta['label']],
                ['القسم', $employment['department']],
                ['نوع التوظيف', $employmentLabels[$employment['employment_type']] ?? $employment['employment_type']],
                ['تاريخ التعيين', $employment['hire_date']?->format('Y/m/d')],
                ['الراتب', $employment['salary'] ? number_format($employment['salary']) . ' ج.م' : '—'],
                ['الحالة', $statusLabels[$employment['status']] ?? $employment['status']],
                ['المدير المباشر', $employment['reports_to'] ?? '—'],
                ['جدول الدوام', $employment['schedule']],
                ['أيام الراحة', $employment['off_days']],
                ['ساعات العمل', $employment['daily_hours'] . ' ساعة'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$lbl, $val]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div><p class="text-xs font-bold text-gray-500 mb-1"><?php echo e($lbl); ?></p><p class="text-sm font-medium text-gray-900"><?php echo e($val ?: '—'); ?></p></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($contracts->isNotEmpty()): ?>
        <div class="mt-8">
            <h4 class="font-bold text-gray-900 mb-3">العقود المسجّلة</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr>
                        <th class="p-3 text-right">رقم العقد</th><th class="p-3 text-right">العنوان</th><th class="p-3 text-right">الفترة</th><th class="p-3 text-right">الحالة</th>
                    </tr></thead>
                    <tbody>
                        <?php $__currentLoopData = $contracts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="border-t"><td class="p-3 font-mono text-xs"><?php echo e($c->contract_number); ?></td><td class="p-3"><?php echo e($c->title); ?></td>
                        <td class="p-3"><?php echo e($c->start_date->format('Y/m/d')); ?><?php if($c->end_date): ?> — <?php echo e($c->end_date->format('Y/m/d')); ?><?php endif; ?></td>
                        <td class="p-3"><?php echo e($c->statusLabel()); ?></td></tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <?php elseif($activeTab === 'cv'): ?>
        <div class="max-w-xl">
            <?php if($cv): ?>
            <div class="border rounded-2xl p-5 mb-4">
                <p class="font-bold text-gray-900 mb-1"><?php echo e($cv->title); ?></p>
                <p class="text-sm text-gray-500 mb-3"><?php echo e($cv->original_filename); ?> — <?php echo e($cv->created_at->format('Y/m/d')); ?></p>
                <a href="<?php echo e(route('employees.dossier.documents.download', [$employee, $cv])); ?>" class="inline-flex px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">تحميل السيرة الذاتية</a>
            </div>
            <?php else: ?>
            <p class="text-gray-500 mb-4">لم تُرفع سيرة ذاتية بعد.</p>
            <?php endif; ?>
            <?php if($canManageDocuments): ?>
            <form method="POST" action="<?php echo e(route('employees.dossier.cv.store', $employee)); ?>" enctype="multipart/form-data" class="border rounded-2xl p-5 space-y-3">
                <?php echo csrf_field(); ?>
                <p class="font-bold text-gray-900"><?php echo e($cv ? 'استبدال السيرة الذاتية' : 'رفع السيرة الذاتية'); ?></p>
                <input type="file" name="file" required accept=".pdf,.doc,.docx" class="w-full text-sm">
                <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">رفع CV</button>
            </form>
            <?php endif; ?>
        </div>

        <?php elseif($activeTab === 'documents'): ?>
        <div class="flex justify-between items-center mb-4">
            <h4 class="font-bold text-gray-900">مستندات الموظف (<?php echo e($documents->count()); ?>)</h4>
            <?php if($canManageDocuments): ?>
            <button type="button" onclick="document.getElementById('uploadDocModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">رفع مستند</button>
            <?php endif; ?>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr>
                    <th class="p-3 text-right">العنوان</th><th class="p-3 text-right">النوع</th><th class="p-3 text-right">الملف</th><th class="p-3 text-right">التاريخ</th><th class="p-3 text-right">إجراء</th>
                </tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-t hover:bg-gray-50/50">
                        <td class="p-3 font-semibold"><?php echo e($doc->title); ?></td>
                        <td class="p-3"><?php echo e($doc->typeLabel()); ?></td>
                        <td class="p-3 text-gray-600 truncate max-w-[10rem]"><?php echo e($doc->original_filename); ?></td>
                        <td class="p-3 text-gray-500"><?php echo e($doc->created_at->format('Y/m/d')); ?></td>
                        <td class="p-3"><a href="<?php echo e(route('employees.dossier.documents.download', [$employee, $doc])); ?>" class="text-xs font-bold" style="color:<?php echo e($themeColor); ?>">تحميل</a></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="p-8 text-center text-gray-500">لا توجد مستندات</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php elseif($activeTab === 'attendance'): ?>
        <form method="GET" action="<?php echo e($dossierUrl); ?>" class="flex flex-wrap gap-3 mb-5 items-end">
            <input type="hidden" name="tab" value="attendance">
            <?php $__currentLoopData = $listQuery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><input type="hidden" name="<?php echo e($k); ?>" value="<?php echo e($v); ?>"><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <div><label class="text-xs font-bold text-gray-500 block mb-1">من</label><input type="date" name="from" value="<?php echo e($period['start']->format('Y-m-d')); ?>" class="border rounded-xl px-3 py-2 text-sm"></div>
            <div><label class="text-xs font-bold text-gray-500 block mb-1">إلى</label><input type="date" name="to" value="<?php echo e($period['end']->format('Y-m-d')); ?>" class="border rounded-xl px-3 py-2 text-sm"></div>
            <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">عرض</button>
        </form>
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-5">
            <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي', 'value' => $attendance_summary['total'], 'accent' => 'theme', 'compact' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('crm.partials.stat-card', ['label' => 'حضور', 'value' => $attendance_summary['present'], 'accent' => 'green', 'compact' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('crm.partials.stat-card', ['label' => 'تأخير', 'value' => $attendance_summary['late'], 'accent' => 'amber', 'compact' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('crm.partials.stat-card', ['label' => 'غياب', 'value' => $attendance_summary['absent'], 'accent' => 'red', 'compact' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('crm.partials.stat-card', ['label' => 'ساعات', 'value' => $attendance_summary['total_hours'], 'accent' => 'blue', 'compact' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr>
                    <th class="p-3 text-right">التاريخ</th><th class="p-3 text-right">دخول</th><th class="p-3 text-right">خروج</th><th class="p-3 text-right">الساعات</th><th class="p-3 text-right">الحالة</th>
                </tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-t">
                        <td class="p-3"><?php echo e($att->date->format('Y/m/d')); ?></td>
                        <td class="p-3" dir="ltr"><?php echo e($att->check_in?->format('H:i') ?? '—'); ?></td>
                        <td class="p-3" dir="ltr"><?php echo e($att->check_out?->format('H:i') ?? '—'); ?></td>
                        <td class="p-3"><?php echo e($att->total_hours ?? '—'); ?></td>
                        <td class="p-3"><?php echo e($att->status); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="p-8 text-center text-gray-500">لا توجد سجلات في هذه الفترة</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <a href="<?php echo e(route('attendances.index', ['employee_id' => $employee->id])); ?>" class="inline-block mt-4 text-sm font-bold" style="color:<?php echo e($themeColor); ?>">عرض السجل الكامل ←</a>

        <?php elseif($activeTab === 'performance'): ?>
        <?php $comp = $performance['compliance'] ?? null; $kpi = $performance['kpi'] ?? null; ?>
        <?php if(!$employee->user): ?>
        <p class="text-gray-500">لا يوجد حساب مستخدم مرتبط — لا يتوفر تقييم أداء آلي.</p>
        <?php else: ?>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
            <?php if($comp): ?>
            <?php echo $__env->make('crm.partials.stat-card', ['label' => 'التقييم الإجمالي', 'value' => $comp['overall_score'] . '%', 'accent' => ($comp['status']['color'] ?? 'theme') === 'green' ? 'green' : 'amber', 'compact' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('crm.partials.stat-card', ['label' => 'التزام الحضور', 'value' => $comp['attendance_compliance'] . '%', 'accent' => 'purple', 'compact' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('crm.partials.stat-card', ['label' => 'التقارير اليومية', 'value' => ($comp['reports']['submitted'] ?? 0) . '/' . ($comp['reports']['expected'] ?? 0), 'accent' => 'blue', 'compact' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مهام متأخرة', 'value' => $comp['overdue_tasks'] ?? 0, 'accent' => 'red', 'compact' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?>
        </div>
        <?php if($kpi && !empty($kpi['items'])): ?>
        <h4 class="font-bold text-gray-900 mb-3">مؤشرات KPI — <?php echo e($kpi['level']['label'] ?? ''); ?></h4>
        <div class="space-y-2 mb-6">
            <?php $__currentLoopData = $kpi['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex justify-between items-center p-3 rounded-xl bg-gray-50 text-sm">
                <span><?php echo e($item['label'] ?? $item['name'] ?? '—'); ?></span>
                <span class="font-bold"><?php echo e($item['score'] ?? $item['percent'] ?? '—'); ?>%</span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
        <?php if($employee->user->canAccessCrm()): ?>
        <a href="<?php echo e(route('crm.employee-compliance.show', $employee->user)); ?>" class="text-sm font-bold" style="color:<?php echo e($themeColor); ?>">تقرير الالتزام التفصيلي ←</a>
        <?php endif; ?>
        <?php endif; ?>

        <?php elseif($activeTab === 'notes'): ?>
        <?php if($canManageNotes): ?>
        <form method="POST" action="<?php echo e(route('employees.dossier.notes.store', $employee)); ?>" class="border rounded-2xl p-5 mb-6 space-y-3">
            <?php echo csrf_field(); ?>
            <p class="font-bold text-gray-900">إضافة ملاحظة إدارية</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <select name="category" class="border rounded-xl px-3 py-2 text-sm">
                    <?php $__currentLoopData = config('employee_admin_notes.categories', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>"><?php echo e($v); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <input type="text" name="title" placeholder="عنوان (اختياري)" class="border rounded-xl px-3 py-2 text-sm">
            </div>
            <textarea name="body" required rows="3" placeholder="نص الملاحظة..." class="w-full border rounded-xl px-3 py-2 text-sm"></textarea>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_confidential" value="1"> ملاحظة سرية (HR فقط)</label>
            <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">حفظ الملاحظة</button>
        </form>
        <?php endif; ?>
        <div class="space-y-3">
            <?php $__empty_1 = true; $__currentLoopData = $notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="border rounded-2xl p-4 <?php echo e($note->is_confidential ? 'border-amber-200 bg-amber-50/50' : ''); ?>">
                <div class="flex justify-between items-start gap-3 mb-2">
                    <div>
                        <p class="font-bold text-gray-900"><?php echo e($note->title ?: $note->categoryLabel()); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($note->author?->name); ?> — <?php echo e($note->created_at->format('Y/m/d H:i')); ?>

                            <?php if($note->is_confidential): ?><span class="text-amber-700 font-bold"> · سرية</span><?php endif; ?>
                        </p>
                    </div>
                    <?php if($canManageNotes): ?>
                    <form method="POST" action="<?php echo e(route('employees.dossier.notes.destroy', [$employee, $note])); ?>" onsubmit="return confirm('حذف الملاحظة؟')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="text-xs text-red-600 font-bold">حذف</button>
                    </form>
                    <?php endif; ?>
                </div>
                <p class="text-sm text-gray-700 whitespace-pre-wrap"><?php echo e($note->body); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-gray-500 text-center py-8">لا توجد ملاحظات إدارية</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="flex flex-wrap gap-3">
    <a href="<?php echo e(route('employees.show', array_merge(['employee' => $employee], $listQuery))); ?>" class="px-4 py-2 rounded-xl border text-sm font-bold text-gray-600">ملخص الموظف</a>
    <a href="<?php echo e(route('employees.index', $listQuery)); ?>" class="px-4 py-2 rounded-xl border text-sm font-bold text-gray-600">قائمة الموظفين</a>
    <?php if(auth()->user()?->canAccessHr()): ?>
    <a href="<?php echo e(route('hr.documents.index', ['employee_id' => $employee->id])); ?>" class="px-4 py-2 rounded-xl text-sm font-bold text-white" style="background:<?php echo e($themeColor); ?>">أرشيف HR</a>
    <?php endif; ?>
</div>

<?php if($canManageDocuments): ?>
<div id="uploadDocModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/50" onclick="document.getElementById('uploadDocModal').classList.add('hidden')"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl border p-5 space-y-3">
            <p class="font-bold">رفع مستند</p>
            <form method="POST" action="<?php echo e(route('employees.dossier.documents.store', $employee)); ?>" enctype="multipart/form-data" class="space-y-3">
                <?php echo csrf_field(); ?>
                <select name="document_type" required class="w-full border rounded-xl px-3 py-2 text-sm">
                    <?php $__currentLoopData = config('employee_documents.types', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>"><?php echo e($v); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <input type="text" name="title" required placeholder="عنوان المستند" class="w-full border rounded-xl px-3 py-2 text-sm">
                <input type="date" name="expires_at" class="w-full border rounded-xl px-3 py-2 text-sm">
                <input type="file" name="file" required class="w-full text-sm">
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="document.getElementById('uploadDocModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border text-sm font-bold">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">رفع</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\employees\dossier.blade.php ENDPATH**/ ?>