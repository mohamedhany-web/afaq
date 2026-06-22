<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($title ?? 'تقرير'); ?> - <?php echo e(config('app.name', 'Solvesta')); ?></title>
    
    <!-- Arabic Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        :root {
            --primary-color: #1e40af;
            --secondary-color: #3b82f6;
            --accent-color: #60a5fa;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --border-color: #e2e8f0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', sans-serif;
            background: #f8fafc;
            color: var(--text-primary);
            line-height: 1.7;
            font-size: 14px;
        }
        
        .print-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }
        
        /* Enhanced Table Styles */
        .report-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 25px 0;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid var(--border-color);
        }
        
        .report-table thead {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
        }
        
        .report-table thead th {
            padding: 18px 16px;
            text-align: right;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 3px solid #1e3a8a;
            white-space: nowrap;
        }
        
        .report-table thead th:first-child {
            border-top-right-radius: 12px;
        }
        
        .report-table thead th:last-child {
            border-top-left-radius: 12px;
        }
        
        .report-table tbody tr {
            border-bottom: 1px solid var(--border-color);
            transition: all 0.2s ease;
        }
        
        .report-table tbody tr:nth-child(odd) {
            background-color: #f8fafc;
        }
        
        .report-table tbody tr:nth-child(even) {
            background-color: white;
        }
        
        .report-table tbody tr:hover {
            background-color: #eff6ff;
            transform: scale(1.005);
        }
        
        .report-table tbody td {
            padding: 14px 16px;
            font-size: 13px;
            color: var(--text-secondary);
            vertical-align: middle;
        }
        
        .report-table tbody td strong {
            color: var(--text-primary);
            font-weight: 700;
        }
        
        .report-table tfoot {
            background: linear-gradient(to bottom, #f1f5f9, #e2e8f0);
            border-top: 3px solid var(--secondary-color);
        }
        
        .report-table tfoot td {
            padding: 16px;
            font-weight: 800;
            font-size: 15px;
            color: var(--text-primary);
        }
        
        /* Enhanced Summary Cards */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin: 30px 0;
        }
        
        .summary-card {
            background: white;
            padding: 25px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }
        
        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .summary-card .label {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 600;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .summary-card .value {
            font-size: 32px;
            font-weight: 900;
            color: var(--text-primary);
            font-family: 'Cairo', sans-serif;
        }
        
        .summary-card.primary::before {
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
        }
        
        .summary-card.success::before {
            background: linear-gradient(90deg, #10b981, #34d399);
        }
        
        .summary-card.warning::before {
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }
        
        .summary-card.danger::before {
            background: linear-gradient(90deg, #ef4444, #f87171);
        }
        
        /* Chart Container */
        .chart-container {
            background: white;
            padding: 30px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            margin: 25px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }
        
        .chart-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid var(--secondary-color);
            position: relative;
        }
        
        .chart-title::after {
            content: '';
            position: absolute;
            bottom: -3px;
            right: 0;
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), transparent);
        }
        
        /* Status Badges */
        .badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        
        .badge-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 1px solid #fcd34d;
        }
        
        .badge-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        
        .badge-info {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border: 1px solid #93c5fd;
        }
        
        /* Signature Section */
        .signature-section {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin: 50px 0 30px;
            page-break-inside: avoid;
        }
        
        .signature-box {
            text-align: center;
            padding: 25px;
            border: 2px dashed var(--border-color);
            border-radius: 12px;
            min-height: 120px;
            background: linear-gradient(to bottom, white, #f8fafc);
            position: relative;
        }
        
        .signature-box::before {
            content: '✓';
            position: absolute;
            top: 10px;
            left: 10px;
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, var(--success-color), #34d399);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
            opacity: 0.2;
        }
        
        .signature-label {
            font-weight: 700;
            color: var(--text-secondary);
            margin-bottom: 60px;
            display: block;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .signature-line {
            border-top: 2px solid var(--text-primary);
            width: 80%;
            margin: 0 auto;
            padding-top: 10px;
            font-size: 12px;
            color: var(--text-secondary);
            font-weight: 600;
        }
        
        /* Report Footer */
        .report-footer {
            margin-top: 60px;
            padding-top: 25px;
            border-top: 3px solid var(--border-color);
            text-align: center;
            color: var(--text-secondary);
            font-size: 12px;
        }
        
        .report-footer p {
            margin: 5px 0;
        }
        
        .report-footer strong {
            color: var(--text-primary);
        }
        
        /* Screen-only Styles */
        @media screen {
            .print-actions {
                position: sticky;
                top: 20px;
                z-index: 1000;
                margin-bottom: 25px;
                padding: 20px;
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            }
            
            .print-btn {
                background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
                color: white;
                padding: 14px 28px;
                border-radius: 10px;
                border: none;
                font-weight: 700;
                cursor: pointer;
                box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 10px;
                font-family: 'Cairo', sans-serif;
                font-size: 15px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .print-btn:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 30px rgba(59, 130, 246, 0.5);
            }
            
            .print-btn:active {
                transform: translateY(-1px);
            }
            
            .back-btn {
                background: white;
                color: var(--text-secondary);
                padding: 14px 28px;
                border-radius: 10px;
                border: 2px solid var(--border-color);
                font-weight: 700;
                cursor: pointer;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 10px;
                font-family: 'Cairo', sans-serif;
                text-decoration: none;
                font-size: 15px;
            }
            
            .back-btn:hover {
                border-color: var(--secondary-color);
                color: var(--secondary-color);
                transform: translateX(5px);
            }
        }
        
        /* Print-specific Styles */
        @media print {
            body {
                background: white;
            }
            
            .print-container {
                max-width: 100%;
                padding: 0;
                margin: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-after: always;
            }
            
            .page-break-before {
                page-break-before: always;
            }
            
            .avoid-break {
                page-break-inside: avoid;
            }
            
            .report-table {
                box-shadow: none;
            }
            
            .report-table thead {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .report-table tbody tr:nth-child(odd) {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .summary-card::before {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .badge-success,
            .badge-warning,
            .badge-danger,
            .badge-info {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .report-table {
                page-break-inside: auto;
            }
            
            .report-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            .report-table thead {
                display: table-header-group;
            }
            
            .report-table tfoot {
                display: table-footer-group;
            }
            
            @page {
                size: A4;
                margin: 1.5cm 1cm;
            }
        }
    </style>
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <div class="print-container">
        
        <div class="print-actions no-print" style="display: flex; justify-content: space-between; align-items: center;">
            <a href="<?php echo e($backUrl ?? route('reports.index')); ?>" class="back-btn">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                رجوع للتقارير
            </a>
            <button onclick="window.print()" class="print-btn">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                طباعة التقرير الآن
            </button>
        </div>
        
        
        <?php if (isset($component)) { $__componentOriginal89e2385e85f2442852cb963dc5feebea = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89e2385e85f2442852cb963dc5feebea = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.print-header','data' => ['title' => $title ?? 'تقرير','subtitle' => $subtitle ?? null,'period' => $period ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('print-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title ?? 'تقرير'),'subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($subtitle ?? null),'period' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($period ?? null)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal89e2385e85f2442852cb963dc5feebea)): ?>
<?php $attributes = $__attributesOriginal89e2385e85f2442852cb963dc5feebea; ?>
<?php unset($__attributesOriginal89e2385e85f2442852cb963dc5feebea); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal89e2385e85f2442852cb963dc5feebea)): ?>
<?php $component = $__componentOriginal89e2385e85f2442852cb963dc5feebea; ?>
<?php unset($__componentOriginal89e2385e85f2442852cb963dc5feebea); ?>
<?php endif; ?>
        
        
        <div class="report-content">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
        
        
        <div class="report-footer avoid-break">
            <?php if(isset($showSignatures) && $showSignatures): ?>
            <div class="signature-section">
                <div class="signature-box">
                    <span class="signature-label">أعد التقرير</span>
                    <div class="signature-line">التوقيع والختم</div>
                </div>
                <div class="signature-box">
                    <span class="signature-label">راجع التقرير</span>
                    <div class="signature-line">التوقيع والختم</div>
                </div>
                <div class="signature-box">
                    <span class="signature-label">اعتمد التقرير</span>
                    <div class="signature-line">التوقيع والختم</div>
                </div>
            </div>
            <?php endif; ?>
            
            <div style="margin-top: 40px; padding: 20px; background: #f8fafc; border-radius: 10px;">
                <p style="font-weight: 600; color: var(--text-primary); margin-bottom: 5px;">
                    📊 تقرير إلكتروني معتمد من نظام <strong><?php echo e(config('app.name', 'Solvesta')); ?></strong>
                </p>
                <p style="font-size: 11px;">
                    تاريخ الإنشاء: <strong><?php echo e(now()->format('Y-m-d')); ?></strong> | 
                    الوقت: <strong><?php echo e(now()->format('h:i A')); ?></strong> | 
                    المستخدم: <strong><?php echo e(auth()->user()->name ?? 'النظام'); ?></strong>
                </p>
            </div>
        </div>
    </div>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\layouts\print.blade.php ENDPATH**/ ?>