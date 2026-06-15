@include('crm.partials.filter-bar', [
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
])
