
<?php
    $companyName = \App\Helpers\SettingsHelper::getCompanyName();
    $companyLogo = \App\Helpers\SettingsHelper::get('logo', null);
    $companyLogoPath = \App\Helpers\SettingsHelper::getLogoPath();
    $companyPhone = \App\Helpers\SettingsHelper::get('company_phone', '+20');
    $companyEmail = \App\Helpers\SettingsHelper::get('company_email', 'info@company.com');
    $companyAddress = \App\Helpers\SettingsHelper::get('company_address', 'عنوان الشركة');
    $companyWebsite = \App\Helpers\SettingsHelper::get('company_website', 'www.company.com');
?>

<div class="corporate-header">
    
    <div class="header-main">
        <div class="header-grid">
            
            <div class="logo-container">
                <?php if($companyLogo && \Storage::disk('public')->exists($companyLogo)): ?>
                    <img src="<?php echo e($companyLogoPath); ?>" alt="<?php echo e($companyName); ?>" class="company-logo">
                <?php else: ?>
                    <div class="company-logo-default">
                        <div class="logo-bg">
                            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="logoGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#1e40af;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <rect width="200" height="200" rx="40" fill="url(#logoGrad)"/>
                                <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="white" font-size="80" font-weight="900" font-family="Cairo, sans-serif">
                                    <?php echo e(mb_substr($companyName, 0, 1)); ?>

                                </text>
                            </svg>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            
            <div class="company-info-main">
                <h1 class="company-name-header"><?php echo e($companyName); ?></h1>
                <p class="company-slogan">نظام إدارة متكامل للموارد البشرية والأعمال</p>
                
                <div class="contact-grid">
                    <div class="contact-item-header">
                        <span class="contact-icon-header">📍</span>
                        <span class="contact-text"><?php echo e($companyAddress); ?></span>
                    </div>
                    <div class="contact-item-header">
                        <span class="contact-icon-header">📞</span>
                        <span class="contact-text"><?php echo e($companyPhone); ?></span>
                    </div>
                    <div class="contact-item-header">
                        <span class="contact-icon-header">✉️</span>
                        <span class="contact-text"><?php echo e($companyEmail); ?></span>
                    </div>
                    <div class="contact-item-header">
                        <span class="contact-icon-header">🌐</span>
                        <span class="contact-text"><?php echo e($companyWebsite); ?></span>
                    </div>
                </div>
            </div>
            
            
            <div class="report-metadata">
                <div class="metadata-box">
                    <div class="metadata-icon">📄</div>
                    <div class="metadata-content">
                        <div class="metadata-label">رقم التقرير</div>
                        <div class="metadata-value"><?php echo e(strtoupper(substr(md5(now()), 0, 8))); ?></div>
                    </div>
                </div>
                <div class="metadata-box">
                    <div class="metadata-icon">📅</div>
                    <div class="metadata-content">
                        <div class="metadata-label">التاريخ</div>
                        <div class="metadata-value"><?php echo e(now()->format('Y/m/d')); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="legal-bar">
        <div class="legal-items">
            <div class="legal-item">
                <span class="legal-label">مستخرج بواسطة:</span>
                <span class="legal-value"><?php echo e(auth()->user()->name ?? 'النظام'); ?></span>
            </div>
        </div>
    </div>

    
    <div class="report-title-section">
        <div class="title-decoration-top"></div>
        <h2 class="report-title-main"><?php echo e($title ?? 'تقرير'); ?></h2>
        <?php if(isset($subtitle)): ?>
            <p class="report-subtitle-main"><?php echo e($subtitle); ?></p>
        <?php endif; ?>
        <?php if(isset($period)): ?>
            <div class="report-period-main">
                <span class="period-icon-main">🗓️</span>
                <span class="period-text"><?php echo e($period); ?></span>
            </div>
        <?php endif; ?>
        <div class="title-decoration-bottom"></div>
    </div>
</div>

<style>
/* Corporate Professional Header */
.corporate-header {
    background: white;
    margin-bottom: 30px;
    border: 2px solid #e2e8f0;
    border-radius: 0;
}

/* Main Header Section */
.header-main {
    padding: 30px 40px;
    background: linear-gradient(to bottom, #ffffff, #f8fafc);
    border-bottom: 1px solid #e2e8f0;
}

.header-grid {
    display: grid;
    grid-template-columns: 140px 1fr 200px;
    gap: 30px;
    align-items: center;
}

/* Logo Styles */
.logo-container {
    display: flex;
    align-items: center;
    justify-content: center;
}

.company-logo {
    width: 130px;
    height: 130px;
    object-fit: contain;
    border: 3px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px;
    background: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.company-logo-default {
    width: 130px;
    height: 130px;
}

.logo-bg {
    width: 100%;
    height: 100%;
    border: 3px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Company Information */
.company-info-main {
    border-right: 3px solid #3b82f6;
    border-left: 3px solid #3b82f6;
    padding: 0 25px;
}

.company-name-header {
    font-size: 36px;
    font-weight: 900;
    color: #0f172a;
    margin: 0 0 5px 0;
    font-family: 'Cairo', sans-serif;
    letter-spacing: 1px;
}

.company-slogan {
    font-size: 14px;
    color: #64748b;
    margin: 0 0 15px 0;
    font-weight: 500;
}

.contact-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
}

.contact-item-header {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #475569;
}

.contact-icon-header {
    font-size: 14px;
}

.contact-text {
    font-weight: 500;
}

/* Report Metadata */
.report-metadata {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.metadata-box {
    display: flex;
    align-items: center;
    gap: 12px;
    background: white;
    padding: 12px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.metadata-icon {
    font-size: 24px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    border-radius: 6px;
}

.metadata-content {
    flex: 1;
}

.metadata-label {
    font-size: 10px;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 2px;
}

.metadata-value {
    font-size: 13px;
    color: #0f172a;
    font-weight: 700;
    font-family: 'Courier New', monospace;
    direction: ltr;
    text-align: right;
}

/* Legal Information Bar */
.legal-bar {
    background: #f1f5f9;
    padding: 15px 40px;
    border-bottom: 1px solid #e2e8f0;
}

.legal-items {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 25px;
}

.legal-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
}

.legal-label {
    color: #64748b;
    font-weight: 600;
}

.legal-value {
    color: #0f172a;
    font-weight: 700;
    font-family: 'Courier New', monospace;
}

.legal-divider {
    width: 2px;
    height: 20px;
    background: #cbd5e1;
}

/* Report Title Section */
.report-title-section {
    padding: 30px 40px;
    text-align: center;
    background: white;
}

.title-decoration-top {
    width: 100px;
    height: 4px;
    background: linear-gradient(90deg, #3b82f6, #1e40af);
    margin: 0 auto 20px;
    border-radius: 2px;
}

.report-title-main {
    font-size: 32px;
    font-weight: 900;
    color: #0f172a;
    margin: 0 0 10px 0;
    font-family: 'Cairo', sans-serif;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.report-subtitle-main {
    font-size: 16px;
    color: #64748b;
    margin: 0 0 15px 0;
    font-weight: 500;
}

.report-period-main {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #3b82f6, #1e40af);
    color: white;
    padding: 8px 20px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.period-icon-main {
    font-size: 16px;
}

.title-decoration-bottom {
    width: 200px;
    height: 3px;
    background: linear-gradient(90deg, transparent, #3b82f6, transparent);
    margin: 20px auto 0;
    border-radius: 2px;
}

/* Print Specific Styles */
@media print {
    .corporate-header {
        page-break-after: avoid;
        border: 2px solid #000;
    }
    
    .header-main {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .legal-bar {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .report-period-main {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .metadata-icon {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .title-decoration-top,
    .title-decoration-bottom {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\components\print-header.blade.php ENDPATH**/ ?>