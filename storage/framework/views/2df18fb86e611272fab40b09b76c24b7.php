<?php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $linkClass = $linkClass ?? 'font-semibold hover:underline';
?>

<?php if(($type ?? '') === 'client' && ($entity ?? null)): ?>
    <a href="<?php echo e($entity->profileUrl()); ?>" class="<?php echo e($linkClass); ?>" style="color: <?php echo e($themeColor); ?>;"><?php echo e($entity->name); ?></a>
<?php elseif(($type ?? '') === 'project' && ($entity ?? null)): ?>
    <a href="<?php echo e(route('crm.projects.show', $entity)); ?>" class="<?php echo e($linkClass); ?>" style="color: <?php echo e($themeColor); ?>;"><?php echo e($entity->name); ?></a>
<?php elseif(($type ?? '') === 'rep' && ($entity ?? null)): ?>
    <a href="<?php echo e(route('crm.team-members.show', $entity)); ?>" class="<?php echo e($linkClass); ?>" style="color: <?php echo e($themeColor); ?>;"><?php echo e($entity->name); ?></a>
<?php else: ?>
    <span class="text-gray-500"><?php echo e($fallback ?? '—'); ?></span>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\partials\entity-link.blade.php ENDPATH**/ ?>