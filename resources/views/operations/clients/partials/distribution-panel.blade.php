@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@if(session('success'))<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm">{{ session('success') }}</div>@endif
@if(session('error'))<div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">{{ session('error') }}</div>@endif

<div class="grid grid-cols-2 gap-3 mb-6">
    @include('crm.partials.stat-card', [
        'label' => __('operations.clients.pending_distribution'),
        'value' => $stats['unassigned'],
        'accent' => 'amber',
        'href' => route('operations.clients.index', ['view' => 'distribution', 'filter' => 'unassigned']) . '#page-data',
        'linkLabel' => __('operations.actions.view_details'),
    ])
    @include('crm.partials.stat-card', [
        'label' => __('operations.clients.stale_unassigned'),
        'value' => $stats['stale'],
        'accent' => 'red',
        'href' => route('operations.clients.index', ['view' => 'distribution', 'filter' => 'stale']) . '#page-data',
        'linkLabel' => __('operations.actions.view_details'),
    ])
</div>

@if($leadKpis ?? null)
@include('operations.partials.kpi-group', ['group' => $leadKpis, 'link' => route('operations.clients.index', ['view' => 'distribution']) . '#page-data'])
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 font-tajawal">
    <div class="lg:col-span-2 bg-white rounded-2xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex flex-wrap gap-2 items-center justify-between">
            <div class="flex flex-wrap gap-2 items-center">
                <p class="font-bold">{{ ($filter ?? 'unassigned') === 'stale' ? __('operations.clients.stale_list_title') : __('operations.clients.pending_list_title') }}</p>
                <a href="{{ route('operations.clients.index', ['view' => 'distribution', 'filter' => 'unassigned']) }}#page-data"
                   class="text-xs font-bold px-2 py-1 rounded-lg {{ ($filter ?? 'unassigned') !== 'stale' ? 'text-white' : 'border text-gray-600' }}"
                   @if(($filter ?? 'unassigned') !== 'stale') style="background:{{ $themeColor }}" @endif>
                    {{ __('operations.clients.pending_distribution') }}
                </a>
                <a href="{{ route('operations.clients.index', ['view' => 'distribution', 'filter' => 'stale']) }}#page-data"
                   class="text-xs font-bold px-2 py-1 rounded-lg {{ ($filter ?? '') === 'stale' ? 'text-white' : 'border text-gray-600' }}"
                   @if(($filter ?? '') === 'stale') style="background:{{ $themeColor }}" @endif>
                    {{ __('operations.clients.stale_filter') }}
                </a>
            </div>
            @if(($filter ?? 'unassigned') !== 'stale')
            <form method="POST" action="{{ route('operations.leads.auto-distribute') }}">@csrf
                <button type="submit" class="px-4 py-2 rounded-xl text-white text-xs font-bold" style="background:{{ $themeColor }}">{{ __('operations.clients.auto_distribute') }}</button>
            </form>
            @endif
        </div>
        <form method="GET" class="p-4 border-b">
            <input type="hidden" name="view" value="distribution">
            <input type="hidden" name="filter" value="{{ $filter ?? 'unassigned' }}">
            <input type="search" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('operations.actions.search') }}..." class="w-full border rounded-xl px-4 py-2 text-sm">
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="p-3 text-right"><input type="checkbox" id="check-all"></th>
                    <th class="p-3 text-right">{{ __('operations.clients.client') }}</th>
                    <th class="p-3 text-right">{{ __('operations.clients.phone') }}</th>
                    <th class="p-3 text-right">{{ __('operations.clients.source') }}</th>
                    <th class="p-3 text-right">{{ __('operations.clients.assign') }}</th>
                    <th class="p-3 text-right">{{ __('operations.clients.actions') }}</th>
                </tr></thead>
                <tbody>
                @forelse($leads as $lead)
                <tr class="border-t">
                    <td class="p-3"><input type="checkbox" name="client_ids[]" value="{{ $lead->id }}" form="batch-form" class="lead-check"></td>
                    <td class="p-3">
                        <a href="{{ $lead->profileUrl() }}" class="font-semibold hover:underline" style="color:{{ $themeColor }}">{{ $lead->name }}</a>
                    </td>
                    <td class="p-3" dir="ltr">{{ $lead->phone }}</td>
                    <td class="p-3 text-xs">@include('crm.clients.partials.source-badge', ['source' => $lead->lead_source])</td>
                    <td class="p-3">
                        @if(($filter ?? 'unassigned') !== 'stale')
                        <form method="POST" action="{{ route('operations.leads.assign', $lead) }}" class="flex gap-1">@csrf
                            <select name="employee_id" class="border rounded-lg text-xs px-2 py-1" required>
                                @foreach($reps as $rep)
                                <option value="{{ $rep->employee?->id }}">{{ $rep->name }}</option>
                                @endforeach
                            </select>
                            <button class="px-2 py-1 rounded-lg text-white text-xs" style="background:{{ $themeColor }}">{{ __('operations.clients.assign_btn') }}</button>
                        </form>
                        @else
                        <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="p-3">
                        <div class="flex flex-wrap gap-1">
                            @can('viewFullDetails', $lead)
                            <a href="{{ route('crm.clients.show', $lead) }}" class="px-2 py-1 rounded-lg text-xs font-bold border hover:bg-gray-50" style="color:{{ $themeColor }};border-color:{{ $themeColor }}40">{{ __('operations.clients.full_profile') }}</a>
                            @else
                            <a href="{{ $lead->profileUrl() }}" class="px-2 py-1 rounded-lg text-xs font-bold border hover:bg-gray-50" style="color:{{ $themeColor }};border-color:{{ $themeColor }}40">{{ __('operations.clients.pipeline') }}</a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-8 text-center text-gray-500">{{ __('operations.clients.distribution_empty') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $leads->links() }}</div>
    </div>
    <div class="space-y-4">
        <div class="bg-white rounded-2xl border p-5">
            <p class="font-bold mb-3">{{ __('operations.clients.rep_loads') }}</p>
            <ul class="space-y-2 text-sm">
                @foreach($repLoads as $row)
                <li class="flex justify-between gap-2 p-2 rounded-lg bg-gray-50">
                    <span>{{ $row['employee']->user?->name ?? ($row['employee']->first_name . ' ' . $row['employee']->last_name) }}</span>
                    <span class="font-bold" style="color:{{ $themeColor }}">{{ $row['load'] }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        <form id="batch-form" method="POST" action="{{ route('operations.leads.distribute-batch') }}" class="bg-white rounded-2xl border p-5">@csrf
            <p class="font-bold mb-2">{{ __('operations.clients.batch_distribute') }}</p>
            <select name="employee_id" class="w-full border rounded-xl px-3 py-2 text-sm mb-3">
                <option value="">{{ __('operations.clients.auto_least_load') }}</option>
                @foreach($reps as $rep)
                <option value="{{ $rep->employee?->id }}">{{ $rep->name }}</option>
                @endforeach
            </select>
            <button class="w-full py-2.5 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">{{ __('operations.clients.batch_distribute') }}</button>
        </form>
    </div>
</div>
<script>
document.getElementById('check-all')?.addEventListener('change', function () {
    document.querySelectorAll('.lead-check').forEach(cb => cb.checked = this.checked);
});
</script>
