@php
    $clientsRoutePrefix = $clientsRoutePrefix ?? 'operations.clients';
    $cr = fn (string $action, mixed $params = []) => route($clientsRoutePrefix . '.' . $action, $params);
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
@endphp

@if(session('success'))
<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal">{{ session('error') }}</div>
@endif

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 ui-compact-hidden">
    @include('crm.partials.stat-card', ['label' => 'إجمالي العملاء', 'value' => $stats['total'], 'accent' => 'theme', 'href' => $cr('index', ['view' => 'data']) . '#page-data', 'linkLabel' => 'عرض القائمة'])
    @include('crm.partials.stat-card', ['label' => 'عملاء محتملون', 'value' => $stats['prospect'], 'accent' => 'blue', 'href' => $cr('index', ['view' => 'data', 'status' => 'prospect']) . '#page-data', 'linkLabel' => 'عرض المحتملين'])
    @include('crm.partials.stat-card', ['label' => 'عملاء نشطون', 'value' => $stats['active'], 'accent' => 'green', 'href' => $cr('index', ['view' => 'data', 'status' => 'active']) . '#page-data', 'linkLabel' => 'عرض النشطين'])
    @include('crm.partials.stat-card', ['label' => 'لديهم صفقات', 'value' => $stats['with_deals'], 'accent' => 'amber', 'href' => $cr('index', ['view' => 'data', 'has_deals' => '1']) . '#page-data', 'linkLabel' => 'عرض الصفقات'])
</div>

<div class="flex flex-wrap gap-2 mb-4 font-tajawal ui-compact-hidden">
    @foreach($bucketLabels as $key => $label)
    <a href="{{ $cr('index', array_filter(['view' => 'data', 'bucket' => $key, 'search' => request('search'), 'sales_rep' => request('sales_rep'), 'created_by' => request('created_by'), 'mine' => request('mine')])) }}#page-data"
       class="text-xs font-bold px-3 py-2 rounded-xl border transition-colors {{ $bucket === $key ? 'text-white border-transparent' : 'text-gray-600 bg-white hover:bg-gray-50' }}"
       @if($bucket === $key) style="background:{{ $themeColor }}" @endif>
        {{ $label }}
        <span class="opacity-80">({{ number_format($bucketCounts[$key] ?? 0) }})</span>
    </a>
    @endforeach
</div>

@include('crm.partials.filter-bar', [
    'action' => $cr('index', ['view' => 'data', 'bucket' => $bucket ?? null]),
    'clientsExportRoute' => $cr('export', request()->query()),
    'clientsRoutePrefix' => $clientsRoutePrefix,
    'preserve' => array_filter([
        'view' => 'data',
        'bucket' => $bucket ?? null,
        'sales_rep' => request('sales_rep'),
        'created_by' => request('created_by'),
        'mine' => request('mine'),
    ]),
])

@if(!empty($selectedSalesRep))
<div class="mb-4 p-4 rounded-xl border-2 font-tajawal flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
     style="border-color: {{ $themeColor }}40; background: {{ $themeColor }}08;">
    <div>
        <p class="text-xs font-bold text-gray-500">عرض عملاء السيلز</p>
        <p class="text-lg font-extrabold text-gray-900" style="color: {{ $themeColor }};">{{ $selectedSalesRep->name }}</p>
        <p class="text-xs text-gray-600 mt-1">{{ $clients->total() }} عميل في هذه القائمة</p>
    </div>
    <a href="{{ $cr('export', request()->query()) }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm"
       style="background: {{ $themeColor }};">
        استخراج قائمة {{ $selectedSalesRep->name }}
    </a>
</div>
@endif

@include('crm.clients.partials.bulk-actions', [
    'assignableReps' => $assignableReps ?? collect(),
    'clientsRoutePrefix' => $clientsRoutePrefix,
])

