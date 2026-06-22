<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير الحضور الشهري — <?php echo e($month->translatedFormat('F Y')); ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 24px; color: #111; }
        h1 { font-size: 22px; margin-bottom: 4px; }
        .meta { color: #555; margin-bottom: 20px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: right; }
        th { background: #f3f4f6; }
        .summary { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 16px; min-width: 120px; }
        .card strong { display: block; font-size: 18px; margin-top: 4px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()" style="margin-bottom:16px;padding:8px 16px;">طباعة</button>
    <h1>تقرير الحضور الشهري</h1>
    <p class="meta"><?php echo e($month->translatedFormat('F Y')); ?> — من <?php echo e($start->format('Y/m/d')); ?> إلى <?php echo e($end->format('Y/m/d')); ?></p>

    <div class="summary">
        <div class="card">الموظفون<strong><?php echo e($summary['employees_count']); ?></strong></div>
        <div class="card">حضور<strong><?php echo e($summary['total_present']); ?></strong></div>
        <div class="card">غياب<strong><?php echo e($summary['total_absent']); ?></strong></div>
        <div class="card">تأخير<strong><?php echo e($summary['total_late']); ?></strong></div>
        <div class="card">إجازات<strong><?php echo e($summary['total_leave_days']); ?></strong></div>
        <div class="card">أذونات<strong><?php echo e($summary['total_permits']); ?></strong></div>
        <div class="card">معدل الحضور<strong><?php echo e($summary['avg_attendance_rate']); ?>%</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>الموظف</th>
                <th>القسم</th>
                <th>متوقع</th>
                <th>حضور</th>
                <th>تأخير</th>
                <th>غياب</th>
                <th>إجازات</th>
                <th>أذونات</th>
                <th>ساعات</th>
                <th>معدل</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($row['employee']->first_name); ?> <?php echo e($row['employee']->last_name); ?></td>
                <td><?php echo e($row['employee']->department?->name ?? '—'); ?></td>
                <td><?php echo e($row['expected_days']); ?></td>
                <td><?php echo e($row['present_days']); ?></td>
                <td><?php echo e($row['late_days']); ?></td>
                <td><?php echo e($row['absent_days']); ?></td>
                <td><?php echo e($row['leave_days']); ?></td>
                <td><?php echo e($row['permit_count']); ?></td>
                <td><?php echo e($row['total_hours']); ?></td>
                <td><?php echo e($row['attendance_rate']); ?>%</td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\hr\reports\monthly-print.blade.php ENDPATH**/ ?>