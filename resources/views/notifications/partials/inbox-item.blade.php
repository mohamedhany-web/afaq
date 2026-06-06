@php
    $meta = $typeMeta($notification->type);
    $notifUrl = $notification->data['url'] ?? route('notifications.index');
    $digestCount = (int) ($notification->data['count'] ?? 0);
@endphp
<article class="px-4 sm:px-5 py-3 sm:py-3.5 transition-colors hover:bg-gray-50/80 {{ !$notification->is_read ? 'border-r-4' : '' }}"
         @if(!$notification->is_read) style="background: {{ $themeColor }}06; border-color: {{ $themeColor }};" @endif>
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 relative">
            <div class="h-10 w-10 rounded-xl flex items-center justify-center shadow-md"
                 style="background: linear-gradient(135deg, {{ $meta['accent'] }} 0%, {{ $meta['accent'] }}cc 100%);">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            @if(!$notification->is_read)
                <span class="absolute -top-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-white" style="background: {{ $themeColor }};"></span>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mb-0.5">
                <a href="{{ $notifUrl }}" class="text-sm font-bold text-gray-900 font-tajawal hover:underline truncate max-w-full">
                    {{ $notification->title }}
                </a>
                @if($digestCount > 1)
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold text-white font-tajawal tabular-nums"
                          style="background: {{ $meta['accent'] }};">{{ $digestCount }}</span>
                @endif
                <span class="text-[10px] font-bold font-tajawal px-1.5 py-0.5 rounded text-white" style="background: {{ $meta['accent'] }}80;">{{ $meta['label'] }}</span>
                <time class="text-[10px] text-gray-400 font-tajawal mr-auto whitespace-nowrap">{{ $notification->created_at->format('H:i') }}</time>
            </div>
            <p class="text-xs text-gray-600 font-tajawal line-clamp-1 leading-relaxed">{{ $notification->message }}</p>
            <div class="flex flex-wrap gap-1.5 mt-2">
                <a href="{{ $notifUrl }}" class="px-2.5 py-1 rounded-lg text-[10px] font-semibold font-tajawal border border-gray-200 text-gray-700 hover:bg-gray-50">فتح</a>
                @if(!$notification->is_read)
                    <button type="button" onclick="markAsRead({{ $notification->id }})"
                            class="px-2.5 py-1 rounded-lg text-[10px] font-semibold font-tajawal text-white"
                            style="background: {{ $themeColor }};">مقروء</button>
                @endif
            </div>
        </div>
    </div>
</article>
