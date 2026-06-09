<?php $__env->startSection('title', \App\Helpers\SettingsHelper::getCompanyName() . ' - حلول الشركات'); ?>

<?php $__env->startSection('content'); ?>
<?php
  $tc = \App\Helpers\SettingsHelper::getThemeColor();
  $cn = \App\Helpers\SettingsHelper::getCompanyName();
?>


<section class="relative overflow-hidden bg-gradient-to-b from-gray-50 to-white border-b border-gray-100">
  <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
    <div class="absolute -top-24 right-0 h-72 w-72 rounded-full blur-3xl opacity-15 translate-x-1/3" style="background: <?php echo e($tc); ?>"></div>
    <div class="absolute bottom-0 left-0 h-72 w-72 rounded-full blur-3xl opacity-10 -translate-x-1/3" style="background: <?php echo e($tc); ?>"></div>
  </div>

  <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
    <div class="max-w-3xl">
      <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-gray-200 bg-white text-sm font-bold text-gray-700 shadow-sm">
        <span class="h-2 w-2 rounded-full" style="background: <?php echo e($tc); ?>"></span>
        Enterprise B2B
      </span>
      <h1 class="mt-6 text-3xl sm:text-4xl lg:text-5xl font-black font-cairo text-gray-900" style="line-height:1.2">
        حلول الشركات — بناء مؤسسة رقمية قابلة للتوسع
      </h1>
      <p class="mt-5 text-lg text-gray-600 leading-relaxed max-w-2xl">
        في <?php echo e($cn); ?> لا نبيع “باقة أسعار”. نحدد نطاقًا واضحًا ثم نبني منظومة تشغيل: إجراءات، صلاحيات، بيانات، تكاملات، ودعم ما بعد البيع — بنتائج قابلة للقياس.
      </p>

      <div class="mt-8 flex flex-col sm:flex-row gap-3">
        <a href="<?php echo e(route('website.contact')); ?>" class="inline-flex items-center justify-center px-7 py-3.5 rounded-2xl text-white font-extrabold btn-brand shadow-lg hover:shadow-xl transition">
          احجز جلسة
        </a>
        <a href="<?php echo e(route('website.case-studies.index')); ?>" class="inline-flex items-center justify-center px-7 py-3.5 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 font-bold text-gray-800 transition">
          نماذج الأعمال
        </a>
      </div>
    </div>
  </div>
</section>


<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
  <div class="text-center mb-14">
    <h2 class="text-3xl sm:text-4xl font-black font-cairo text-gray-900">مسارات الحلول</h2>
    <p class="mt-4 text-gray-600 max-w-2xl mx-auto leading-relaxed">
      نبدأ من “الأساسيات” التي تثبّت التشغيل والبيانات، ثم نضيف طبقات التكامل والأتمتة والذكاء الاصطناعي حسب الأولويات.
    </p>
  </div>

  <?php
    $tracks = [
      ['title'=>'منظومة تشغيل (ERP/CRM)','desc'=>'مشاريع + مهام + فرق + اعتمادات + تقارير تشغيلية.'],
      ['title'=>'مالية وتحصيل','desc'=>'فواتير + دفعات + أرصدة + تقارير + متابعة متأخرات.'],
      ['title'=>'بوابة العميل','desc'=>'وصول آمن لعميلك لمتابعة المشاريع والفواتير والدعم.'],
      ['title'=>'ما بعد البيع (Support + SLA)','desc'=>'تذاكر منظمة، تعيين للأقسام، تقارير حتى الإغلاق.'],
      ['title'=>'حوكمة وصلاحيات','desc'=>'RBAC + Audit + سياسات وصول ومسارات قرار.'],
      ['title'=>'AI داخل النظام','desc'=>'تصنيف + تلخيص + تنبؤ + تنبيهات استباقية.'],
    ];
  ?>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php $__currentLoopData = $tracks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="bg-white rounded-2xl border border-gray-200 p-7 hover:shadow-xl hover:border-gray-300 transition-all">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center mb-5" style="background: color-mix(in srgb, <?php echo e($tc); ?> 12%, transparent)">
          <div class="h-2.5 w-2.5 rounded-full" style="background: <?php echo e($tc); ?>"></div>
        </div>
        <h3 class="text-lg font-extrabold text-gray-900 mb-2"><?php echo e($t['title']); ?></h3>
        <p class="text-sm text-gray-600 leading-relaxed"><?php echo e($t['desc']); ?></p>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
</section>


