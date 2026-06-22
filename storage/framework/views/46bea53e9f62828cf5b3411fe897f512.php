<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
?>
<div class="mb-6 sm:mb-8">
    <div class="rounded-2xl p-5 sm:p-6 lg:p-8 shadow-xl border overflow-hidden relative"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>15 0%, <?php echo e($themeColor); ?>05 50%, <?php echo e($themeColor); ?>10 100%); border-color: <?php echo e($themeColor); ?>30;">
        <div class="absolute top-0 left-0 w-full h-full opacity-5 overflow-hidden pointer-events-none">
            <div class="absolute top-10 right-10 w-64 h-64 rounded-full" style="background: <?php echo e($themeColor); ?>;"></div>
            <div class="absolute bottom-10 left-10 w-48 h-48 rounded-full" style="background: <?php echo e($themeColor); ?>;"></div>
        </div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <?php if(!empty($icon)): ?>
                <div class="h-14 w-14 rounded-2xl flex items-center justify-center shadow-xl flex-shrink-0"
                     style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><?php echo $icon; ?></svg>
                </div>
                <?php endif; ?>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 font-tajawal"><?php echo e($title); ?></h1>
                    <?php if(!empty($subtitle)): ?>
                    <p class="text-sm text-gray-600 mt-1 font-tajawal"><?php echo e($subtitle); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php if(!empty($actionUrl) || !empty($secondaryUrl)): ?>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <?php if(!empty($secondaryUrl)): ?>
                <a href="<?php echo e($secondaryUrl); ?>" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold border-2 hover:bg-gray-50 transition-all"
                   style="border-color: <?php echo e($themeColor); ?>40; color: <?php echo e($themeColor); ?>;">
                    <?php if(!empty($secondaryIcon)): ?>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><?php echo $secondaryIcon; ?></svg>
                    <?php endif; ?>
                    <?php echo e($secondaryLabel ?? 'عرض'); ?>

                </a>
                <?php endif; ?>
                <?php if(!empty($actionUrl)): ?>
                <a href="<?php echo e($actionUrl); ?>" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold shadow-md hover:shadow-lg transition-all"
                   style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
                    <?php if(!empty($actionIcon)): ?>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><?php echo $actionIcon; ?></svg>
                    <?php endif; ?>
                    <?php echo e($actionLabel ?? 'إضافة'); ?>

                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\partials\page-header.blade.php ENDPATH**/ ?>