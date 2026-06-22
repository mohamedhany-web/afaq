<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير الموظفين</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Tajawal', 'Arial', sans-serif;
            line-height: 1.6;
            color: #2d3748;
            background: #ffffff;
        }
        
        .report-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header */
        .report-header {
            border-bottom: 2px solid #1a202c;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 10px;
        }
        
        .report-date {
            font-size: 12px;
            color: #718096;
        }
        
        /* Summary Box */
        .summary-box {
            border: 1px solid #e2e8f0;
            padding: 20px;
            margin-bottom: 30px;
            background: #f7fafc;
        }
        
        .summary-title {
            font-size: 14px;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 15px;
            border-bottom: 1px solid #cbd5e0;
            padding-bottom: 8px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-label {
            font-size: 11px;
            color: #718096;
            margin-bottom: 5px;
        }
        
        .summary-value {
            font-size: 18px;
            font-weight: 600;
            color: #1a202c;
        }
        
        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 11px;
        }
        
        .data-table thead th {
            background: #2d3748;
            color: #ffffff;
            padding: 10px 8px;
            text-align: right;
            font-weight: 600;
            font-size: 11px;
            border: 1px solid #1a202c;
        }
        
        .data-table tbody td {
            padding: 8px;
            border: 1px solid #e2e8f0;
            text-align: right;
            background: #ffffff;
        }
        
        .data-table tbody tr:nth-child(even) td {
            background: #f7fafc;
        }
        
        .data-table tfoot td {
            padding: 10px 8px;
            border: 1px solid #2d3748;
            font-weight: 600;
            background: #edf2f7;
            color: #1a202c;
        }
        
        .employee-id {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }
        
        .employee-name {
            font-weight: 600;
            color: #1a202c;
        }
        
        .salary-amount {
            font-weight: 600;
            text-align: left;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 500;
        }
        
        .status-active {
            background: #c6f6d5;
            color: #22543d;
        }
        
        .status-inactive {
            background: #e2e8f0;
            color: #4a5568;
        }
        
        /* Department Section */
        .department-section {
            margin-top: 30px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 15px;
            border-bottom: 1px solid #cbd5e0;
            padding-bottom: 8px;
        }
        
        .department-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .department-item {
            border: 1px solid #e2e8f0;
            padding: 12px;
            background: #ffffff;
        }
        
        .department-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .department-name {
            font-size: 12px;
            font-weight: 600;
            color: #2d3748;
        }
        
        .department-count {
            font-size: 14px;
            font-weight: 700;
            color: #1a202c;
        }
        
        .progress-bar {
            width: 100%;
            height: 6px;
            background: #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: #4a5568;
        }
        
        .department-percentage {
            font-size: 10px;
            color: #718096;
            margin-top: 5px;
        }
        
        /* Footer */
        .report-footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        
        .signature-section {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 30px;
        }
        
        .signature-box {
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #2d3748;
            margin-bottom: 8px;
            padding-top: 40px;
        }
        
        .signature-label {
            font-size: 11px;
            color: #4a5568;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
            }
            
            .report-container {
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
            
            .avoid-break {
                page-break-inside: avoid;
            }
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #718096;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div class="company-name"><?php echo e(\App\Helpers\SettingsHelper::getCompanyName()); ?></div>
            <div class="report-title">تقرير الموظفين والرواتب</div>
            <div class="report-date">تاريخ التقرير: <?php echo e(now()->format('Y-m-d')); ?> | <?php echo e(now()->format('h:i A')); ?></div>
        </div>
        
        <?php if($employees->count() > 0): ?>
            <!-- Summary -->
            <div class="summary-box avoid-break">
                <div class="summary-title">ملخص التقرير</div>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-label">إجمالي عدد الموظفين</div>
                        <div class="summary-value"><?php echo e($summary['total']); ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">إجمالي الرواتب الشهرية</div>
                        <div class="summary-value"><?php echo e(number_format($summary['total_salary'], 0)); ?> ج.م</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">متوسط الراتب</div>
                        <div class="summary-value"><?php echo e(number_format($summary['average_salary'], 0)); ?> ج.م</div>
                    </div>
                </div>
            </div>
            
            <!-- Employees Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">م</th>
                        <th style="width: 10%;">رقم الموظف</th>
                        <th style="width: 20%;">الاسم الكامل</th>
                        <th style="width: 15%;">القسم</th>
                        <th style="width: 15%;">الوظيفة</th>
                        <th style="width: 12%;">الراتب الشهري</th>
                        <th style="width: 12%;">تاريخ التعيين</th>
                        <th style="width: 8%;">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td class="employee-id"><?php echo e($employee->employee_id); ?></td>
                        <td class="employee-name"><?php echo e($employee->first_name); ?> <?php echo e($employee->last_name); ?></td>
                        <td><?php echo e($employee->department->name ?? '-'); ?></td>
                        <td><?php echo e($employee->position); ?></td>
                        <td class="salary-amount"><?php echo e(number_format($employee->salary, 0)); ?> ج.م</td>
                        <td><?php echo e($employee->hire_date ? $employee->hire_date->format('Y-m-d') : '-'); ?></td>
                        <td>
                            <span class="status-badge <?php echo e($employee->status === 'active' ? 'status-active' : 'status-inactive'); ?>">
                                <?php echo e($employee->status === 'active' ? 'نشط' : 'غير نشط'); ?>

                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" style="text-align: left;">الإجمالي الكلي</td>
                        <td class="salary-amount"><?php echo e(number_format($summary['total_salary'], 0)); ?> ج.م</td>
                        <td colspan="2"><?php echo e($summary['total']); ?> موظف</td>
                    </tr>
                </tfoot>
            </table>
            
            <!-- Department Distribution -->
            <?php if($summary['by_department']->count() > 0): ?>
            <div class="department-section avoid-break">
                <div class="section-title">التوزيع حسب الأقسام</div>
                <div class="department-grid">
            <?php $__currentLoopData = $summary['by_department']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deptId => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $dept = $departments->firstWhere('id', $deptId);
                    $percentage = ($count / $summary['total']) * 100;
                ?>
                <?php if($dept): ?>
                        <div class="department-item">
                            <div class="department-header">
                                <span class="department-name"><?php echo e($dept->name); ?></span>
                                <span class="department-count"><?php echo e($count); ?></span>
                    </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo e($percentage); ?>%;"></div>
                    </div>
                            <div class="department-percentage"><?php echo e(number_format($percentage, 1)); ?>% من الموظفين</div>
                </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
            
            <!-- Footer with Signatures -->
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
                <p>لا توجد بيانات موظفين مطابقة للمعايير المحددة</p>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Auto print on load
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\reports\employees-print.blade.php ENDPATH**/ ?>