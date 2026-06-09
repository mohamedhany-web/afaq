@include('accounting.partials.context')
@include('crm.partials.page-header', [
    'title' => $title,
    'subtitle' => $subtitle ?? '',
    'icon' => $icon ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-7M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" />',
    'actionUrl' => route('accounting.reports.index'),
    'actionLabel' => 'كل التقارير',
])
@include('accounting.partials.nav')
