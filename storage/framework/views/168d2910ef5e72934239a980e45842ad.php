<?php
    $m = $report->metrics ?? [];
    $v = fn (string $section, string $key) => data_get($m, "{$section}.{$key}", 0);
    $money = fn (string $section, string $key) => number_format((float) data_get($m, "{$section}.{$key}", 0), 2);
?>

<div class="space-y-6">
    <section class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-1">1. Lead Summary</h2>
        <p class="text-sm text-gray-500 mb-4">ملخص العملاء المحتملين</p>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-lg bg-gray-50 px-4 py-3"><dt class="text-sm text-gray-600">عملاء جدد</dt><dd class="text-2xl font-bold text-gray-900"><?php echo e($v('lead_summary', 'new_leads_received')); ?></dd></div>
            <div class="rounded-lg bg-gray-50 px-4 py-3"><dt class="text-sm text-gray-600">تم التواصل معهم</dt><dd class="text-2xl font-bold text-gray-900"><?php echo e($v('lead_summary', 'leads_contacted')); ?></dd></div>
            <div class="rounded-lg bg-gray-50 px-4 py-3"><dt class="text-sm text-gray-600">مؤهلون</dt><dd class="text-2xl font-bold text-gray-900"><?php echo e($v('lead_summary', 'qualified_leads')); ?></dd></div>
            <div class="rounded-lg bg-gray-50 px-4 py-3"><dt class="text-sm text-gray-600">غير مؤهلين</dt><dd class="text-2xl font-bold text-gray-900"><?php echo e($v('lead_summary', 'unqualified_leads')); ?></dd></div>
        </dl>
    </section>

    <section class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-1">2. Communication Activity</h2>
        <p class="text-sm text-gray-500 mb-4">نشاط التواصل</p>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-lg bg-gray-50 px-4 py-3"><dt class="text-sm text-gray-600">مكالمات تم إجراؤها</dt><dd class="text-2xl font-bold"><?php echo e($v('communication', 'calls_made')); ?></dd></div>
            <div class="rounded-lg bg-gray-50 px-4 py-3"><dt class="text-sm text-gray-600">مكالمات تم الرد عليها</dt><dd class="text-2xl font-bold"><?php echo e($v('communication', 'calls_answered')); ?></dd></div>
            <div class="rounded-lg bg-gray-50 px-4 py-3"><dt class="text-sm text-gray-600">محادثات واتساب</dt><dd class="text-2xl font-bold"><?php echo e($v('communication', 'whatsapp_conversations')); ?></dd></div>
            <div class="rounded-lg bg-gray-50 px-4 py-3"><dt class="text-sm text-gray-600">رسائل بريد</dt><dd class="text-2xl font-bold"><?php echo e($v('communication', 'emails_sent')); ?></dd></div>
        </dl>
    </section>

    <section class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-1">3. Meetings & Visits</h2>
        <p class="text-sm text-gray-500 mb-4">الاجتماعات والمعاينات</p>
        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-lg bg-gray-50 px-4 py-3"><dt class="text-sm text-gray-600">اجتماعات مجدولة</dt><dd class="text-2xl font-bold"><?php echo e($v('meetings_visits', 'meetings_scheduled')); ?></dd></div>
            <div class="rounded-lg bg-gray-50 px-4 py-3"><dt class="text-sm text-gray-600">اجتماعات منجزة</dt><dd class="text-2xl font-bold"><?php echo e($v('meetings_visits', 'meetings_completed')); ?></dd></div>
            <div class="rounded-lg bg-gray-50 px-4 py-3"><dt class="text-sm text-gray-600">معاينات عقارية</dt><dd class="text-2xl font-bold"><?php echo e($v('meetings_visits', 'property_visits_conducted')); ?></dd></div>
        </dl>
    </section>

    <section class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-1">4. Pipeline Progress</h2>
        <p class="text-sm text-gray-500 mb-4">تقدم مسار المبيعات</p>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-lg bg-gray-50 px-4 py-3"><dt class="text-sm text-gray-600">انتقلوا لمرحلة مؤهل</dt><dd class="text-2xl font-bold"><?php echo e($v('pipeline_progress', 'leads_to_qualified')); ?></dd></div>
            <div class="rounded-lg bg-gray-50 px-4 py-3"><dt class="text-sm text-gray-600">انتقلوا للتفاوض</dt><dd class="text-2xl font-bold"><?php echo e($v('pipeline_progress', 'leads_to_negotiation')); ?></dd></div>
            <div class="rounded-lg bg-gray-50 px-4 py-3"><dt class="text-sm text-gray-600">عروض مرسلة</dt><dd class="text-2xl font-bold"><?php echo e($v('pipeline_progress', 'proposals_sent')); ?></dd></div>
            <div class="rounded-lg bg-gray-50 px-4 py-3"><dt class="text-sm text-gray-600">عقود مرسلة</dt><dd class="text-2xl font-bold"><?php echo e($v('pipeline_progress', 'contracts_sent')); ?></dd></div>
        </dl>
    </section>

    <section class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-1">5. Deals</h2>
        <p class="text-sm text-gray-500 mb-4">الصفقات</p>
        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-lg bg-green-50 px-4 py-3"><dt class="text-sm text-green-800">صفقات رابحة</dt><dd class="text-2xl font-bold text-green-900"><?php echo e($v('deals', 'deals_closed_won')); ?></dd></div>
            <div class="rounded-lg bg-red-50 px-4 py-3"><dt class="text-sm text-red-800">صفقات خاسرة</dt><dd class="text-2xl font-bold text-red-900"><?php echo e($v('deals', 'deals_closed_lost')); ?></dd></div>
            <div class="rounded-lg bg-blue-50 px-4 py-3"><dt class="text-sm text-blue-800">إيراد متوقع (فرص جديدة)</dt><dd class="text-2xl font-bold text-blue-900"><?php echo e($money('deals', 'expected_revenue_new_opportunities')); ?></dd></div>
        </dl>
    </section>

    <section class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-1">6. Follow-Ups</h2>
        <p class="text-sm text-gray-500 mb-4">المتابعات</p>
        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-lg bg-gray-50 px-4 py-3"><dt class="text-sm text-gray-600">متابعات مكتملة</dt><dd class="text-2xl font-bold"><?php echo e($v('follow_ups', 'follow_ups_completed')); ?></dd></div>
            <div class="rounded-lg bg-amber-50 px-4 py-3"><dt class="text-sm text-amber-800">متابعات متأخرة</dt><dd class="text-2xl font-bold text-amber-900"><?php echo e($v('follow_ups', 'overdue_follow_ups')); ?></dd></div>
            <div class="rounded-lg bg-gray-50 px-4 py-3"><dt class="text-sm text-gray-600">مجدولة لغداً</dt><dd class="text-2xl font-bold"><?php echo e($v('follow_ups', 'follow_ups_scheduled_tomorrow')); ?></dd></div>
        </dl>
    </section>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\daily-reports\partials\metrics.blade.php ENDPATH**/ ?>