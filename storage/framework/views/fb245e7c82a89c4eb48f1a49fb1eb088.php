<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-color-mode" content="light">
    <meta name="color-scheme" content="light">
    <title>كود التحقق - <?php echo e(\App\Helpers\SettingsHelper::getSystemName()); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;600;700;900&family=Cairo:wght@300;400;500;600;700;900&display=swap');
        
        /* Reset styles */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            outline: none;
            text-decoration: none;
        }
        
        body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            font-family: 'Tajawal', 'Cairo', Arial, sans-serif;
            background-color: #f4f7fa;
            line-height: 1.6;
        }
        
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                max-width: 100% !important;
            }
            .email-content {
                padding: 20px !important;
            }
            .code-display {
                font-size: 36px !important;
                letter-spacing: 8px !important;
            }
        }
        
        .email-wrapper {
            width: 100%;
            background-color: #f4f7fa;
            padding: 40px 0;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .email-header {
            background: linear-gradient(135deg, <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?> 0%, <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>dd 100%);
            padding: 40px 30px;
            text-align: center;
            color: #ffffff;
        }
        
        .email-header .logo-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            font-family: 'Cairo', sans-serif;
        }
        
        .email-header p {
            margin: 10px 0 0;
            font-size: 16px;
            opacity: 0.95;
            font-family: 'Tajawal', sans-serif;
        }
        
        .email-content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            color: #2d3748;
            margin-bottom: 20px;
            font-weight: 500;
            font-family: 'Tajawal', sans-serif;
        }
        
        .message-box {
            background-color: #f8f9fa;
            border-right: 4px solid <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-family: 'Tajawal', sans-serif;
        }
        
        .message-box p {
            margin: 0;
            color: #4a5568;
            font-size: 16px;
            line-height: 1.8;
        }
        
        .code-container {
            background: linear-gradient(135deg, <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?> 0%, <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>dd 100%);
            border-radius: 16px;
            padding: 40px 30px;
            text-align: center;
            margin: 30px 0;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }
        
        .code-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin-bottom: 15px;
            font-weight: 500;
            font-family: 'Tajawal', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .code-display {
            font-size: 56px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 15px;
            margin: 20px 0;
            font-family: 'Courier New', 'Monaco', 'Consolas', monospace;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            direction: ltr;
            unicode-bidi: embed;
            text-align: center;
        }
        
        .code-expiry {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin-top: 15px;
            font-family: 'Tajawal', sans-serif;
        }
        
        .warning-box {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 12px;
            padding: 20px;
            margin: 30px 0;
            font-family: 'Tajawal', sans-serif;
        }
        
        .warning-box .warning-title {
            color: #856404;
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .warning-box .warning-title svg {
            margin-left: 8px;
            width: 20px;
            height: 20px;
        }
        
        .warning-box p {
            margin: 0;
            color: #856404;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .security-notice {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin: 30px 0;
            border: 1px solid #e9ecef;
            font-family: 'Tajawal', sans-serif;
        }
        
        .security-notice p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .email-footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .email-footer p {
            margin: 5px 0;
            color: #6c757d;
            font-size: 13px;
            font-family: 'Tajawal', sans-serif;
        }
        
        .company-name {
            color: <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>;
            font-weight: 600;
        }
        
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e9ecef, transparent);
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td align="center">
                    <table role="presentation" class="email-container" cellspacing="0" cellpadding="0" border="0" width="600">
                        <!-- Header -->
                        <tr>
                            <td class="email-header">
                                <div class="logo-container">
                                    <?php
                                        $logoPath = \App\Helpers\SettingsHelper::getLogoPath();
                                        $logoUrl = null;
                                        if ($logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath)) {
                                            // استخدام URL مطلق للبريد الإلكتروني
                                            $baseUrl = config('app.url');
                                            $logoUrl = rtrim($baseUrl, '/') . '/storage/' . $logoPath;
                                        }
                                    ?>
                                    <?php if($logoUrl): ?>
                                        <img src="<?php echo e($logoUrl); ?>" alt="Logo" style="width: 60px; height: 60px; object-fit: contain; display: block; margin: 0 auto; border: 0; outline: none; text-decoration: none;">
                                    <?php else: ?>
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    <?php endif; ?>
                                </div>
                                <h1><?php echo e(\App\Helpers\SettingsHelper::getSystemName()); ?></h1>
                                <p>كود التحقق</p>
                            </td>
                        </tr>
                        
                        <!-- Content -->
                        <tr>
                            <td class="email-content">
                                <div class="greeting">
                                    مرحباً <strong><?php echo e($userName ?? 'المستخدم'); ?></strong>،
                                </div>
                                
                                <div class="message-box">
                                    <p>
                                        لقد قمت بتسجيل الدخول إلى نظام <strong><?php echo e(\App\Helpers\SettingsHelper::getSystemName()); ?></strong>. 
                                        يرجى استخدام الكود التالي للتحقق من هويتك ومتابعة تسجيل الدخول.
                                    </p>
                                </div>
                                
                                <div class="code-container">
                                    <div class="code-label">كود التحقق</div>
                                    <div class="code-display"><?php echo e(str_replace(' ', '', $code)); ?></div>
                                    <div class="code-expiry">⏱️ هذا الكود صالح لمدة 10 دقائق فقط</div>
                                </div>
                                
                                <div class="warning-box">
                                    <div class="warning-title">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        تحذير أمني
                                    </div>
                                    <p>
                                        لا تشارك هذا الكود مع أي شخص. إذا لم تكن أنت من طلب هذا الكود، يرجى تجاهل هذه الرسالة وإبلاغ الدعم الفني فوراً.
                                    </p>
                                </div>
                                
                                <div class="security-notice">
                                    <p>
                                        <strong>ملاحظة:</strong> إذا لم تطلب هذا الكود، يرجى تغيير كلمة المرور الخاصة بك فوراً من إعدادات حسابك.
                                    </p>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Footer -->
                        <tr>
                            <td class="email-footer">
                                <div class="divider"></div>
                                <p>
                                    هذه رسالة آلية من نظام <span class="company-name"><?php echo e(\App\Helpers\SettingsHelper::getSystemName()); ?></span>
                                </p>
                                <p>
                                    يرجى عدم الرد على هذه الرسالة
                                </p>
                                <p style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e9ecef;">
                                    &copy; <?php echo e(date('Y')); ?> <span class="company-name"><?php echo e(\App\Helpers\SettingsHelper::getCompanyName()); ?></span>. جميع الحقوق محفوظة.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\emails\verification-code.blade.php ENDPATH**/ ?>