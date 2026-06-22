

<?php $__env->startSection('title', \App\Helpers\SettingsHelper::getCompanyName() . ' - الرئيسية'); ?>

<?php $__env->startSection('content'); ?>
<?php
  $tc = \App\Helpers\SettingsHelper::getThemeColor();
  $cn = \App\Helpers\SettingsHelper::getCompanyName();
?>


<section class="relative overflow-hidden bg-gradient-to-b from-gray-50 to-white">
  <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
    
    <div class="absolute -top-32 right-0 w-96 h-96 rounded-full opacity-20 blur-3xl translate-x-1/3" style="background:<?php echo e($tc); ?>"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 rounded-full opacity-10 blur-3xl -translate-x-1/3" style="background:<?php echo e($tc); ?>"></div>
  </div>

  <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20 sm:pt-24 sm:pb-28">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-start">

      
      <div class="pt-4">
        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-gray-200 bg-white text-sm font-bold text-gray-700 shadow-sm">
          <span class="h-2 w-2 rounded-full" style="background:<?php echo e($tc); ?>"></span>
          Software &amp; AI Solutions
        </span>

        <div class="mt-6">
          <div class="text-xl sm:text-2xl font-extrabold font-cairo text-gray-900"><?php echo e($cn); ?></div>
          <h1 class="mt-3 text-3xl sm:text-4xl lg:text-5xl font-black font-cairo text-gray-900" style="line-height:1.2">
            <span class="block" style="color:<?php echo e($tc); ?>">نبني حلولاً برمجية</span>
            <span class="block mt-1">وذكاءً اصطناعياً للشركات.</span>
          </h1>
        </div>

        <p class="mt-5 text-base sm:text-lg text-gray-600 leading-relaxed">
          شركة متخصصة في تطوير البرمجيات وحلول الذكاء الاصطناعي.
          نصمّم أنظمة تشغيل، نبني بنى تحتية رقمية، ونُمكّن عملاءنا من التوسع بثقة.
        </p>

        <div class="mt-8 flex flex-col sm:flex-row gap-3">
          <a href="<?php echo e(route('website.contact')); ?>" class="inline-flex items-center justify-center px-7 py-3.5 rounded-2xl text-white font-extrabold btn-brand shadow-lg hover:shadow-xl transition-all">
            احجز جلسة استشارية
          </a>
          <a href="<?php echo e(route('website.services')); ?>" class="inline-flex items-center justify-center px-7 py-3.5 rounded-2xl border-2 border-gray-200 bg-white hover:bg-gray-50 font-bold text-gray-800 transition-all">
            استعرض خدماتنا
          </a>
        </div>

        <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-3">
          <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
            <div class="text-xl font-black font-cairo" style="color:<?php echo e($tc); ?>">+50</div>
            <div class="text-[11px] text-gray-500 mt-0.5">مشروع مُنجز</div>
          </div>
          <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
            <div class="text-xl font-black font-cairo" style="color:<?php echo e($tc); ?>">B2B</div>
            <div class="text-[11px] text-gray-500 mt-0.5">حلول للشركات</div>
          </div>
          <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
            <div class="text-xl font-black font-cairo" style="color:<?php echo e($tc); ?>">AI</div>
            <div class="text-[11px] text-gray-500 mt-0.5">ذكاء اصطناعي</div>
          </div>
          <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
            <div class="text-xl font-black font-cairo" style="color:<?php echo e($tc); ?>">SLA</div>
            <div class="text-[11px] text-gray-500 mt-0.5">مستويات خدمة</div>
          </div>
        </div>
      </div>

      
      <div class="relative">
        
        <div class="relative mx-auto max-w-xl">
          <div class="absolute inset-0 -z-10 pointer-events-none" aria-hidden="true">
            <div class="absolute -top-10 right-6 h-44 w-44 rounded-full blur-3xl opacity-20" style="background:<?php echo e($tc); ?>"></div>
          </div>

          <div class="grid grid-cols-12 gap-4">
            
            <div class="col-span-12 sm:col-span-6 rounded-3xl border border-gray-200 bg-white shadow-xl p-6 min-h-[170px] flex flex-col">
              <div class="text-xs font-bold mb-1" style="color:<?php echo e($tc); ?>">خدمات أساسية</div>
              <div class="text-sm font-extrabold text-gray-900">ERP • CRM • بوابة عميل</div>
              <div class="mt-4 space-y-2 text-[12px] text-gray-500 leading-relaxed flex-1">
                <div class="flex items-center gap-2">
                  <span class="h-2 w-2 rounded-full" style="background:<?php echo e($tc); ?>"></span>
                  إدارة المشاريع والمهام
                </div>
                <div class="flex items-center gap-2">
                  <span class="h-2 w-2 rounded-full" style="background:<?php echo e($tc); ?>"></span>
                  فواتير ومدفوعات وتقارير
                </div>
                <div class="flex items-center gap-2">
                  <span class="h-2 w-2 rounded-full" style="background:<?php echo e($tc); ?>"></span>
                  تذاكر ودعم ما بعد البيع
                </div>
              </div>
            </div>

            
            <div class="col-span-12 sm:col-span-6 rounded-3xl border border-gray-200 bg-white shadow-xl p-6 min-h-[170px] flex flex-col">
              <div class="flex items-center justify-between">
                <div class="text-xs font-bold" style="color:<?php echo e($tc); ?>">قيمة مضافة</div>
                <span class="text-[11px] px-2 py-0.5 rounded-full border border-gray-200 bg-gray-50 text-gray-600 font-bold">AI Insights</span>
              </div>
              <div class="mt-2 text-sm font-extrabold text-gray-900">قرارات أسرع… ببيانات أوضح</div>
              <div class="mt-3 h-2 rounded-full bg-gray-100 overflow-hidden">
                <div class="h-full w-[78%] rounded-full" style="background:<?php echo e($tc); ?>"></div>
              </div>
              <div class="mt-4 text-[12px] text-gray-500 leading-relaxed flex-1">لوحات مؤشرات، تنبيهات، وتنبؤات تشغيلية.</div>
              <div class="mt-3 flex items-center gap-2 text-[11px] text-gray-500">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border border-gray-200 bg-gray-50 font-bold">
                  <span class="h-2 w-2 rounded-full" style="background:<?php echo e($tc); ?>"></span>
                  Insights
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border border-gray-200 bg-gray-50 font-bold">
                  <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                  Alerts
                </span>
              </div>
            </div>

            
            <div class="col-span-12 rounded-3xl border border-gray-200 bg-white shadow-xl p-6 min-h-[138px]">
              <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-bold" style="color:<?php echo e($tc); ?>">بوابة العميل</div>
                <span class="text-[11px] px-2 py-0.5 rounded-full border border-gray-200 bg-white font-bold" style="color:<?php echo e($tc); ?>">Secure</span>
              </div>
              <div class="grid grid-cols-3 gap-3 text-center">
                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-3">
                  <div class="text-[11px] text-gray-500">فواتير</div>
                  <div class="mt-1 text-sm font-black font-cairo text-gray-900">عرض</div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-3">
                  <div class="text-[11px] text-gray-500">تذاكر</div>
                  <div class="mt-1 text-sm font-black font-cairo text-gray-900">رفع</div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-3">
                  <div class="text-[11px] text-gray-500">تقارير</div>
                  <div class="mt-1 text-sm font-black font-cairo text-gray-900">متابعة</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>


