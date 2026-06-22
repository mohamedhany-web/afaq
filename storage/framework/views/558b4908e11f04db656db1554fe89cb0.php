<?php echo $__env->make('crm.partials.filter-bar', [
    'filterKeys' => array_values(array_filter([
        ($teamMembers ?? collect())->isNotEmpty() ? 'sales_rep' : null,
        'date_from',
        'date_to',
        ($showStatusFilter ?? false) ? 'status' : null,
    ])),
    'advancedKeys' => [],
    'showSalesRepFilter' => ($teamMembers ?? collect())->isNotEmpty(),
    'salesReps' => $teamMembers ?? collect(),
    'hasActive' => request()->hasAny(['sales_rep', 'user_id', 'status', 'date_from', 'date_to']),
    'clearUrl' => route('crm.daily-reports.index'),
    'statusOptions' => ['draft' => 'مسودة', 'submitted' => 'مرفوع'],
    'statusLabel' => 'حالة التقرير',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\daily-reports\partials\filters.blade.php ENDPATH**/ ?>