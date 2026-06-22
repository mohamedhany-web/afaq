<?php $__env->startSection('title', \App\Helpers\SettingsHelper::getCompanyName() . ' - الخدمات'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
  <div class="text-center mb-10">
    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-gray-200 bg-white text-sm font-bold text-gray-700 shadow-sm">
      <span class="h-2 w-2 rounded-full" style="background: <?php echo e($themeColor); ?>"></span>
      Software &amp; AI — للشركات والمنظمات
    </span>
    <h1 class="mt-5 text-3xl sm:text-4xl font-black font-cairo text-gray-900">خدمات البرمجيات والذكاء الاصطناعي</h1>
    <p class="mt-3 text-gray-600 text-lg max-w-3xl mx-auto leading-relaxed">نعمل مع <strong class="text-gray-800">الشركات والمؤسسات والجهات</strong> التي تحتاج سيادة على بياناتها، تشغيلاً موثوقاً، واتفاقيات مستوى خدمة واضحة — لا منتجات «استهلاكية» سريعة الزوال.</p>
  </div>

  
  <div class="mb-14 rounded-2xl border-2 border-gray-200 bg-gradient-to-br from-gray-50 via-white to-gray-50/80 p-6 sm:p-10 shadow-sm">
    <div class="text-right max-w-4xl ms-auto">
      <h2 class="text-2xl sm:text-3xl font-black font-cairo text-gray-900">حلول برمجية وذكاء اصطناعي مؤسسية</h2>
      <p class="mt-4 text-gray-700 leading-relaxed">نغطي دورة الحل كاملةً: من <strong>استشارات التحول والتصميم المعماري</strong> إلى <strong>التطوير والتكامل والتشغيل</strong>، ثم <strong>طبقات الذكاء الاصطناعي والبيانات</strong> حيث تعود على الكفاءة والامتثال والقرار — مع التزام بـ<strong>الخصوصية، الصلاحيات، وسجل التدقيق</strong> كما تتطلبه المنظمات.</p>
      <p class="mt-3 text-gray-700 leading-relaxed">ننفّذ بأسلوب <strong>إطلاق مرحلي قابل للقياس</strong> (Roadmap واضح، نماذج أولية، إنتاج تدريجي)، ونوثّق المخرجات، وندرب الفرق، ونوفر <strong>دعماً تشغيلياً وSLA</strong> بحيث يصبح النظام جزءاً من «طريقة عملكم» وليس مشروعاً معزولاً.</p>
    </div>

    <?php
      $engCards = [
        ['title'=>'استشارات التحول الرقمي والهندسة المعمارية','desc'=>'تشخيص واقعي، خرائط طريق، تصميم عالي/منخفض المستوى (HLD/LLD)، ومعايير تكامل تلائم سياساتكم وهيكلكم التنظيمي.','icon'=>'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z'],
        ['title'=>'تطوير تطبيقات مؤسسية (ويب، بوابات، موبايل ميداني)','desc'=>'واجهات للموظفين والشركاء والعملاء، أداء تحت حمل العمل، تجربة استخدام واضحة، ودعم أجهزة وفِرق ميدانية عند الحاجة.','icon'=>'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ['title'=>'واجهات برمجية (APIs) وتكامل الأنظمة','desc'=>'ربط ERP/CRM/مالية/مخزون/بوابات دفع وخدمات خارجية؛ تقليل الإدخال المكرر، تدفق بيانات موحّد، ومراقبة للأعطال بين الأنظمة.','icon'=>'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1'],
        ['title'=>'حداثة الأنظمة وترحيل البيانات','desc'=>'الخروج من الورق وExcel والتطبيقات «الهشة» بترحيل منضبط، تنظيف بيانات، ومزامنة مرحلية حتى لا يتوقف التشغيل الحرج.','icon'=>'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
        ['title'=>'بنية تحتية سحابية، DevOps، ومراقبة','desc'=>'بيئات إنتاج واختبار، نشر آلي (CI/CD)، نسخ احتياطي، توسّع، مراقبة لحظية، وسياسات استرداد تلائم حجم منظمتكم.','icon'=>'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01'],
        ['title'=>'أمن معلومات، صلاحيات، وامتثال تشغيلي','desc'=>'أدوار وتفويض، مسارات اعتماد، تشفير ومسارات وصول، تدقيق وسجلات؛ بما يدعم متطلبات الإدارات والمراجعين والشركاء.','icon'=>'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
      ];
      $aiCards = [
        ['title'=>'استراتيجية وحوكمة الذكاء الاصطناعي','desc'=>'اختيار حالات استخدام ذات عائد، سياسات بيانات التدريب والاستخدام، تفسير النتائج حيث يلزم، وحدود آمنة للنماذج داخل منظمتكم.','icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
        ['title'=>'ذكاء المستندات ومعالجة اللغة (NLP / OCR)','desc'=>'استخراج حقول من عقود وفواتير وطلبات، تصنيف وفهرسة، ومطابقة مع أنظمتكم لتقليل العمل اليدوي والأخطاء البشرية.','icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['title'=>'مساعدين ذكيين للموظفين (Enterprise Copilots)','desc'=>'مساعد يعمل فوق بياناتكم وسياساتكم: إجابات مرجعية، مسودات، ملخصات، وإرشاد إجرائي — وليس محادثة عامة منفصلة عن العمل.','icon'=>'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
        ['title'=>'تنبؤات، تصنيف، ومؤشرات ذكية على بياناتكم','desc'=>'نماذج للطلب، المخاطر، الجودة، أو التشغيل؛ مدمجة في لوحاتكم ومسارات العمل وليس «تقارير معزولة».','icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-4 0a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v12a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        ['title'=>'ذكاء الدعم والخدمة: تذاكر، أولوية، روبوتات مرتبطة بالسياسات','desc'=>'تصنيف تلقائي، توجيه للفريق المناسب، إجابات أولية مضبوطة، وتحسين مستمر بمقاييس رضا وزمن حل — بما يتماشى مع SLA المؤسسي.','icon'=>'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
        ['title'=>'منصات بيانات، BI، وتقارير تنفيذية','desc'=>'مستودعات بيانات، نماذج قياس موحّدة، لوحات للإدارة العليا، وتصدير لأنظمة الحوكمة — قرار واحد من مصدر واحد للحقيقة.','icon'=>'M13 10V3L4 14h7v7l9-11h-7z'],
      ];
    ?>

    <h3 class="mt-10 text-xl font-black font-cairo text-gray-900 text-right border-t border-gray-200 pt-8">الهندسة البرمجية، التكامل، والتشغيل المؤسسي</h3>
    <p class="mt-2 text-sm text-gray-600 text-right max-w-3xl ms-auto leading-relaxed">ما تحتاجه المنظمات لبناء وامتلاك البرمجيات: من الفكرة إلى الإنتاج مع ضبط الجودة والأمن والاستمرارية.</p>
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php $__currentLoopData = $engCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-2xl border border-gray-200 p-6 hover:shadow-lg transition text-right">
          <div class="h-12 w-12 rounded-xl flex items-center justify-center mb-4" style="background: color-mix(in srgb, <?php echo e($themeColor); ?> 15%, transparent)">
            <svg class="h-6 w-6" style="color: <?php echo e($themeColor); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($c['icon']); ?>" />
            </svg>
          </div>
          <h4 class="text-lg font-extrabold text-gray-900"><?php echo e($c['title']); ?></h4>
          <p class="mt-2 text-sm text-gray-600 leading-relaxed"><?php echo e($c['desc']); ?></p>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <h3 class="mt-12 text-xl font-black font-cairo text-gray-900 text-right border-t border-gray-200 pt-8">الذكاء الاصطناعي، البيانات، ودعم القرار المؤسسي</h3>
    <p class="mt-2 text-sm text-gray-600 text-right max-w-3xl ms-auto leading-relaxed">طبقة ذكاء تُبنى فوق بياناتكم وعملياتكم: شفافة الحدود، قابلة للمراجعة، ومربوطة بمؤشرات أداء حقيقية.</p>
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php $__currentLoopData = $aiCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-2xl border border-gray-200 p-6 hover:shadow-lg transition text-right">
          <div class="h-12 w-12 rounded-xl flex items-center justify-center mb-4" style="background: color-mix(in srgb, <?php echo e($themeColor); ?> 15%, transparent)">
            <svg class="h-6 w-6" style="color: <?php echo e($themeColor); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($c['icon']); ?>" />
            </svg>
          </div>
          <h4 class="text-lg font-extrabold text-gray-900"><?php echo e($c['title']); ?></h4>
          <p class="mt-2 text-sm text-gray-600 leading-relaxed"><?php echo e($c['desc']); ?></p>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="mt-10 rounded-xl border border-gray-200 bg-white/80 p-5 sm:p-6 text-right">
      <p class="text-sm text-gray-700 leading-relaxed"><strong class="text-gray-900">التزامنا تجاه الشركات والمنظمات:</strong> وثائق فنية وتشغيلية، تدريب فرقكم، قنوات دعم واضحة، وخيارات صيانة وتطوير مستمر — حتى يبقى الحل قابلاً للتدقيق والتوسع مع نموكم.</p>
    </div>
  </div>

  
  <div class="mb-14">
    <h2 class="text-2xl font-extrabold font-cairo text-gray-900 text-center mb-2">كيف نضع الشركات «التقليدية» على الطريق الرقمي؟</h2>
    <p class="text-center text-gray-600 max-w-2xl mx-auto mb-8">إذا كانت العملية تعتمد على الذاكرة الشخصية، الدفاتر، الملفات المتناثرة، ولا يوجد نظام موحّد — فهذا ليس فشلاً، بل نقطة بداية شائعة. «إعادة البناء» هنا تعني <strong class="text-gray-800">ترتيب الطبقات خطوة بخطوة</strong> حتى تصبح البيانات والقرارات قابلة للتتبع.</p>
    <ol class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 text-right">
      <?php $__currentLoopData = [
        ['n'=>'١','title'=>'تشخيص واقعي','text'=>'رسم ما يحدث اليوم: من يفعل ماذا، أين تضيع المعلومة، وما المخاطر التشغيلية والمالية.'],
        ['n'=>'٢','title'=>'أولويات قصيرة المدى','text'=>'نبدأ بمسار واحد مؤثر (مثلاً: فواتير، مخزون، أو متابعة عميل) بدل محاولة رقمنة كل شيء دفعة واحدة.'],
        ['n'=>'٣','title'=>'نظام أساس واضح','text'=>'قاعدة بيانات وصلاحيات وسجل تدقيق؛ حتى «القديم» يُحفظ ويُرجع إليه بشكل منظم.'],
        ['n'=>'٤','title'=>'تدريب وتبني عادات','text'=>'النظام يعيش مع الموظفين؛ جلسات بسيطة وقنوات دعم حتى لا يعود أحد للورق لأن النظام «صعب».'],
        ['n'=>'٥','title'=>'توسيع وذكاء','text'=>'بعد الاستقرار نضيف تكاملاً أكثر أو طبقة AI حيث يعود على الإنتاجية والجودة.'],
      ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="relative rounded-2xl border border-gray-200 bg-white p-5 pt-8 ps-14">
          <span class="absolute top-4 start-4 h-8 w-8 rounded-full flex items-center justify-center text-sm font-extrabold text-white" style="background: <?php echo e($themeColor); ?>"><?php echo e($step['n']); ?></span>
          <h3 class="font-extrabold text-gray-900"><?php echo e($step['title']); ?></h3>
          <p class="mt-2 text-sm text-gray-600 leading-relaxed"><?php echo e($step['text']); ?></p>
        </li>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ol>
  </div>

  <h2 class="text-2xl sm:text-3xl font-black font-cairo text-gray-900 text-center mb-3">منصة تشغيل جاهزة للمؤسسة</h2>
  <p class="text-center text-gray-600 text-sm sm:text-base max-w-3xl mx-auto mb-2 leading-relaxed">بالإضافة إلى الحلول المخصصة أعلاه، نوفر <strong class="text-gray-800">وحدات تشغيل متكاملة</strong> تُسرّع اعتماداً منضبطاً للشركات والمنظمات: مشاريع، موارد بشرية، فواتير، بوابة عميل، دعم ما بعد البيع، وتقارير — قابلة للتفعيل كجزء من برنامج تحولكم أو كمنصة أساس ثم توسيع لاحقاً.</p>
  <p class="text-center text-xs text-gray-500 max-w-2xl mx-auto mb-8">تكامل مباشر مع مسارات التطوير والذكاء الاصطناعي عند الحاجة، دون قفل على «صندوق أسود» لا تملكونه.</p>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php
      $cards = [
        ['title'=>'إدارة المشاريع والمهام','desc'=>'متابعة التقدم، توزيع المهام، تقارير الأداء وصلاحيات حسب الدور.','icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5'],
        ['title'=>'الموارد البشرية','desc'=>'موظفين، حضور وانصراف، إجازات، رواتب، وتحليلات.','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857'],
        ['title'=>'الفواتير والمدفوعات','desc'=>'فواتير عادية ومالية، متابعة الرصيد، وتحديثات حالة الدفع.','icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['title'=>'بوابة العميل','desc'=>'العميل يشاهد بياناته ومشاريعه وفواتيره ويقدم طلبات الدعم.','icon'=>'M12 11c0 1.656-1.791 3-4 3s-4-1.344-4-3 1.791-3 4-3 4 1.344 4 3zm8 0c0 1.656-1.791 3-4 3s-4-1.344-4-3 1.791-3 4-3 4 1.344 4 3z'],
        ['title'=>'ما بعد البيع (الدعم)','desc'=>'تذاكر أعطال/طلبات، تعيين للموظفين، وتواصل منظم مع العميل.','icon'=>'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
        ['title'=>'تقارير وتحليلات','desc'=>'مؤشرات تشغيل ومالية، تقارير قابلة للطباعة والتصدير.','icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2z'],
      ];
    ?>

    <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="bg-white rounded-2xl border border-gray-200 p-6 hover:shadow-lg transition">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center mb-4" style="background: color-mix(in srgb, <?php echo e($themeColor); ?> 15%, transparent)">
          <svg class="h-6 w-6" style="color: <?php echo e($themeColor); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($c['icon']); ?>" />
          </svg>
        </div>
        <h3 class="text-lg font-extrabold text-gray-900"><?php echo e($c['title']); ?></h3>
        <p class="mt-2 text-sm text-gray-600 leading-relaxed"><?php echo e($c['desc']); ?></p>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  <div class="mt-12 text-center">
    <a href="<?php echo e(route('website.contact')); ?>" class="inline-flex items-center justify-center px-6 py-3 rounded-xl text-white font-extrabold btn-brand shadow-md hover:shadow-lg transition">اطلب عرض سعر</a>
  </div>
</section>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('website.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\website\services.blade.php ENDPATH**/ ?>