<section class="border-y border-gray-100 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col items-center justify-center gap-4 sm:gap-6 text-center">
      <span class="text-sm text-gray-400 font-bold">نفتخر بثقة عملائنا</span>
      <div class="grid grid-cols-4 gap-4 sm:gap-8 w-full sm:w-auto">
        <div class="text-center">
          <div class="text-xl sm:text-2xl font-black font-cairo" style="color:<?php echo e($tc); ?>">+50</div>
          <div class="text-[11px] sm:text-xs text-gray-400">مشروع</div>
        </div>
        <div class="text-center">
          <div class="text-xl sm:text-2xl font-black font-cairo" style="color:<?php echo e($tc); ?>">+30</div>
          <div class="text-[11px] sm:text-xs text-gray-400">عميل</div>
        </div>
        <div class="text-center">
          <div class="text-xl sm:text-2xl font-black font-cairo" style="color:<?php echo e($tc); ?>">24/7</div>
          <div class="text-[11px] sm:text-xs text-gray-400">دعم</div>
        </div>
        <div class="text-center">
          <div class="text-xl sm:text-2xl font-black font-cairo" style="color:<?php echo e($tc); ?>">99%</div>
          <div class="text-[11px] sm:text-xs text-gray-400">رضا</div>
        </div>
      </div>
    </div>
  </div>
