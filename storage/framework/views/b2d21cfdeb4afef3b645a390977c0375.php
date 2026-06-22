<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير المبيعات</title>
    <style>
        @page { size: A4; margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Tajawal', 'Arial', sans-serif; line-height: 1.6; color: #2d3748; background: #ffffff; }
        .report-container { max-width: 210mm; margin: 0 auto; padding: 20px; }
        
        .report-header { border-bottom: 2px solid #1a202c; padding-bottom: 20px; margin-bottom: 30px; }
        .company-name { font-size: 24px; font-weight: 700; color: #1a202c; margin-bottom: 5px; }
        .report-title { font-size: 18px; font-weight: 600; color: #4a5568; margin-bottom: 10px; }
        .report-date { font-size: 12px; color: #718096; }
        
        .summary-box { border: 1px solid #e2e8f0; padding: 20px; margin-bottom: 30px; background: #f7fafc; }
        .summary-title { font-size: 14px; font-weight: 600; color: #1a202c; margin-bottom: 15px; border-bottom: 1px solid #cbd5e0; padding-bottom: 8px; }
        .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .summary-item { text-align: center; }
        .summary-label { font-size: 11px; color: #718096; margin-bottom: 5px; }
        .summary-value { font-size: 18px; font-weight: 600; color: #1a202c; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 11px; }
        .data-table thead th { background: #2d3748; color: #ffffff; padding: 10px 8px; text-align: right; font-weight: 600; font-size: 11px; border: 1px solid #1a202c; }
        .data-table tbody td { padding: 8px; border: 1px solid #e2e8f0; text-align: right; background: #ffffff; }
        .data-table tbody tr:nth-child(even) td { background: #f7fafc; }
        .data-table tfoot td { padding: 10px 8px; border: 1px solid #2d3748; font-weight: 600; background: #edf2f7; color: #1a202c; }
        
        .client-name { font-weight: 600; color: #1a202c; }
        .amount { font-weight: 600; text-align: left; }
        
        .status-badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 10px; font-weight: 500; }
        .status-pending { background: #feebc8; color: #7c2d12; }
        .status-closed { background: #c6f6d5; color: #22543d; }
        .status-lost { background: #fed7d7; color: #742a2a; }
        
        .report-footer { margin-top: 50px; padding-top: 20px; border-top: 1px solid #e2e8f0; }
        .signature-section { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-top: 30px; }
        .signature-box { text-align: center; }
        .signature-line { border-top: 1px solid #2d3748; margin-bottom: 8px; padding-top: 40px; }
        .signature-label { font-size: 11px; color: #4a5568; }
        
        @media print { body { background: white; } .report-container { padding: 0; } .avoid-break { page-break-inside: avoid; } }
        .no-data { text-align: center; padding: 40px; color: #718096; font-size: 14px; }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="report-header">
            <div class="company-name"><?php echo e(\App\Helpers\SettingsHelper::getCompanyName()); ?></div>
            <div class="report-title">تقرير المبيعات</div>
            <div class="report-date">الفترة: <?php echo e($start_date); ?> إلى <?php echo e($end_date); ?> | تاريخ الطباعة: <?php echo e(now()->format('Y-m-d h:i A')); ?></div>
        </div>
        
        <?php if($sales->count() > 0): ?>
            <div class="summary-box avoid-break">
                <div class="summary-title">ملخص التقرير</div>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-label">إجمالي عدد المبيعات</div>
                        <div class="summary-value"><?php echo e($summary['total_sales']); ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">إجمالي المبلغ</div>
                        <div class="summary-value"><?php echo e(number_format($summary['total_amount'], 0)); ?> ج.م</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">متوسط قيمة الصفقة</div>
                        <div class="summary-value"><?php echo e(number_format($summary['average_sale'], 0)); ?> ج.م</div>
                    </div>
                </div>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">م</th>
                        <th style="width: 22%;">اسم الصفقة</th>
                        <th style="width: 20%;">العميل</th>
                        <th style="width: 15%;">المبلغ</th>
                        <th style="width: 15%;">تاريخ البيع</th>
                        <th style="width: 10%;">المرحلة</th>
                        <th style="width: 10%;">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $stageLabels = [
                            'lead' => 'عميل محتمل',
                            'prospect' => 'مهتم',
                            'proposal' => 'عرض سعر',
                            'negotiation' => 'تفاوض',
                            'closed_won' => 'تم البيع',
                            'closed_lost' => 'خسارة',
                        ];
                    ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td class="client-name"><?php echo e($sale->product_service); ?></td>
                        <td><?php echo e($sale->client->name ?? '-'); ?></td>
                        <td class="amount"><?php echo e(number_format($sale->amount, 0)); ?> ج.م</td>
                        <td><?php echo e($sale->expected_close_date?->format('Y-m-d') ?? '-'); ?></td>
                        <td><?php echo e($stageLabels[$sale->stage] ?? $sale->stage); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo e($sale->stage === 'closed_won' ? 'closed' : ($sale->stage === 'closed_lost' ? 'lost' : 'pending')); ?>">
                                <?php echo e($stageLabels[$sale->stage] ?? $sale->stage); ?>

                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: left;">الإجمالي الكلي</td>
                        <td class="amount"><?php echo e(number_format($summary['total_amount'], 0)); ?> ج.م</td>
                        <td colspan="3"><?php echo e($summary['total_sales']); ?> صفقة</td>
                    </tr>
                </tfoot>
            </table>
            
            <div class="report-footer avoid-break">
                <div class="signature-section">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="signature-label">إعداد</div>
                    </div>
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="signature-label">مراجعة</div>
                    </div>
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="signature-label">اعتماد</div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="no-data">
                <p>لا توجد مبيعات للفترة المحددة</p>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>


<?php /**PATH C:\xampp\htdocs\afaq\resources\views\reports\sales-print.blade.php ENDPATH**/ ?>