<div id="page-data" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full font-tajawal">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        <h2 class="font-bold text-gray-900">{{ $bucketLabels[$bucket] ?? 'العملاء' }}</h2>
        <span class="text-xs px-3 py-1 rounded-full font-medium" style="background: {{ $themeColor }}15; color: {{ $themeColor }};">{{ $clients->total() }} عميل</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-200 bg-gray-50/50">
                <tr class="text-gray-600">
                    <th class="p-4 w-10">
                        @if(auth()->user()->can('bulkDelete', \App\Models\Client::class) || auth()->user()->can('bulkUpdate', \App\Models\Client::class))
                        <input type="checkbox" id="client-bulk-check-all" class="rounded border-gray-300">
                        @endif
                    </th>
                    <th class="text-right p-4 font-bold whitespace-nowrap">العميل</th>
                    <th class="text-right p-4 font-bold whitespace-nowrap">التواصل</th>
                    <th class="text-right p-4 font-bold whitespace-nowrap">التصنيف</th>
                    <th class="text-right p-4 font-bold whitespace-nowrap">المصدر</th>
                    <th class="text-right p-4 font-bold whitespace-nowrap">الحالة</th>
                    <th class="text-right p-4 font-bold whitespace-nowrap">المرحلة</th>
                    <th class="text-right p-4 font-bold whitespace-nowrap min-w-[180px]">Comment</th>
                    <th class="text-right p-4 font-bold whitespace-nowrap min-w-[160px]">Next Action</th>
                    <th class="text-right p-4 font-bold whitespace-nowrap">الصفقات</th>
                    <th class="text-right p-4 font-bold whitespace-nowrap">السيلز</th>
                    <th class="text-right p-4 font-bold whitespace-nowrap">إجراءات</th>
                </tr>
            </thead>
            <tbody>
            @forelse($clients as $client)
                <tr class="border-t border-gray-100 hover:bg-gray-50/80">
                    <td class="p-4 align-top">
                        @if(auth()->user()->can('bulkDelete', \App\Models\Client::class) || auth()->user()->can('bulkUpdate', \App\Models\Client::class))
                        <input type="checkbox" class="client-bulk-check rounded border-gray-300" value="{{ $client->id }}"
                               data-name="{{ $client->name }}" data-phone="{{ $client->phone }}">
                        @endif
                    </td>
                    <td class="p-4">
                        <a href="{{ route('crm.clients.show', $client) }}" class="font-semibold text-gray-900 hover:underline">{{ $client->name }}</a>
                        @if($client->company_name)
                        <div class="text-xs text-gray-500 mt-0.5">{{ $client->company_name }}</div>
                        @endif
                    </td>
                    <td class="p-4">
                        <div class="text-gray-900" dir="ltr">{{ $client->phone }}</div>
                        @if($client->email)<div class="text-xs text-gray-500 mt-0.5" dir="ltr">{{ $client->email }}</div>@endif
                    </td>
                    <td class="p-4 whitespace-nowrap">@include('crm.clients.partials.type-badge', ['type' => $client->client_type])</td>
                    <td class="p-4 whitespace-nowrap">@include('crm.clients.partials.source-badge', ['source' => $client->lead_source])</td>
                    <td class="p-4">@include('crm.clients.partials.status-badge', ['status' => $client->status])</td>
                    <td class="p-4">@include('crm.clients.partials.lead-stage-badge', ['stage' => $client->lead_stage])</td>
                    <td class="p-4 align-top">@include('crm.clients.partials.list-comment', ['client' => $client])</td>
                    <td class="p-4 align-top">@include('crm.clients.partials.list-next-action', ['client' => $client])</td>
                    <td class="p-4">
                        <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold" style="background: {{ $themeColor }}10; color: {{ $themeColor }};">
                            {{ $client->sales->count() }} صفقة
                        </span>
                    </td>
                    <td class="p-4 whitespace-nowrap">@include('crm.clients.partials.sales-rep-badge', compact('client', 'themeColor') + ['clientsRoutePrefix' => $clientsRoutePrefix])</td>
                    <td class="p-4">
                        <a href="{{ route('crm.clients.show', $client) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold hover:opacity-80"
                           style="background: {{ $themeColor }}15; color: {{ $themeColor }};">عرض</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="p-12 text-center text-gray-500">
                        لا يوجد عملاء مطابقون
                        <div class="mt-4">
                            <a href="{{ $cr('create') }}" class="inline-flex px-5 py-2.5 rounded-xl text-white text-sm font-semibold"
                               style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">إضافة عميل</a>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($clients->hasPages())
    <div class="p-4 border-t">{{ $clients->links() }}</div>
    @endif
</div>
