<?php $__env->startSection('page-title', request()->routeIs('financial-invoices.*') ? 'الفواتير المالية' : 'فواتير المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('accounting.partials.context', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php
    $isFinancial = request()->routeIs('financial-invoices.*');
    $createRoute = $isFinancial ? route('financial-invoices.create') : route('invoices.create');
?>
<?php echo $__env->make('crm.partials.page-header', [
    'title' => $isFinancial ? 'الفواتير المالية' : 'فواتير المبيعات',
    'subtitle' => 'إدارة وتتبع الفواتير والتحصيل',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
    'actionUrl' => $createRoute,
    'actionLabel' => 'فاتورة جديدة',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('accounting.partials.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي الفواتير', 'value' => $totalInvoices, 'accent' => 'theme', 'compact' => true, 'href' => ($isFinancial ? route('financial-invoices.index') : route('invoices.index')) . '#page-data', 'linkLabel' => 'عرض القائمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مدفوعة', 'value' => $paidInvoices, 'accent' => 'green', 'compact' => true, 'footer' => '<span class="text-gray-500">'.($totalInvoices > 0 ? round(($paidInvoices / $totalInvoices) * 100) : 0).'%</span>', 'href' => ($isFinancial ? route('financial-invoices.index', ['status' => 'paid']) : route('invoices.index', ['status' => 'paid'])) . '#page-data', 'linkLabel' => 'عرض المدفوعة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'معلقة', 'value' => $pendingInvoices, 'accent' => 'amber', 'compact' => true, 'footer' => '<span class="text-amber-600">'.$money($pendingAmount).'</span>', 'href' => ($isFinancial ? route('financial-invoices.index', ['status' => 'pending']) : route('invoices.index', ['status' => 'pending'])) . '#page-data', 'linkLabel' => 'عرض المعلّقة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي الإيرادات', 'value' => $money($totalRevenue), 'accent' => 'purple', 'compact' => true, 'footer' => '<span class="text-gray-500">'.$money($monthlyRevenue).' هذا الشهر</span>', 'href' => ($isFinancial ? route('financial-invoices.index', ['status' => 'paid']) : route('invoices.index', ['status' => 'paid'])) . '#page-data', 'linkLabel' => 'عرض الإيرادات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div id="page-data" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b font-bold font-tajawal flex justify-between items-center" style="<?php echo e($headerStyle); ?>">
                <h3>قائمة الفواتير</h3>
                <span class="text-xs px-3 py-1 rounded-full font-medium" style="background:<?php echo e($themeColor); ?>15;color:<?php echo e($themeColor); ?>;"><?php echo e($invoices->total()); ?></span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[1000px] font-tajawal">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-gray-600">
                        <th class="p-4 text-right font-bold">رقم الفاتورة</th>
                        <th class="p-4 text-right font-bold">العميل</th>
                        <th class="p-4 text-right font-bold">المشروع</th>
                        <th class="p-4 text-center font-bold">المبلغ</th>
                        <th class="p-4 text-center font-bold">المدفوع</th>
                        <th class="p-4 text-center font-bold">الحالة</th>
                        <th class="p-4 text-center font-bold">التاريخ</th>
                        <th class="p-4 text-center font-bold">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="p-4">
                            <div class="font-bold text-gray-900"><?php echo e($invoice->invoice_number); ?></div>
                        </td>
                        <td class="p-4 text-gray-700"><?php echo e($invoice->client?->name ?? '—'); ?></td>
                        <td class="p-4 text-gray-700"><?php echo e($invoice->project?->name ?? '—'); ?></td>
                        <td class="p-4 text-center font-bold tabular-nums"><?php echo e($money($invoice->total_amount)); ?></td>
                        <td class="p-4 text-center font-bold text-green-600 tabular-nums"><?php echo e($money($invoice->paid_amount)); ?></td>
                        <td class="p-4 text-center">
                            <?php
                                $statusColor = match($invoice->status) {
                                    'paid' => 'bg-green-100 text-green-800',
                                    'sent' => 'bg-blue-100 text-blue-800',
                                    'viewed' => 'bg-yellow-100 text-yellow-800',
                                    'overdue' => 'bg-red-100 text-red-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                                $statusName = match($invoice->status) {
                                    'draft' => 'مسودة',
                                    'sent' => 'مرسل',
                                    'viewed' => 'تم المشاهدة',
                                    'paid' => 'مدفوع',
                                    'overdue' => 'متأخر',
                                    'cancelled' => 'ملغي',
                                    default => $invoice->status
                                };
                            ?>
                            <span class="text-xs font-bold px-2 py-1 rounded-lg <?php echo e($statusColor); ?>"><?php echo e($statusName); ?></span>
                        </td>
                        <td class="p-4 text-center text-gray-500">
                            <?php if($invoice->invoice_date): ?>
                                <?php echo e($invoice->invoice_date instanceof \Carbon\Carbon ? $invoice->invoice_date->format('Y/m/d') : \Carbon\Carbon::parse($invoice->invoice_date)->format('Y/m/d')); ?>

                            <?php elseif($invoice->issue_date): ?>
                                <?php echo e($invoice->issue_date instanceof \Carbon\Carbon ? $invoice->issue_date->format('Y/m/d') : \Carbon\Carbon::parse($invoice->issue_date)->format('Y/m/d')); ?>

                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?php echo e(request()->routeIs('financial-invoices.*') ? route('financial-invoices.show', $invoice) : route('invoices.show', $invoice)); ?>" class="text-xs font-bold" style="color:<?php echo e($themeColor); ?>">عرض</a>
                                <?php if($invoice->status !== 'paid'): ?>
                                <button onclick="markAsPaid(<?php echo e($invoice->id); ?>)" class="text-xs font-bold text-green-600">تحديد كمدفوع</button>
                                <?php endif; ?>
                                <?php if($invoice->status === 'draft'): ?>
                                <button onclick="deleteInvoice(<?php echo e($invoice->id); ?>)" class="text-xs font-bold text-red-600">حذف</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="p-4 bg-gray-100 rounded-full mb-4">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">لا توجد فواتير</h3>
                                <p class="text-gray-600 mb-4">ابدأ بإنشاء فاتورة جديدة</p>
                                <a href="<?php echo e(route('invoices.create')); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm font-medium">
                                    إضافة فاتورة جديدة
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($invoices->hasPages()): ?>
        <div class="px-5 py-4 border-t"><?php echo e($invoices->links()); ?></div>
        <?php endif; ?>
    </div>

<script>
var invoicesBasePath = '<?php echo e(request()->routeIs("financial-invoices.*") ? "financial-invoices" : "invoices"); ?>';
function markAsPaid(invoiceId) {
    if (confirm('هل أنت متأكد من تحديد هذه الفاتورة كمدفوعة؟')) {
        fetch(`/${invoicesBasePath}/${invoiceId}/mark-as-paid`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => { throw new Error(data.message || 'حدث خطأ'); });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification('تم تحديث حالة الفاتورة بنجاح', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message || 'حدث خطأ', 'error');
            }
        })
        .catch(error => {
            showNotification(error.message || 'حدث خطأ في الاتصال', 'error');
        });
    }
}

function deleteInvoice(invoiceId) {
    if (confirm('هل أنت متأكد من حذف هذه الفاتورة؟\n\nملاحظة: لا يمكن التراجع عن هذا الإجراء.')) {
        fetch(`/${invoicesBasePath}/${invoiceId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'حدث خطأ');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification('تم حذف الفاتورة بنجاح', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message || 'حدث خطأ', 'error');
            }
        })
        .catch(error => {
            showNotification(error.message || 'حدث خطأ في الاتصال', 'error');
        });
    }
}

function exportInvoices() {
    alert('وظيفة التصدير قيد التطوير');
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white font-medium ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        notification.style.transition = 'all 0.3s';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\invoices\index.blade.php ENDPATH**/ ?>