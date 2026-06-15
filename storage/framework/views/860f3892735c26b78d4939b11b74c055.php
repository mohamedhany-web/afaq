<?php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $group = $group ?? null;
    $itemLinks = [
        'lead_response_time' => route('operations.leads.index') . '#page-data',
        'lead_distribution_time' => route('operations.leads.index') . '#page-data',
        'lead_leakage_rate' => route('operations.leads.index', ['filter' => 'stale']) . '#page-data',
        'contact_rate' => route('operations.crm.index') . '#page-data',
        'crm_compliance_rate' => route('operations.crm.index') . '#page-data',
        'data_accuracy_rate' => route('operations.crm.index') . '#page-data',
        'duplicate_records_rate' => route('operations.crm.index') . '#page-data',
        'pipeline_update_rate' => route('crm.pipeline.index'),
        'lead_to_meeting_conversion' => route('crm.pipeline.index'),
        'meeting_to_reservation_conversion' => route('crm.pipeline.index'),
        'reservation_to_contract_conversion' => route('crm.pipeline.index'),
        'sales_cycle_duration' => route('operations.crm.index') . '#page-data',
        'revenue_growth_support' => route('operations.crm.index') . '#page-data',
        'lost_opportunity_recovery' => route('operations.crm.index') . '#page-data',
        'inventory_accuracy' => route('operations.inventory.index') . '#page-data',
        'unit_availability_accuracy' => route('operations.inventory.index', ['status' => 'available']) . '#page-data',
        'double_booking_incidents' => route('operations.inventory.index') . '#page-data',
        'active_inventory_units' => route('operations.inventory.index', ['status' => 'available']) . '#page-data',
        'sales_activity_compliance' => route('operations.team.index') . '#page-data',
        'follow_up_compliance' => route('operations.team.index') . '#page-data',
        'employee_productivity_score' => route('operations.team.index') . '#page-data',
        'report_accuracy' => route('operations.reports.index') . '#page-data',
        'report_delivery_time' => route('operations.reports.index') . '#page-data',
        'reports_submitted' => route('operations.reports.index', ['status' => 'submitted']) . '#page-data',
    ];
?>
<?php if($group): ?>
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden font-tajawal">
    <div class="px-5 py-4 border-b flex items-center justify-between gap-3" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, transparent 100%);">
        <div>
            <p class="font-bold text-gray-900"><?php echo e($group['label']); ?></p>
            <p class="text-xs text-gray-500">النتيجة الإجمالية: <?php echo e(number_format($group['score'], 1)); ?>%</p>
        </div>
        <?php if(!empty($link)): ?>
        <a href="<?php echo e($link); ?>" class="text-xs font-bold px-3 py-1.5 rounded-lg border hover:bg-gray-50" style="color:<?php echo e($themeColor); ?>">التفاصيل</a>
        <?php endif; ?>
    </div>
    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
        <?php $__currentLoopData = $group['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $statusColors = ['excellent' => 'text-green-700 bg-green-50', 'good' => 'text-blue-700 bg-blue-50', 'warning' => 'text-amber-700 bg-amber-50', 'critical' => 'text-red-700 bg-red-50'];
            $badge = $statusColors[$item['status']] ?? 'text-gray-700 bg-gray-50';
            $itemHref = $item['href'] ?? ($itemLinks[$item['slug'] ?? ''] ?? null);
        ?>
        <?php if($itemHref): ?>
        <a href="<?php echo e($itemHref); ?>" class="block p-3 rounded-xl bg-gray-50 border border-gray-100 hover:border-gray-200 hover:shadow-sm transition-all group">
            <p class="text-xs text-gray-500 mb-1"><?php echo e($item['label']); ?></p>
            <div class="flex items-end justify-between gap-2">
                <p class="text-lg font-extrabold text-gray-900"><?php echo e(number_format($item['value'], 1)); ?> <span class="text-xs font-normal text-gray-500"><?php echo e($item['unit']); ?></span></p>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?php echo e($badge); ?>"><?php echo e(number_format($item['achievement'], 0)); ?>%</span>
            </div>
            <p class="text-[10px] text-gray-400 mt-1">المستهدف: <?php echo e(number_format($item['target'], 1)); ?> <?php echo e($item['unit']); ?></p>
            <span class="inline-flex items-center gap-1 text-[10px] font-bold mt-2 opacity-70 group-hover:opacity-100" style="color:<?php echo e($themeColor); ?>">عرض التفاصيل ←</span>
        </a>
        <?php else: ?>
        <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
            <p class="text-xs text-gray-500 mb-1"><?php echo e($item['label']); ?></p>
            <div class="flex items-end justify-between gap-2">
                <p class="text-lg font-extrabold text-gray-900"><?php echo e(number_format($item['value'], 1)); ?> <span class="text-xs font-normal text-gray-500"><?php echo e($item['unit']); ?></span></p>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?php echo e($badge); ?>"><?php echo e(number_format($item['achievement'], 0)); ?>%</span>
            </div>
            <p class="text-[10px] text-gray-400 mt-1">المستهدف: <?php echo e(number_format($item['target'], 1)); ?> <?php echo e($item['unit']); ?></p>
        </div>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/operations/partials/kpi-group.blade.php ENDPATH**/ ?>