<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
    <div class="bg-white rounded-3xl border border-gray-200 p-8">
      <h2 class="text-2xl font-black font-cairo text-gray-900 mb-3">كيف نبني البنية التحتية التكنولوجية؟</h2>
      <p class="text-gray-600 leading-relaxed">
        البنية ليست “سيرفر فقط”. هي مزيج من <span class="font-bold text-gray-900">الهوية والصلاحيات</span> + <span class="font-bold text-gray-900">نموذج بيانات</span> +
        <span class="font-bold text-gray-900">تكاملات</span> + <span class="font-bold text-gray-900">تشغيل ومراقبة</span> + <span class="font-bold text-gray-900">حوكمة</span>.
        هدفنا: نظام يعيش سنوات، يتوسع مع الشركة، ويعطي الإدارة رؤية تشغيلية دقيقة.
      </p>

      <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div class="rounded-2xl border border-gray-200 p-5">
          <div class="font-extrabold text-gray-900 mb-1">Identity &amp; Access</div>
          <div class="text-gray-600">RBAC، صلاحيات دقيقة، سجل تدقيق (Audit).</div>
        </div>
        <div class="rounded-2xl border border-gray-200 p-5">
          <div class="font-extrabold text-gray-900 mb-1">Data Model</div>
          <div class="text-gray-600">تعريف “مصدر الحقيقة” للعميل/المشروع/الفاتورة/التذكرة.</div>
        </div>
        <div class="rounded-2xl border border-gray-200 p-5">
          <div class="font-extrabold text-gray-900 mb-1">Integrations</div>
          <div class="text-gray-600">APIs + Webhooks + ربط WhatsApp/Email/ERP/CRM.</div>
        </div>
        <div class="rounded-2xl border border-gray-200 p-5">
          <div class="font-extrabold text-gray-900 mb-1">Operations</div>
          <div class="text-gray-600">Monitoring، نسخ احتياطي، سياسات نشر وتحديث.</div>
        </div>
      </div>
    </div>

    <div class="rounded-3xl border border-gray-200 bg-gray-50 p-8">
      <h3 class="text-xl font-black font-cairo text-gray-900 mb-4">مخرجات واضحة (Deliverables) من أول أسبوع</h3>
      <div class="space-y-3 text-sm text-gray-700 leading-relaxed">
        <div class="flex items-start gap-2">
          <span class="mt-1 h-2 w-2 rounded-full" style="background: <?php echo e($tc); ?>"></span>
          <div><span class="font-bold">خارطة نظم (System Map):</span> ما الذي نُبقيه؟ ما الذي ندمجه؟ وما الذي نبنيه؟</div>
        </div>
        <div class="flex items-start gap-2">
          <span class="mt-1 h-2 w-2 rounded-full" style="background: <?php echo e($tc); ?>"></span>
          <div><span class="font-bold">نموذج بيانات مُبسّط:</span> تعريف الكيانات الأساسية وعلاقاتها (Client/Project/Invoice/Ticket).</div>
        </div>
        <div class="flex items-start gap-2">
          <span class="mt-1 h-2 w-2 rounded-full" style="background: <?php echo e($tc); ?>"></span>
          <div><span class="font-bold">صلاحيات وسياسات:</span> من يرى ماذا؟ ومن يعتمد ماذا؟ مع مسارات قرار واقعية.</div>
        </div>
        <div class="flex items-start gap-2">
          <span class="mt-1 h-2 w-2 rounded-full" style="background: <?php echo e($tc); ?>"></span>
          <div><span class="font-bold">لوحة KPIs أولية:</span> مؤشرات تشغيل/مالية/دعم لتبدأ القياس مبكرًا.</div>
        </div>
      </div>
    </div>
  </div>
</section>


