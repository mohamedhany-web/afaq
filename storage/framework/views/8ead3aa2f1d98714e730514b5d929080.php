<?php if(session('success')): ?>
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-xl font-tajawal"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl font-tajawal"><?php echo e(session('error')); ?></div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\daily-reports\partials\alerts.blade.php ENDPATH**/ ?>