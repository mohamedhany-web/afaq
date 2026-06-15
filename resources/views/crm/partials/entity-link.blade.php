@php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $linkClass = $linkClass ?? 'font-semibold hover:underline';
@endphp

@if(($type ?? '') === 'client' && ($entity ?? null))
    <a href="{{ $entity->profileUrl() }}" class="{{ $linkClass }}" style="color: {{ $themeColor }};">{{ $entity->name }}</a>
@elseif(($type ?? '') === 'project' && ($entity ?? null))
    <a href="{{ route('crm.projects.show', $entity) }}" class="{{ $linkClass }}" style="color: {{ $themeColor }};">{{ $entity->name }}</a>
@elseif(($type ?? '') === 'rep' && ($entity ?? null))
    <a href="{{ route('crm.team-members.show', $entity) }}" class="{{ $linkClass }}" style="color: {{ $themeColor }};">{{ $entity->name }}</a>
@else
    <span class="text-gray-500">{{ $fallback ?? '—' }}</span>
@endif
