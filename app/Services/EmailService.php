<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class EmailService
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->configureSMTP();
    }

    /**
     * Configure SMTP settings for Gmail
     */
    private function configureSMTP(): void
    {
        try {
            $this->mail->isSMTP();
            $this->mail->Host       = env('MAIL_HOST', 'smtp.gmail.com');
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = env('MAIL_USERNAME', '');
            $this->mail->Password   = env('MAIL_PASSWORD', '');
            $encryption             = env('MAIL_ENCRYPTION', 'tls');
            $this->mail->SMTPSecure = $encryption === 'ssl'
                ? PHPMailer::ENCRYPTION_SMTPS
                : PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port       = (int) env('MAIL_PORT', 587);
            $this->mail->CharSet    = 'UTF-8';
            $this->mail->Encoding   = '8bit';
            
            // إعدادات إضافية للاتصال
            $this->mail->SMTPDebug  = 0; // 0 = لا توجد رسائل, 2 = رسائل مفصلة (للتشخيص فقط)
            $this->mail->Debugoutput = function($str, $level) {
                Log::info("PHPMailer Debug [$level]: $str");
                error_log("PHPMailer Debug [$level]: $str");
            };
            $this->mail->Timeout    = 60;
            $this->mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
        } catch (Exception $e) {
            Log::error('Failed to configure SMTP: ' . $e->getMessage());
        }
    }

    /**
     * Send verification code email
     */
    public function sendVerificationCode(string $email, string $code, string $userName = 'المستخدم'): bool
    {
        // التأكد من وجود userName - معالجة شاملة قبل try
        $finalUserName = 'المستخدم'; // قيمة افتراضية
        if (!empty($userName) && trim($userName) !== '' && !is_null($userName)) {
            $finalUserName = trim($userName);
        }
        
        try {
            // إعادة تهيئة PHPMailer لكل إرسال جديد
            $this->mail = new PHPMailer(true);
            $this->configureSMTP();
            
            // Log للمساعدة في التشخيص
            Log::info('Sending verification code', [
                'email' => $email,
                'userName' => $finalUserName,
                'code' => $code,
                'originalUserName' => $userName
            ]);
            
            // إعادة تهيئة المرسل
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            $this->mail->clearCustomHeaders();
            $this->mail->clearReplyTos();
            
            // المرسل والمستقبل
            $this->mail->setFrom(
                env('MAIL_FROM_ADDRESS', env('MAIL_USERNAME')),
                env('MAIL_FROM_NAME', 'Solvesta'),
            );
            $this->mail->addAddress($email);
            
            // محتوى البريد
            $this->mail->isHTML(true);
            $this->mail->Subject = 'كود التحقق - Solvesta';
            
            // استخدام محتوى HTML من القالب
            // التأكد من تمرير userName بشكل صحيح بشكل صريح
            // التأكد من أن الكود هو نص واضح بدون مسافات
            $cleanCode = is_string($code) ? preg_replace('/\s+/', '', $code) : (string)$code;
            
            $mailBody = view('emails.verification-code', [
                'code' => $cleanCode,
                'userName' => $finalUserName
            ])->render();
            
            // إضافة Content-Type header صريح
            $this->mail->Body = $mailBody;
            $this->mail->ContentType = 'text/html; charset=UTF-8';
            $this->mail->AltBody = sprintf(
                "مرحباً %s،\n\nكود التحقق الخاص بك هو: %s\n\nهذا الكود صالح لمدة 10 دقائق فقط.",
                $finalUserName,
                $cleanCode
            );
            
            // محاولة الإرسال مع التحقق من النتيجة
            $result = $this->mail->send();
            
            if ($result) {
                Log::info('Verification code sent successfully', [
                    'email' => $email,
                    'code' => $code,
                    'message_id' => $this->mail->getLastMessageID()
                ]);
                return true;
            } else {
                Log::error('PHPMailer send() returned false', [
                    'email' => $email,
                    'error' => $this->mail->ErrorInfo
                ]);
                return false;
            }
            
        } catch (Exception $e) {
            Log::error('Mailer Error: ' . $this->mail->ErrorInfo);
            Log::error('Exception: ' . $e->getMessage());
            Log::error('Exception Trace: ' . $e->getTraceAsString());
            
            // إعادة تهيئة PHPMailer في حالة الخطأ
            try {
                $this->mail = new PHPMailer(true);
                $this->configureSMTP();
            } catch (Exception $reinitException) {
                Log::error('Failed to reinitialize PHPMailer: ' . $reinitException->getMessage());
            }
            
            return false;
        }
    }

    /**
     * Send custom email
     */
    public function sendEmail(string $to, string $subject, string $body, string $fromName = 'Solvesta'): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            
            $this->mail->setFrom(
                env('MAIL_FROM_ADDRESS', env('MAIL_USERNAME')),
                $fromName ?: env('MAIL_FROM_NAME', 'Solvesta'),
            );
            $this->mail->addAddress($to);
            
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            
            $this->mail->send();
            
            return true;
            
        } catch (Exception $e) {
            Log::error('Mailer Error: ' . $this->mail->ErrorInfo);
            
            return false;
        }
    }
}

