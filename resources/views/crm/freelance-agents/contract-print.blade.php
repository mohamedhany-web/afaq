<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>عقد وكيل عقاري مستقل — {{ $contract->user?->name }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; line-height: 1.8; max-width: 800px; margin: 2rem auto; padding: 1rem 2rem; color: #111; }
        h1 { text-align: center; font-size: 1.35rem; margin-bottom: 0.5rem; }
        h2 { font-size: 1rem; margin-top: 1.5rem; border-bottom: 1px solid #ddd; padding-bottom: 0.25rem; }
        p, li { font-size: 0.95rem; }
        .meta { text-align: center; color: #555; margin-bottom: 2rem; }
        .signatures { margin-top: 3rem; display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
        .sig-box { border-top: 1px solid #333; padding-top: 0.5rem; min-height: 80px; }
        @media print { body { margin: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
<button class="no-print" onclick="window.print()" style="margin-bottom:1rem;padding:0.5rem 1rem;cursor:pointer;">طباعة</button>

<h1>عقد وكيل عقاري مستقل (Freelance Agent Agreement)</h1>
<p class="meta">{{ $companyName }}</p>

<p>إنه في يوم: <strong>{{ $contract->signed_at?->locale('ar')->translatedFormat('l') ?? '...............' }}</strong> الموافق: <strong>{{ $contract->signed_at?->format('d / m / Y') ?? '.... / .... / ........' }}</strong></p>
<p>تم الاتفاق بين كل من:</p>

<p><strong>الطرف الأول:</strong> شركة <strong>{{ $companyName }}</strong> للاستثمار والتسويق العقاري، ويمثلها في التوقيع السيد/ <strong>{{ $contract->company_signatory_name ?? '.......................................' }}</strong> بصفته <strong>{{ $contract->company_signatory_title ?? 'المدير التنفيذي' }}</strong>.</p>

<p><strong>الطرف الثاني:</strong> السيد/ <strong>{{ $contract->user?->name }}</strong>، الجنسية: <strong>{{ $contract->nationality ?? '....................' }}</strong>، بطاقة الرقم القومي: <strong>{{ $contract->national_id ?? '.......................................' }}</strong>، والمقيم في: <strong>{{ $contract->address ?? '.......................................................' }}</strong>، رقم الهاتف: <strong>{{ $contract->phone ?? $contract->user?->phone ?? '......................................' }}</strong>.</p>

<h2>تمهيد</h2>
<p>حيث إن الطرف الأول شركة عاملة في مجال التسويق والاستثمار العقاري وتمتلك محفظة عقارية وقنوات تسويقية، وحيث إن الطرف الثاني يعمل كوكيل عقاري مستقل (Freelance Agent) ويمتلك الخبرة والقدرة على جلب وتسويق العقارات وجلب العملاء، فقد التقت إرادة الطرفين على التعاقد وفقًا للشروط والبنود التالية:</p>

<h2>البند الأول: الطبيعة القانونية للتعاقد</h2>
<ol>
    <li>يقر الطرفان بأن هذا العقد عقد عمل مستقل (Freelance) ولا ينشأ عنه علاقة توظيف تنص عليها قوانين العمل.</li>
    <li>للطرف الثاني الحرية في تنظيم وقت عمله شريطة الالتزام بقواعد العمل ونظام الشركة (CRM).</li>
</ol>

<h2>البند الثاني: مهام ومسؤوليات الوكيل</h2>
<ol>
    <li>جلب وتسجيل العقارات (Listing) وإدخال بيانات العملاء على نظام CRM فور التواصل.</li>
    <li>لا يعتد بأي عميل غير مسجل على النظام.</li>
    <li>معاينة الوحدات (Showings) وشرح المشاريع بأسلوب بيعي احترافي.</li>
    <li>متابعة العملاء (Follow-up) وتحديث حالاتهم؛ في حال عدم المتابعة لمدة 30 يومًا يحق للشركة نقل العميل لوكيل آخر.</li>
    <li>إدارة المفاوضات المبدئية وإبلاغ الشركة فورًا لتجهيز الإجراءات القانونية.</li>
    <li>الالتزام بالصدق والأمانة وعدم تقديم معلومات مغلوطة.</li>
</ol>

<h2>البند الثالث: التزامات الشركة</h2>
<ol>
    <li>توفير التدريب والدعم المعرفي للمشاريع والمطورين المتعاقد معهم.</li>
    <li>إتاحة نظام CRM لتنظيم العملاء والصفقات.</li>
    <li>الدعم القانوني وصياغة العقود ومراجعة أوراق الملكية (Closing).</li>
    <li>صرف مستحقات الوكيل وفق جدول هيكل العمولات المرفق فور تحصيل عمولة الشركة.</li>
</ol>

<h2>البند الرابع: سياسة العمولات وآلية الصرف</h2>
<ol>
    <li>يعتمد الطرفان «جدول هيكل العمولات» المعتمد في النظام كجزء لا يتجزأ من العقد.</li>
    <li>تستحق العمولة عند إتمام الصفقة (توقيع العقود ودفع المقدم) وتحصيل الشركة لقيمة العمولة.</li>
    <li>في حال إلغاء العميل واسترداد المقدم قبل تحصيل الشركة للعمولة، لا تستحق عمولة.</li>
    <li>يتم الصرف خلال {{ config('freelance_agents.payout_days_min') }} إلى {{ config('freelance_agents.payout_days_max') }} يوم عمل من تحصيل العمولة.</li>
    @if($contract->quarterly_target_deals || $contract->quarterly_target_amount)
    <li>التارجت الربع سنوي المتفق عليه: @if($contract->quarterly_target_deals)<strong>{{ $contract->quarterly_target_deals }} صفقة</strong>@endif @if($contract->quarterly_target_amount)<strong>{{ number_format($contract->quarterly_target_amount) }} جنيه</strong>@endif — عند تحقيقه تُطبّق نسبة 50% لمبيعات المطورين (Primary).</li>
    @endif
</ol>

<h2>البند الخامس: سرية المعلومات وحظر المنافسة</h2>
<p>يتعهد الوكيل بالمحافظة على سرية بيانات العملاء وعدم تخطي الشركة أو إتمام صفقات مع عملاء التعرف عليهم عبر الشركة لحسابه الخاص. مخالفة ذلك تخوّل الشركة إلغاء العقد وحرمانه من العمولات والمطالبة قانونيًا.</p>

<h2>البند السادس: مدة العقد وفسخه</h2>
<p>مدة العقد سنة واحدة من <strong>{{ $contract->start_date->format('Y/m/d') }}</strong>@if($contract->end_date) إلى <strong>{{ $contract->end_date->format('Y/m/d') }}</strong>@endif وتتجدد تلقائيًا ما لم يُخطر أحد الطرفين قبل شهر. يحق الإنهاء بإخطار كتابي قبل 15 يومًا مع صرف عمولات العمليات المغلقة قبل الإنهاء.</p>

<h2>البند السابع: الاختصاص القضائي</h2>
<p>أي نزاع يكون من اختصاص المحكمة التابع لها مقر الشركة (الطرف الأول).</p>

<p style="margin-top:2rem;">تحرر هذا العقد من نسختين بيد كل طرف نسخة للعمل بموجبها.</p>

<div class="signatures">
    <div>
        <p><strong>توقيع الطرف الأول (الشركة)</strong></p>
        <div class="sig-box">الاسم: {{ $contract->company_signatory_name ?? '.......................' }}<br>الصفة: {{ $contract->company_signatory_title ?? '.......................' }}</div>
    </div>
    <div>
        <p><strong>توقيع الطرف الثاني (الوكيل)</strong></p>
        <div class="sig-box">الاسم: {{ $contract->user?->name }}<br>التوقيع: .......................</div>
    </div>
</div>
</body>
</html>
