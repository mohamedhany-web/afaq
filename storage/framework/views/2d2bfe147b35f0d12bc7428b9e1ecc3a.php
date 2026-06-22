

<?php $__env->startSection('title', \App\Helpers\SettingsHelper::getCompanyName() . ' - عن الشركة'); ?>

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
        Software &amp; AI Solutions
      </span>
      <h1 class="mt-6 text-3xl sm:text-4xl lg:text-5xl font-black font-cairo text-gray-900" style="line-height:1.2">
        من نحن في <?php echo e($cn); ?>؟
      </h1>
      <p class="mt-5 text-lg text-gray-600 leading-relaxed max-w-2xl">
        نحن لا نبيع “نظامًا جاهزًا” — نحن نبني المؤسسة رقميًا: نحول التشغيل اليومي إلى إجراءات واضحة، صلاحيات دقيقة، بيانات قابلة للقياس، وتكاملات تربط الأقسام بالعميل.
      </p>

      <div class="mt-8 flex flex-col sm:flex-row gap-3">
        <a href="<?php echo e(route('website.contact')); ?>" class="inline-flex items-center justify-center px-7 py-3.5 rounded-2xl text-white font-extrabold btn-brand shadow-lg hover:shadow-xl transition">
          احجز جلسة
        </a>
        <a href="<?php echo e(route('website.case-studies.index')); ?>" class="inline-flex items-center justify-center px-7 py-3.5 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 font-bold text-gray-800 transition">
          استعرض نماذج الأعمال
        </a>
      </div>
    </div>
  </div>
</section>


<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
    <div>
      <h2 class="text-2xl sm:text-3xl font-black font-cairo text-gray-900">نبني منظومة التشغيل… وليس مجرد واجهة</h2>
      <p class="mt-4 text-gray-600 leading-relaxed">
        عملنا يبدأ من داخل الشركة: نحلل العمليات، نعيد تصميم الإجراءات، ثم نحولها إلى نظام يعمل على أرض الواقع.
        النتيجة: وضوح مسؤوليات، سرعة قرار، تقارير لحظية، ودعم ما بعد البيع بمعايير مؤسسية.
      </p>

      <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
          <div class="text-xs font-bold mb-2" style="color: <?php echo e($tc); ?>">حوكمة وصلاحيات</div>
          <div class="text-sm font-extrabold text-gray-900">أدوار + اعتمادات + سجل تدقيق</div>
          <p class="mt-2 text-sm text-gray-600 leading-relaxed">صلاحيات دقيقة ومسارات قرار واضحة تقلل المخاطر وتزيد الانضباط.</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
          <div class="text-xs font-bold mb-2" style="color: <?php echo e($tc); ?>">بيانات وتقارير</div>
          <div class="text-sm font-extrabold text-gray-900">KPIs لحظية قابلة للقياس</div>
          <p class="mt-2 text-sm text-gray-600 leading-relaxed">لوحات مؤشرات تُظهر الحقيقة اليومية بدل التخمين.</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
          <div class="text-xs font-bold mb-2" style="color: <?php echo e($tc); ?>">تكاملات وتشغيل</div>
          <div class="text-sm font-extrabold text-gray-900">ربط الأقسام وإغلاق الحلقة</div>
          <p class="mt-2 text-sm text-gray-600 leading-relaxed">مبيعات + مالية + دعم + بوابة عميل في مسار واحد.</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
          <div class="text-xs font-bold mb-2" style="color: <?php echo e($tc); ?>">AI داخل التنفيذ</div>
          <div class="text-sm font-extrabold text-gray-900">تصنيف + تنبؤ + تلخيص</div>
          <p class="mt-2 text-sm text-gray-600 leading-relaxed">ذكاء اصطناعي يخدم القرار والتشغيل… وليس مجرد “ميزة”.</p>
        </div>
      </div>
    </div>

    
    <div class="bg-white rounded-3xl border border-gray-200 p-8 shadow-sm">
      <div class="flex items-center justify-between gap-3">
        <h3 class="text-xl font-extrabold text-gray-900">معلومات التواصل</h3>
        <span class="text-xs font-extrabold px-3 py-1.5 rounded-full border border-gray-200 bg-gray-50" style="color: <?php echo e($tc); ?>">Enterprise</span>
      </div>

      <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
          <div class="text-xs text-gray-500 mb-1">الاسم</div>
          <div class="font-extrabold text-gray-900"><?php echo e($cn); ?></div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
          <div class="text-xs text-gray-500 mb-1">البريد</div>
          <div class="font-extrabold text-gray-900 break-words"><?php echo e(\App\Helpers\SettingsHelper::getCompanyEmail()); ?></div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
          <div class="text-xs text-gray-500 mb-1">الهاتف</div>
          <div class="font-extrabold text-gray-900"><?php echo e(\App\Helpers\SettingsHelper::getCompanyPhone()); ?></div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
          <div class="text-xs text-gray-500 mb-1">العنوان</div>
          <div class="font-extrabold text-gray-900"><?php echo e(\App\Helpers\SettingsHelper::getCompanyAddress()); ?></div>
        </div>
      </div>

      <div class="mt-6 flex flex-col sm:flex-row gap-3">
        <a href="<?php echo e(route('website.contact')); ?>" class="inline-flex w-full items-center justify-center px-6 py-3 rounded-2xl text-white font-extrabold btn-brand shadow-md hover:shadow-lg transition">
          تواصل معنا
        </a>
        <a href="<?php echo e(route('client.login')); ?>" class="inline-flex w-full items-center justify-center px-6 py-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 font-bold text-gray-800 transition">
          بوابة العملاء
        </a>
      </div>
    </div>
  </div>
</section>


<section class="bg-gray-50 border-y border-gray-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="text-center mb-14">
      <h2 class="text-3xl sm:text-4xl font-black font-cairo text-gray-900">كيف نضمن النتائج؟</h2>
      <p class="mt-4 text-gray-600 max-w-2xl mx-auto leading-relaxed">قيم تنفيذ واضحة تجعل النظام يعيش ويتوسع داخل الشركة.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white rounded-2xl border border-gray-200 p-7 hover:shadow-lg transition">
        <div class="text-xs font-bold mb-2" style="color: <?php echo e($tc); ?>">تنفيذ مؤسسي</div>
        <div class="text-lg font-extrabold text-gray-900">خارطة طريق + إطلاق مرحلي</div>
        <p class="mt-2 text-sm text-gray-600 leading-relaxed">نُطلق بسرعة ثم نتوسع على مراحل… بدون تعطيل التشغيل.</p>
      </div>
      <div class="bg-white rounded-2xl border border-gray-200 p-7 hover:shadow-lg transition">
        <div class="text-xs font-bold mb-2" style="color: <?php echo e($tc); ?>">قياس واضح</div>
        <div class="text-lg font-extrabold text-gray-900">KPIs + تقارير</div>
        <p class="mt-2 text-sm text-gray-600 leading-relaxed">كل قرار ومخرجات التنفيذ قابلة للقياس والمتابعة.</p>
      </div>
      <div class="bg-white rounded-2xl border border-gray-200 p-7 hover:shadow-lg transition">
        <div class="text-xs font-bold mb-2" style="color: <?php echo e($tc); ?>">استمرارية</div>
        <div class="text-lg font-extrabold text-gray-900">SLA + دعم ما بعد البيع</div>
        <p class="mt-2 text-sm text-gray-600 leading-relaxed">تذاكر منظمة، تعيين للأقسام، وتقارير مهنية حتى الإغلاق.</p>
      </div>
    </div>
  </div>
</section>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('website.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\website\about.blade.php ENDPATH**/ ?>