<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $accent = $accent ?? 'theme';
    $accentColors = [
        'theme' => $themeColor,
        'green' => '#16a34a',
        'blue' => '#2563eb',
        'amber' => '#d97706',
        'purple' => '#9333ea',
        'orange' => '#ea580c',
        'red' => '#dc2626',
        'cyan' => '#0891b2',
        'teal' => '#0d9488',
        'yellow' => '#ca8a04',
    ];
    $color = $accentColors[$accent] ?? $themeColor;
?>
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 sm:p-6 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 transform group flex flex-col h-full">
    <div class="flex items-center justify-between gap-3 sm:gap-4 <?php echo e(!empty($footer) ? 'mb-4' : ''); ?>">
        <div class="text-right flex-1 min-w-0">
            <div class="text-xs sm:text-sm text-gray-500 mb-1 font-tajawal"><?php echo e($label); ?></div>
            <div class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 font-tajawal tabular-nums leading-tight"><?php echo e($value); ?></div>
        </div>
        <?php if(!empty($icon)): ?>
        <div class="p-3 sm:p-4 rounded-2xl shadow-lg flex-shrink-0 group-hover:scale-110 transition-transform duration-300"
             style="background: linear-gradient(135deg, <?php echo e($color); ?> 0%, <?php echo e($color); ?>dd 100%);">
            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><?php echo $icon; ?></svg>
        </div>
        <?php endif; ?>
    </div>
    <?php if(!empty($footer)): ?>
    <div class="pt-3 border-t border-gray-100 mt-auto text-xs sm:text-sm font-tajawal">
        <?php echo $footer; ?>

    </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\dashboard\partials\stat-card.blade.php ENDPATH**/ ?>