<?php
    use App\Models\Project;
    $type = Project::normalizeOwnershipType($project->ownership_type ?? 'developer') ?? 'developer';
    $contract = $type === 'developer' ? $project->realEstateDeveloper?->activeContract : null;
    $details = $project->ownership_details ?? [];
    if ($contract) {
        $details = array_filter([
            'contract_ref' => $contract->contract_ref,
            'commission_percent' => $contract->commission_percent,
            'exclusivity' => $contract->exclusivity,
            'exclusivity_until' => optional($contract->exclusivity_until)->format('Y-m-d'),
            'contact_person' => $contract->contact_person,
            'contact_phone' => $contract->contact_phone,
            'listing_terms' => $contract->listing_terms,
            'developer_notes' => $contract->notes,
        ], fn ($v) => $v !== null && $v !== '');
    }
    $labels = [
        'contact_name' => 'اسم الجهة',
        'contact_phone' => 'رقم التواصل',
        'commission_percent' => 'نسبة العمولة %',
        'share_percent' => 'نسبة الحصة %',
        'fee_percent' => 'نسبة الإدارة %',
        'contract_ref' => 'مرجع العقد',
        'notes' => 'ملاحظات',
        'internal_entity' => 'الجهة الداخلية',
        'acquisition_date' => 'تاريخ الاستحواذ',
        'investment_amount' => 'قيمة الاستثمار',
        'management_notes' => 'ملاحظات إدارية',
        'partner_name' => 'اسم الشريك',
        'partner_phone' => 'هاتف الشريك',
        'partner_contact' => 'مسؤول التواصل',
        'our_share_percent' => 'حصتنا %',
        'partner_share_percent' => 'حصة الشريك %',
        'partnership_start' => 'بداية الشراكة',
        'partnership_notes' => 'ملاحظات الشراكة',
        'exclusivity' => 'حصرية',
        'exclusivity_until' => 'انتهاء الحصرية',
        'contact_person' => 'مسؤول المطور',
        'listing_terms' => 'شروط العرض',
        'developer_notes' => 'ملاحظات المطور',
    ];
    $fieldLabel = $fieldLabel ?? 'text-xs font-bold text-gray-500 mb-1 font-tajawal';
    $fieldValue = $fieldValue ?? 'text-sm font-medium text-gray-900 font-tajawal';
?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900 flex flex-wrap items-center justify-between gap-2"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        <span>نوع الملكية</span>
        <?php echo $__env->make('projects.partials.ownership-badge', ['type' => $type], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
    <div class="p-5 sm:p-6 space-y-4">
        <?php if($type === 'developer'): ?>
        <div>
            <dt class="<?php echo e($fieldLabel); ?>">المطور العقاري</dt>
            <dd class="<?php echo e($fieldValue); ?>"><?php echo e($project->displayDeveloperName()); ?></dd>
        </div>
        <?php endif; ?>

        <?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($value === null || $value === ''): ?> <?php continue; ?> <?php endif; ?>
            <div>
                <dt class="<?php echo e($fieldLabel); ?>"><?php echo e($labels[$key] ?? $key); ?></dt>
                <dd class="<?php echo e($fieldValue); ?>">
                    <?php if($key === 'exclusivity'): ?>
                        <?php echo e($value ? 'نعم' : 'لا'); ?>

                    <?php elseif(in_array($key, ['investment_amount'], true)): ?>
                        <?php echo e(\App\Helpers\SettingsHelper::formatMoney($value)); ?>

                    <?php else: ?>
                        <?php echo e($value); ?>

                    <?php endif; ?>
                </dd>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if(empty($details) && $type !== 'developer'): ?>
        <p class="text-sm text-gray-400 font-tajawal">لا توجد بيانات إضافية — يمكن إضافتها من تعديل المشروع.</p>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\projects\partials\ownership-summary.blade.php ENDPATH**/ ?>