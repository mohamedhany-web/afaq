<?php

namespace App\Helpers;

use App\Models\SystemSetting;

class SettingsHelper
{
    /**
     * الحصول على اسم النظام
     */
    public static function getSystemName()
    {
        return SystemSetting::get('system_name', 'نظام إدارة سولفيستا');
    }

    /**
     * الحصول على وصف النظام
     */
    public static function getSystemDescription()
    {
        return SystemSetting::get('system_description', 'نظام شامل لإدارة العمليات التجارية والموارد البشرية');
    }

    /**
     * الحصول على اسم الشركة
     */
    public static function getCompanyName()
    {
        return SystemSetting::get('company_name', '');
    }

    /**
     * الحصول على عنوان الشركة
     */
    public static function getCompanyAddress()
    {
        return SystemSetting::get('company_address', '');
    }

    /**
     * الحصول على هاتف الشركة
     */
    public static function getCompanyPhone()
    {
        return SystemSetting::get('company_phone', '');
    }

    /**
     * الحصول على بريد الشركة الإلكتروني
     */
    public static function getCompanyEmail()
    {
        return SystemSetting::get('company_email', '');
    }

    /**
     * الحصول على مسار اللوجو
     */
    public static function getLogoPath()
    {
        return SystemSetting::get('logo', 'system/logo.png');
    }

    /**
     * الحصول على URL اللوجو الكامل
     */
    public static function getLogoUrl()
    {
        $logoPath = self::getLogoPath();
        
        if (empty($logoPath)) {
            return null;
        }
        
        // IMPORTANT:
        // Return a relative URL to avoid breaking assets when APP_URL is misconfigured on hosting
        // (common cause of "sometimes works" due to cached HTML / mixed environments).
        $url = '/storage/' . ltrim($logoPath, '/');

        // Optional cache busting: only when debug enabled (avoid killing browser/CDN caching in production)
        if (config('app.debug')) {
            $url .= '?v=' . time();
        }

        return $url;
    }

    /**
     * الحصول على مسار الأيقونة
     */
    public static function getFaviconPath()
    {
        return SystemSetting::get('favicon', 'system/favicon.ico');
    }

    /**
     * الحصول على URL الأيقونة الكامل
     */
    public static function getFaviconUrl()
    {
        $faviconPath = self::getFaviconPath();
        
        if (empty($faviconPath)) {
            return null;
        }
        
        // Return relative URL (same reasoning as logo)
        return '/storage/' . ltrim($faviconPath, '/');
    }

    /**
     * الحصول على اللون الرئيسي
     */
    public static function getThemeColor()
    {
        return SystemSetting::get('theme_color', '#2563eb');
    }

    /**
     * الحصول على لون الشريط الجانبي
     */
    public static function getSidebarColor()
    {
        return SystemSetting::get('sidebar_color', '#1f2937');
    }

    /**
     * الحصول على حجم اللوجو
     */
    public static function getLogoSize()
    {
        return SystemSetting::get('logo_size', 'medium');
    }

    /**
     * الحصول على حجم اللوجو بالبكسل
     */
    public static function getLogoSizePixels()
    {
        $size = self::getLogoSize();
        switch ($size) {
            case 'small':
                return 'h-8 w-8';
            case 'large':
                return 'h-16 w-16';
            default:
                return 'h-12 w-12';
        }
    }

    /**
     * الحصول على المنطقة الزمنية
     */
    public static function getTimezone()
    {
        return SystemSetting::get('timezone', 'Africa/Cairo');
    }

    /**
     * الحصول على لغة النظام
     */
    public static function getLanguage()
    {
        return SystemSetting::get('language', 'ar');
    }

    /**
     * الحصول على تنسيق التاريخ
     */
    public static function getDateFormat()
    {
        return SystemSetting::get('date_format', 'Y-m-d');
    }

    /**
     * الحصول على تنسيق الوقت
     */
    public static function getTimeFormat()
    {
        return SystemSetting::get('time_format', 'H:i');
    }