</section>


<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
  <div class="text-center mb-14">
    <h2 class="text-3xl sm:text-4xl font-black font-cairo text-gray-900">ماذا نبني للشركات؟</h2>
    <p class="mt-4 text-gray-600 max-w-xl mx-auto">حلول متكاملة — من التصميم إلى التشغيل إلى خدمة ما بعد البيع.</p>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php
      $cards = [
        ['icon'=>'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z','title'=>'أنظمة تشغيل مخصصة','desc'=>'نبني أنظمة ERP/CRM مصممة حول احتياجات شركتك — ليست "جاهزة" بل مبنية لك.'],
        ['icon'=>'M13 10V3L4 14h7v7l9-11h-7z','title'=>'حلول ذكاء اصطناعي','desc'=>'نماذج AI مدمجة في عمليات الشركة: تحليل بيانات، تنبؤات، أتمتة قرارات.'],
        ['icon'=>'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z','title'=>'حوكمة وصلاحيات','desc'=>'أدوار، سياسات وصول، مسارات اعتمادات، وتوثيق كامل لكل حركة.'],
        ['icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5','title'=>'بنية تحتية رقمية','desc'=>'بيئة Cloud جاهزة للنمو: خوادم، قواعد بيانات، CI/CD، ومراقبة مستمرة.'],
        ['icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','title'=>'بوابة العميل','desc'=>'عميلك يدخل يشوف مشاريعه، فواتيره، وتذاكر الدعم — بصلاحيات آمنة.'],
        ['icon'=>'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z','title'=>'ما بعد البيع','desc'=>'تذاكر أعطال، تعيين لأقسام، تقارير للعميل، وSLA واضح.'],
      ];
    ?>

    <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bg-white rounded-2xl border border-gray-200 p-7 hover:shadow-xl hover:border-gray-300 transition-all duration-300 group">
      <div class="h-12 w-12 rounded-xl flex items-center justify-center mb-5" style="background: color-mix(in srgb, <?php echo e($tc); ?> 12%, transparent)">
        <svg class="h-6 w-6" style="color:<?php echo e($tc); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($c['icon']); ?>" />
        </svg>
      </div>
      <h3 class="text-lg font-extrabold text-gray-900 mb-2"><?php echo e($c['title']); ?></h3>
      <p class="text-sm text-gray-600 leading-relaxed"><?php echo e($c['desc']); ?></p>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
</section>


<section class="bg-gray-50 border-y border-gray-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="text-center mb-14">
      <h2 class="text-3xl sm:text-4xl font-black font-cairo text-gray-900">لماذا <?php echo e($cn); ?>؟</h2>
      <p class="mt-4 text-gray-600 max-w-2xl mx-auto leading-relaxed">
        لأننا لا “نطوّر شاشة” أو “نركّب نظام” — نحن نبني المؤسسة رقميًا من الداخل: نحول التشغيل إلى إجراءات واضحة، بيانات قابلة للقياس، ومسارات عمل تتكرر بدون فوضى.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center hover:shadow-lg transition-all">
        <div class="mx-auto h-16 w-16 rounded-2xl flex items-center justify-center mb-6" style="background: color-mix(in srgb, <?php echo e($tc); ?> 12%, transparent)">
          <svg class="h-8 w-8" style="color:<?php echo e($tc); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
        </div>
        <h3 class="text-xl font-extrabold text-gray-900 mb-3">نبني التشغيل (Operating Model)</h3>
        <p class="text-sm text-gray-600 leading-relaxed">
          نوثق الإجراءات، نحدد المسؤوليات، ونبني مسارات عمل واعتمادات (Workflow) بحيث يتحول التشغيل اليومي إلى نظام واضح ومحاسبي.
        </p>
      </div>

      <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center hover:shadow-lg transition-all">
        <div class="mx-auto h-16 w-16 rounded-2xl flex items-center justify-center mb-6" style="background: color-mix(in srgb, <?php echo e($tc); ?> 12%, transparent)">
          <svg class="h-8 w-8" style="color:<?php echo e($tc); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
          </svg>
        </div>
        <h3 class="text-xl font-extrabold text-gray-900 mb-3">نربط الأقسام والبيانات</h3>
        <p class="text-sm text-gray-600 leading-relaxed">
          توحيد البيانات بين الأقسام، صلاحيات دقيقة، تقارير لحظية، وتكاملات مع الأنظمة الحالية — حتى لا تبقى القرارات “تقديرية”.
        </p>
      </div>

      <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center hover:shadow-lg transition-all">
        <div class="mx-auto h-16 w-16 rounded-2xl flex items-center justify-center mb-6" style="background: color-mix(in srgb, <?php echo e($tc); ?> 12%, transparent)">
          <svg class="h-8 w-8" style="color:<?php echo e($tc); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        <h3 class="text-xl font-extrabold text-gray-900 mb-3">نؤمّن الاستمرارية والنتائج</h3>
        <p class="text-sm text-gray-600 leading-relaxed">
          إطلاق مرحلي + تدريب + دعم ما بعد البيع (SLA) + قياس أداء. هدفنا: نظام يعيش معك ويتوسع… وليس “تسليم ووداع”.
        </p>
      </div>
    </div>
  </div>
