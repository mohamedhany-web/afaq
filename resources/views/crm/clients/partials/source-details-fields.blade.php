@php
    $details = old('lead_source_details', isset($client) ? ($client->lead_source_details ?? []) : []);
    if (! is_array($details)) {
        $details = [];
    }
    $detailFields = config('client_lead_sources.detail_fields', []);
    $campaigns = $marketingCampaigns ?? collect();
@endphp

<div id="lead-source-details-wrap" class="sm:col-span-2 lg:col-span-4 space-y-3">
    @foreach($detailFields as $sourceKey => $fields)
    <div class="lead-source-detail-group hidden rounded-xl border border-gray-200 bg-gray-50/80 p-4 grid grid-cols-1 sm:grid-cols-2 gap-4"
         data-source="{{ $sourceKey }}">
        @foreach($fields as $fieldKey => $fieldLabel)
        <div class="detail-field" data-field="{{ $fieldKey }}">
            <label class="{{ $label }}">{{ $fieldLabel }} <span class="text-red-600">*</span></label>
            <input type="text"
                   name="lead_source_details[{{ $fieldKey }}]"
                   value="{{ old("lead_source_details.{$fieldKey}", $details[$fieldKey] ?? '') }}"
                   class="{{ $input }} lead-source-detail-input"
                   dir="{{ str_contains($fieldKey, 'number') ? 'ltr' : 'auto' }}"
                   maxlength="{{ str_contains($fieldKey, 'number') ? 50 : 255 }}">
            @error("lead_source_details.{$fieldKey}")<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
        </div>
        @endforeach
        @if($sourceKey === 'marketing' && $campaigns->isNotEmpty())
        <div class="sm:col-span-2">
            <label class="{{ $label }}">حملة تسويقية مسجّلة (اختياري)</label>
            <select name="marketing_campaign_id" class="{{ $input }}">
                <option value="">— أو اكتب اسم الحملة أعلاه —</option>
                @foreach($campaigns as $campaign)
                <option value="{{ $campaign->id }}" @selected(old('marketing_campaign_id', $client->marketing_campaign_id ?? '') == $campaign->id)>
                    {{ $campaign->name }}
                </option>
                @endforeach
            </select>
            @error('marketing_campaign_id')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
        </div>
        @endif
    </div>
    @endforeach
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sourceSelect = document.querySelector('select[name="lead_source"]');
    const groups = document.querySelectorAll('.lead-source-detail-group');
    if (!sourceSelect || !groups.length) return;

    function syncSourceDetails() {
        const source = sourceSelect.value;
        groups.forEach(group => {
            const show = group.dataset.source === source;
            group.classList.toggle('hidden', !show);
            group.querySelectorAll('.lead-source-detail-input').forEach(inp => {
                inp.required = show;
                inp.disabled = !show;
            });
        });
    }

    sourceSelect.addEventListener('change', syncSourceDetails);
    syncSourceDetails();
});
</script>
@endpush
