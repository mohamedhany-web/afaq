<?php
    $meta = $typeMeta($notification->type);
    $notifUrl = $notification->data['url'] ?? route('notifications.index');
    $digestCount = (int) ($notification->data['count'] ?? 0);
?>
<article data-notification-id="<?php echo e($notification->id); ?>"
         class="px-3.5 py-3 transition-colors hover:bg-gray-50/90 <?php echo e(!$notification->is_read ? 'border-r-4' : ''); ?>"
         <?php if(!$notification->is_read): ?> style="background: <?php echo e($themeColor); ?>06; border-color: <?php echo e($themeColor); ?>;" <?php endif; ?>>
    <div class="flex items-start gap-2.5">
        <div class="flex-shrink-0 relative">
            <div class="h-9 w-9 rounded-xl flex items-center justify-center shadow-md"
                 style="background: linear-gradient(135deg, <?php echo e($meta['accent']); ?> 0%, <?php echo e($meta['accent']); ?>cc 100%);">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <?php if(!$notification->is_read): ?>
            <span class="absolute -top-0.5 -right-0.5 h-2.5 w-2.5 rounded-full border-2 border-white" style="background: <?php echo e($themeColor); ?>;"></span>
            <?php endif; ?>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-x-1.5 gap-y-0.5 mb-0.5">
                <a href="<?php echo e($notifUrl); ?>" class="text-xs font-bold text-gray-900 hover:underline truncate max-w-[11rem]">
                    <?php echo e($notification->title); ?>

                </a>
                <?php if($digestCount > 1): ?>
                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold text-white tabular-nums" style="background: <?php echo e($meta['accent']); ?>;"><?php echo e($digestCount); ?></span>
                <?php endif; ?>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded text-white" style="background: <?php echo e($meta['accent']); ?>99;"><?php echo e($meta['label']); ?></span>
                <time class="text-[9px] text-gray-400 mr-auto whitespace-nowrap"><?php echo e($notification->created_at->diffForHumans()); ?></time>
            </div>
            <p class="text-[11px] text-gray-600 leading-relaxed line-clamp-2"><?php echo e($notification->message); ?></p>
            <div class="flex flex-wrap gap-1.5 mt-1.5">
                <a href="<?php echo e($notifUrl); ?>" class="px-2 py-0.5 rounded-lg text-[10px] font-semibold border border-gray-200 text-gray-700 hover:bg-gray-50">فتح</a>
                <?php if(!$notification->is_read): ?>
                <button type="button" onclick="markNotificationRead(<?php echo e($notification->id); ?>, this)"
                        class="px-2 py-0.5 rounded-lg text-[10px] font-semibold text-white"
                        style="background: <?php echo e($themeColor); ?>;">مقروء</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</article>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/notifications/partials/dropdown-item.blade.php ENDPATH**/ ?>