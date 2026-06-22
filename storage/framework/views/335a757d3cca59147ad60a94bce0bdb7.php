<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $query = request()->except('view');
?>
<div class="flex flex-wrap items-center gap-2 mb-4">
    <span class="text-xs text-gray-500 font-tajawal">العرض:</span>
    <a href="<?php echo e(route('crm.pipeline.index', $query)); ?>"
       class="px-3 py-1.5 rounded-lg text-xs font-semibold font-tajawal <?php echo e(($current ?? 'kanban') === 'kanban' ? 'text-white' : 'border border-gray-200 text-gray-700 hover:bg-gray-50'); ?>"
       <?php if(($current ?? 'kanban') === 'kanban'): ?> style="background: <?php echo e($themeColor); ?>;" <?php endif; ?>>Kanban — مسار العملاء</a>
    <a href="<?php echo e(route('crm.pipeline.index', array_merge($query, ['view' => 'list']))); ?>"
       class="px-3 py-1.5 rounded-lg text-xs font-semibold font-tajawal <?php echo e(($current ?? '') === 'list' ? 'text-white' : 'border border-gray-200 text-gray-700 hover:bg-gray-50'); ?>"
       <?php if(($current ?? '') === 'list'): ?> style="background: <?php echo e($themeColor); ?>;" <?php endif; ?>>قائمة العملاء</a>
    <a href="<?php echo e(route('crm.pipeline.index', array_merge($query, ['view' => 'deals']))); ?>"
       class="px-3 py-1.5 rounded-lg text-xs font-semibold font-tajawal <?php echo e(($current ?? '') === 'deals' ? 'text-white' : 'border border-gray-200 text-gray-700 hover:bg-gray-50'); ?>"
       <?php if(($current ?? '') === 'deals'): ?> style="background: <?php echo e($themeColor); ?>;" <?php endif; ?>>صفقات فقط</a>
    <a href="<?php echo e(route('crm.clients.create')); ?>" class="mr-auto px-3 py-1.5 rounded-lg text-xs font-semibold font-tajawal border border-dashed border-gray-300 text-gray-600 hover:bg-gray-50">+ عميل جديد</a>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\pipeline\partials\view-switcher.blade.php ENDPATH**/ ?>