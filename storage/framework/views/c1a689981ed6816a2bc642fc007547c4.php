<?php
    $registeredAt = $client->created_at;
?>
<div class="rounded-xl border border-gray-100 bg-gray-50/80 p-4 space-y-3 font-tajawal">
    <p class="text-xs font-bold text-gray-500">بيانات التسجيل على النظام</p>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
        <div>
            <span class="block text-[11px] font-bold text-gray-400 mb-0.5">تاريخ الإضافة</span>
            <span class="font-semibold text-gray-900"><?php echo e($registeredAt->format('Y/m/d')); ?></span>
        </div>
        <div>
            <span class="block text-[11px] font-bold text-gray-400 mb-0.5">وقت الإضافة</span>
            <span class="font-semibold text-gray-900" dir="ltr"><?php echo e($registeredAt->format('h:i A')); ?></span>
        </div>
        <div>
            <span class="block text-[11px] font-bold text-gray-400 mb-0.5">أضافه إلى النظام</span>
            <div><?php echo $__env->make('crm.clients.partials.created-by', ['client' => $client, 'class' => 'text-gray-700 text-sm'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/clients/partials/registration-meta.blade.php ENDPATH**/ ?>