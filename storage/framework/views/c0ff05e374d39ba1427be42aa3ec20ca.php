<?php
    $comment = $client->listLatestComment();
?>
<?php if($comment): ?>
<p class="text-xs text-gray-600 font-tajawal line-clamp-2 max-w-[220px]" title="<?php echo e($comment); ?>"><?php echo e(Str::limit($comment, 90)); ?></p>
<?php else: ?>
<span class="text-xs text-gray-300">—</span>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/clients/partials/list-comment.blade.php ENDPATH**/ ?>