<section class="bg-gray-50 border-y border-gray-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
      <div class="bg-white rounded-3xl border border-gray-200 p-8">
        <h2 class="text-2xl font-black font-cairo text-gray-900 mb-4">منهجية التنفيذ</h2>
        <div class="space-y-3 text-sm text-gray-700 leading-relaxed">
          <div class="flex items-start gap-2">
            <span class="mt-1 h-2 w-2 rounded-full" style="background: <?php echo e($tc); ?>"></span>
            <div><span class="font-bold">اكتشاف وتشخيص:</span> مقابلات سريعة + تحليل الإجراءات + تحديد الاختناقات (وقت/تكلفة/مخاطر).</div>
          </div>
          <div class="flex items-start gap-2">
            <span class="mt-1 h-2 w-2 rounded-full" style="background: <?php echo e($tc); ?>"></span>
            <div><span class="font-bold">تصميم معماري وتشغيلي:</span> Domain Model + RBAC + مواصفات تكامل + خطة بيانات (ترحيل/تنظيف).</div>
          </div>
          <div class="flex items-start gap-2">
            <span class="mt-1 h-2 w-2 rounded-full" style="background: <?php echo e($tc); ?>"></span>
            <div><span class="font-bold">تنفيذ وإطلاق مرحلي:</span> MVP مُستخدم فعليًا ثم توسعة في دورات (Sprints) مع تدريب وقياس.</div>
          </div>
          <div class="flex items-start gap-2">
            <span class="mt-1 h-2 w-2 rounded-full" style="background: <?php echo e($tc); ?>"></span>
            <div><span class="font-bold">تشغيل وتحسين:</span> Monitoring + SLA + تقارير دورية للإدارة + تحسينات على أساس البيانات.</div>
          </div>
        </div>
      </div>

      <div class="rounded-3xl border border-gray-200 bg-gray-950 p-8 text-white relative overflow-hidden">
        <div class="absolute -top-16 right-0 h-56 w-56 rounded-full blur-3xl opacity-25" style="background: <?php echo e($tc); ?>"></div>
        <div class="relative">
          <h2 class="text-2xl font-black font-cairo mb-4">نتعهد بالاستمرارية</h2>
          <p class="text-gray-200 leading-relaxed">
            دعم ما بعد البيع ليس “إضافة” — هو جزء من البناء. تذاكر منظمة، تعيين للأقسام، تقارير، ومؤشرات SLA.
          </p>
          <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
              <div class="text-sm font-extrabold">SLA واضح</div>
              <div class="mt-1 text-sm text-gray-300">وقت استجابة + تتبع حالة</div>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
              <div class="text-sm font-extrabold">تقارير مهنية</div>
              <div class="mt-1 text-sm text-gray-300">حتى إغلاق التذكرة</div>
            </div>
          </div>
          <div class="mt-8 flex flex-col sm:flex-row gap-3">
            <a href="<?php echo e(route('website.contact')); ?>" class="inline-flex w-full justify-center px-6 py-3 rounded-2xl text-white font-extrabold btn-brand shadow-md hover:shadow-lg transition">
              اطلب جلسة تعريف
            </a>
            <a href="<?php echo e(route('website.services')); ?>" class="inline-flex w-full justify-center px-6 py-3 rounded-2xl border border-white/20 bg-white/10 hover:bg-white/15 font-bold transition">
              استعرض الخدمات
            </a>
          </div>
          <p class="mt-4 text-xs text-gray-400">التنفيذ والتكلفة حسب نطاق شركتك واحتياجاتها.</p>
        </div>
      </div>
    </div>
  </div>
</section>


<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
  <div class="text-center mb-14">
    <h2 class="text-3xl sm:text-4xl font-black font-cairo text-gray-900">أمثلة عملية لحلول الشركات</h2>
    <p class="mt-4 text-gray-600 max-w-2xl mx-auto leading-relaxed">أفكار جاهزة تُنفّذ كمنظومة—ليست “صفحات” فقط.</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php
      $examples = [
        ['t'=>'مسارات اعتمادات','d'=>'اعتماد مشتريات/مصروفات/تغييرات نطاق المشروع مع سجل قرار واضح.'],
        ['t'=>'بوابة عميل B2B','d'=>'متابعة التقدم + التقارير + الفواتير + فتح تذكرة خلال ثوانٍ مع SLA.'],
        ['t'=>'إغلاق الفجوة بين التشغيل والمالية','d'=>'ربط المشروع بالمصروفات والتحصيل لقراءة الربحية لحظيًا.'],
        ['t'=>'تكاملات تنبيهات حرجة','d'=>'WhatsApp/Email للإنذارات والاعتمادات وتحديثات الحالة.'],
        ['t'=>'حوكمة وأمان','d'=>'RBAC، Audit، سياسات وصول حسب القسم/المشروع، مع تقارير امتثال.'],
        ['t'=>'AI للقرارات السريعة','d'=>'تلخيص تحديثات الفريق، تصنيف التذاكر، تنبيه استباقي للتأخير.'],
      ];
    ?>

    <?php $__currentLoopData = $examples; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="bg-white rounded-2xl border border-gray-200 p-7 hover:shadow-xl hover:border-gray-300 transition-all">
        <div class="text-lg font-extrabold text-gray-900 mb-2"><?php echo e($e['t']); ?></div>
        <div class="text-sm text-gray-600 leading-relaxed"><?php echo e($e['d']); ?></div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
</section>


<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-2xl border border-gray-200 p-7">
      <div class="text-sm font-extrabold text-gray-900 mb-2">مناسب لـ</div>
      <div class="text-sm text-gray-600 leading-relaxed">الشركات التي تريد تحويل التشغيل إلى منظومة قابلة للقياس: مشاريع + مالية + دعم + حوكمة.</div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-7">
      <div class="text-sm font-extrabold text-gray-900 mb-2">النتيجة</div>
      <div class="text-sm text-gray-600 leading-relaxed">رؤية للإدارة، مسارات قرار واضحة، وتقليل العمل اليدوي مع تكاملات وتنبيهات.</div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-7">
      <div class="text-sm font-extrabold text-gray-900 mb-2">الخطوة التالية</div>
      <div class="text-sm text-gray-600 leading-relaxed">جلسة تعريف + تحديد نطاق + خارطة طريق تنفيذ مرحلي بمخرجات واضحة لكل مرحلة.</div>
    </div>
  </div>
</section>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('website.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\website\pricing.blade.php ENDPATH**/ ?>