    /**
     * التحقق من تفعيل إشعارات البريد الإلكتروني
     */
    public static function isEmailNotificationsEnabled()
    {
        return SystemSetting::get('email_notifications', '1') == '1';
    }

    /**
     * التحقق من تفعيل إشعارات الرسائل النصية
     */
    public static function isSmsNotificationsEnabled()
    {
        return SystemSetting::get('sms_notifications', '0') == '1';
    }

    /**
     * التحقق من تفعيل الإشعارات الفورية
     */
    public static function isPushNotificationsEnabled()
    {
        return SystemSetting::get('push_notifications', '1') == '1';
    }

    /**
     * الحصول على جميع إعدادات النظام
     */
    public static function getAllSettings()
    {
        return SystemSetting::getSystemSettings();
    }

    /**
     * تحديث إعداد
     */
    public static function updateSetting($key, $value, $type = 'string', $group = 'general', $description = null, $isPublic = false)
    {
        return SystemSetting::set($key, $value, $type, $group, $description, $isPublic);
    }

    /**
     * الحصول على بادئة الرقم التوظيفي
     */
    public static function getEmployeeIdPrefix()
    {
        return SystemSetting::get('employee_id_prefix', 'EMP');
    }

    /**
     * الحصول على طول الرقم التوظيفي
     */
    public static function getEmployeeIdLength()
    {
        return SystemSetting::get('employee_id_length', 6);
    }

    /**
     * الحصول على نوع توليد الرقم التوظيفي
     */
    public static function getEmployeeIdType()
    {
        return SystemSetting::get('employee_id_type', 'sequential'); // sequential, random
    }

    /**
     * مسح كاش الإعدادات
     */
    public static function clearCache()
    {
        SystemSetting::clearCache();
    }

    // ==================== الإعدادات المالية ====================
    
    /**
     * الحصول على اسم البنك
     */
    public static function getBankName()
    {
        return SystemSetting::get('bank_name', '');
    }

    /**
     * الحصول على رقم الحساب البنكي
     */
    public static function getBankAccountNumber()
    {
        return SystemSetting::get('bank_account_number', '');
    }

    /**
     * الحصول على IBAN
     */
    public static function getBankIban()
    {
        return SystemSetting::get('bank_iban', '');
    }

    /**
     * الحصول على اسم صاحب الحساب
     */
    public static function getBankAccountHolder()
    {
        return SystemSetting::get('bank_account_holder', '');
    }

    /**
     * الحصول على Swift Code
     */
    public static function getBankSwift()
    {
        return SystemSetting::get('bank_swift', '');
    }

    /**
     * الحصول على فرع البنك
     */
    public static function getBankBranch()
    {
        return SystemSetting::get('bank_branch', '');
    }

    /**
     * الحصول على طرق الدفع المقبولة
     */
    public static function getPaymentMethods()
    {
        return SystemSetting::get('payment_methods', 'تحويل بنكي أو شيك');
    }

    /**
     * الحصول على مدة السداد الافتراضية (أيام)
     */
    public static function getDefaultPaymentPeriod()
    {
        return (int) SystemSetting::get('default_payment_period', 30);
    }

    /**
     * الحصول على نسبة الضريبة الافتراضية
     */
    public static function getDefaultTaxRate()
    {
        return (float) SystemSetting::get('default_tax_rate', 0);
    }

    /**
     * الحصول على الرقم الضريبي
     */
    public static function getTaxNumber()
    {
        return SystemSetting::get('tax_number', '');
    }

    /**
     * الحصول على السجل التجاري
     */
    public static function getCommercialRegistration()
    {
        return SystemSetting::get('commercial_registration', '');
    }

    /**
     * الحصول على الملاحظات المالية للفواتير
     */
    public static function getInvoiceFinancialNotes()
    {
        return SystemSetting::get('invoice_financial_notes', '');
    }

    public static function getCurrencySymbol(): string
    {
        return SystemSetting::get('currency_symbol', 'ج.م');
    }

    public static function formatMoney($amount, int $decimals = 0): string
    {
        return number_format((float) ($amount ?? 0), $decimals) . ' ' . self::getCurrencySymbol();
    }
}