</section>


<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
  <div class="text-center mb-14">
    <h2 class="text-3xl sm:text-4xl font-black font-cairo text-gray-900">كيف نعمل؟</h2>
    <p class="mt-4 text-gray-600 max-w-lg mx-auto">أربع مراحل واضحة — من الفهم إلى الإطلاق والتحسين المستمر.</p>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <?php
      $steps = [
        ['n'=>'01','title'=>'الاكتشاف','desc'=>'نفهم شركتك، أقسامها، إجراءاتها، ونقاط الألم.'],
        ['n'=>'02','title'=>'التصميم','desc'=>'نصمم البنية: صلاحيات، تدفق بيانات، مسارات اعتمادات.'],
        ['n'=>'03','title'=>'التنفيذ','desc'=>'تطوير تدريجي، اختبارات، وإطلاق مرحلي مع تدريب الفرق.'],
        ['n'=>'04','title'=>'التحسين','desc'=>'مراقبة أداء، تقارير، وتحسينات دورية بناءً على البيانات.'],
      ];
    ?>

    <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="relative bg-white rounded-2xl border border-gray-200 p-7 hover:shadow-lg transition-all">
      <div class="text-5xl font-black font-cairo opacity-10 absolute top-4 left-5" style="color:<?php echo e($tc); ?>"><?php echo e($s['n']); ?></div>
      <div class="relative pt-8">
        <h3 class="text-lg font-extrabold text-gray-900 mb-2"><?php echo e($s['title']); ?></h3>
        <p class="text-sm text-gray-600 leading-relaxed"><?php echo e($s['desc']); ?></p>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
</section>


