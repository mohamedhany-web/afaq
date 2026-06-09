@foreach($accounts as $account)
<div class="flex items-center justify-between py-2.5 border-b border-gray-100">
    <span class="text-sm font-bold text-gray-900">{{ $account->code ?? '' }} — {{ $account->name ?? $account->account_name ?? 'غير محدد' }}</span>
    <span class="text-sm font-bold text-gray-900 tabular-nums">{{ $money($account->total_balance ?? $account->balance ?? 0) }}</span>
</div>
@if(!empty($account->children) && $account->children->count() > 0)
    @foreach($account->children as $child)
    <div class="flex items-center justify-between py-2 pr-5 border-b border-gray-50">
        <span class="text-xs text-gray-600">{{ $child->code ?? '' }} — {{ $child->name ?? 'غير محدد' }}</span>
        <span class="text-xs font-medium text-gray-700 tabular-nums">{{ $money($child->computed_balance ?? $child->period_balance ?? 0) }}</span>
    </div>
    @endforeach
@endif
@endforeach
