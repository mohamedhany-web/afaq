<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير الأقسام</title>
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
        .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
        .summary-item { text-align: center; }
        .summary-label { font-size: 10px; color: #718096; margin-bottom: 5px; }
        .summary-value { font-size: 16px; font-weight: 600; color: #1a202c; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 11px; }
        .data-table thead th { background: #2d3748; color: #ffffff; padding: 10px 8px; text-align: right; font-weight: 600; font-size: 11px; border: 1px solid #1a202c; }
        .data-table tbody td { padding: 8px; border: 1px solid #e2e8f0; text-align: right; background: #ffffff; }
        .data-table tbody tr:nth-child(even) td { background: #f7fafc; }
        
        .dept-name { font-weight: 600; color: #1a202c; }
        
        .status-badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 10px; font-weight: 500; }
        .status-active { background: #c6f6d5; color: #22543d; }
        .status-inactive { background: #e2e8f0; color: #4a5568; }
        
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
            <div class="report-title">تقرير الأقسام</div>
            <div class="report-date">تاريخ التقرير: <?php echo e(now()->format('Y-m-d')); ?> | <?php echo e(now()->format('h:i A')); ?></div>
        </div>
        
        <?php if($departments->count() > 0): ?>
            <div class="summary-box avoid-break">
                <div class="summary-title">ملخص التقرير</div>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-label">إجمالي الأقسام</div>
                        <div class="summary-value"><?php echo e($summary['total_departments']); ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">إجمالي الموظفين</div>
                        <div class="summary-value"><?php echo e($summary['total_employees']); ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">إجمالي المشاريع</div>
                        <div class="summary-value"><?php echo e($summary['total_projects']); ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">متوسط الموظفين</div>
                        <div class="summary-value"><?php echo e($summary['average_employees']); ?></div>
                    </div>
                </div>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">م</th>
                        <th style="width: 27%;">اسم القسم</th>
                        <th style="width: 25%;">مدير القسم</th>
                        <th style="width: 15%;">عدد الموظفين</th>
                        <th style="width: 15%;">عدد المشاريع</th>
                        <th style="width: 10%;">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td class="dept-name"><?php echo e($department->name); ?></td>
                        <td><?php echo e($department->manager->name ?? '-'); ?></td>
                        <td><?php echo e($department->employees_count); ?></td>
                        <td><?php echo e($department->projects_count); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo e($department->is_active ? 'active' : 'inactive'); ?>">
                                <?php echo e($department->is_active ? 'نشط' : 'غير نشط'); ?>

                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
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
                <p>لا توجد أقسام</p>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>


<?php /**PATH C:\xampp\htdocs\afaq\resources\views\reports\departments-print.blade.php ENDPATH**/ ?>