<section class="bg-gray-50 border-y border-gray-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="text-center mb-14">
      <h2 class="text-3xl sm:text-4xl font-black font-cairo text-gray-900">القطاعات التي نخدمها</h2>
      <p class="mt-4 text-gray-600 max-w-lg mx-auto">خبرة عميقة في قطاعات متعددة — بحلول مخصصة لكل قطاع.</p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
      <?php
        $sectors = [
          ['icon'=>'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z','name'=>'تقنية المعلومات'],
          ['icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5','name'=>'المقاولات'],
          ['icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1','name'=>'المالية'],
          ['icon'=>'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z','name'=>'التجارة'],
          ['icon'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z','name'=>'الصحة'],
          ['icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253','name'=>'التعليم'],
        ];
      ?>

      <?php $__currentLoopData = $sectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="bg-white rounded-2xl border border-gray-200 p-5 text-center hover:shadow-lg transition-all group">
        <div class="mx-auto h-12 w-12 rounded-xl flex items-center justify-center mb-3" style="background: color-mix(in srgb, <?php echo e($tc); ?> 10%, transparent)">
          <svg class="h-6 w-6 text-gray-500 group-hover:text-gray-900 transition-colors" style="color:<?php echo e($tc); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($sec['icon']); ?>" />
          </svg>
        </div>
        <div class="text-sm font-bold text-gray-900"><?php echo e($sec['name']); ?></div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
</section>


<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
    <div>
      <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold border border-gray-200 bg-white mb-6" style="color:<?php echo e($tc); ?>">
        بوابة العملاء
      </span>
      <h2 class="text-3xl sm:text-4xl font-black font-cairo text-gray-900" style="line-height:1.25">
        في Solvesta
        <span style="color:<?php echo e($tc); ?>">العميل أولاً.</span>
      </h2>
      <p class="mt-5 text-gray-600 leading-relaxed">
        نحن لا نُسلّم مشروعًا ثم نختفي — نهتم بالعميل قبل التنفيذ وأثناءه وبعده.
        تواصل واضح، متابعة مستمرة للتقدم، ودعم سريع عند أي عطل مع التزام بمستويات الخدمة (SLA) وتقارير مهنية.
      </p>
      <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-4">
          <div class="text-xs font-bold mb-1" style="color:<?php echo e($tc); ?>">شفافية كاملة</div>
          <div class="text-sm font-extrabold text-gray-900">كل الخدمات والإنجازات في مكان واحد</div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4">
          <div class="text-xs font-bold mb-1" style="color:<?php echo e($tc); ?>">دعم أسرع</div>
          <div class="text-sm font-extrabold text-gray-900">تذاكر أعطال + متابعة + تقارير</div>
        </div>
      </div>
      <a href="<?php echo e(route('client.login')); ?>" class="mt-8 inline-flex px-6 py-3 rounded-2xl text-white font-extrabold btn-brand shadow-lg hover:shadow-xl transition-all">
        جرّب بوابة العملاء
      </a>
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <div class="text-xs text-gray-500 mb-2">المشاريع</div>
        <div class="text-sm font-extrabold text-gray-900 mb-1">متابعة مباشرة</div>
        <p class="text-xs text-gray-500 leading-relaxed">تقدم المشروع، المراحل، والحالة الحالية.</p>
      </div>
      <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <div class="text-xs text-gray-500 mb-2">الفواتير</div>
        <div class="text-sm font-extrabold text-gray-900 mb-1">شفافية مالية</div>
        <p class="text-xs text-gray-500 leading-relaxed">جميع الفواتير، المدفوع والمتبقي.</p>
      </div>
      <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <div class="text-xs text-gray-500 mb-2">تذاكر الدعم</div>
        <div class="text-sm font-extrabold text-gray-900 mb-1">تواصل منظم</div>
        <p class="text-xs text-gray-500 leading-relaxed">يرفع العطل ويتابع الحل خطوة بخطوة.</p>
      </div>
      <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <div class="text-xs text-gray-500 mb-2">الأمان</div>
        <div class="text-sm font-extrabold text-gray-900 mb-1">حساب مستقل</div>
        <p class="text-xs text-gray-500 leading-relaxed">Guard منفصل — لا يرى بيانات غيره.</p>
      </div>
    </div>
  </div>
</section>


<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
  <div class="rounded-3xl bg-gray-950 p-8 sm:p-14 text-white overflow-hidden relative">
    <div class="absolute -top-20 right-0 w-72 h-72 rounded-full opacity-25 blur-3xl" style="background:<?php echo e($tc); ?>"></div>

    <div class="relative text-center max-w-2xl mx-auto">
      <h2 class="text-3xl sm:text-4xl font-black font-cairo" style="line-height:1.25">جاهز نبني مع شركتك؟</h2>
      <p class="mt-4 text-gray-300 leading-relaxed">
        رتّب جلسة تعريف مجانية — نفهم احتياجاتك ونرسم خارطة طريق التنفيذ معك.
      </p>
      <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="<?php echo e(route('website.contact')); ?>" class="px-8 py-4 rounded-2xl text-white font-extrabold btn-brand shadow-lg hover:shadow-xl transition-all">
          احجز جلسة الآن
        </a>
        <a href="<?php echo e(route('client.login')); ?>" class="px-8 py-4 rounded-2xl border border-white/20 bg-white/10 hover:bg-white/15 font-bold transition-all">
          بوابة العملاء
        </a>
      </div>
    </div>
  </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('website.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\website\home.blade.php ENDPATH**/ ?>