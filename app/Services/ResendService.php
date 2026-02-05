<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResendService
{
    protected $apiKey;
    protected $fromEmail;

    public function __construct()
    {
        $this->apiKey = env('RESEND_API_KEY');
        $this->fromEmail = env('RESEND_FROM_EMAIL', 'onboarding@resend.dev');
    }

    /**
     * Send OTP email via Resend API
     */
    public function sendOTP(string $email, string $otp): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.resend.com/emails', [
                'from' => $this->fromEmail,
                'to' => [$email],
                'subject' => 'Reset Your Password - OTP Code',
                'html' => $this->getOTPEmailTemplate($otp),
            ]);

            if ($response->successful()) {
                Log::info('OTP email sent successfully', ['email' => $email]);
                return true;
            }

            Log::error('Failed to send OTP email', [
                'email' => $email,
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
            return false;
        } catch (\Exception $e) {
            Log::error('Exception sending OTP email', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get HTML template for OTP email
     */
    private function getOTPEmailTemplate(string $otp): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="margin: 0; padding: 0; background-color: #f4f4f4;">
            <div style="max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 20px; text-align: center;">
                    <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-family: Arial, sans-serif;">FindFix</h1>
                    <p style="color: #ffffff; margin: 10px 0 0 0; font-size: 14px; font-family: Arial, sans-serif;">Password Reset Request</p>
                </div>
                
                <div style="padding: 40px 30px; font-family: Arial, sans-serif;">
                    <p style="color: #333333; font-size: 16px; line-height: 1.5; margin: 0 0 20px 0;">
                        Hello! 👋
                    </p>
                    <p style="color: #666666; font-size: 14px; line-height: 1.6; margin: 0 0 30px 0;">
                        You requested to reset your password. Use the OTP code below to complete the process:
                    </p>
                    
                    <div style="background-color: #f8f9fa; border-radius: 8px; padding: 30px; text-align: center; margin: 0 0 30px 0;">
                        <p style="color: #666666; font-size: 12px; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 1px;">Your OTP Code</p>
                        <h2 style="color: #667eea; font-size: 40px; margin: 0; letter-spacing: 8px; font-family: \'Courier New\', monospace;">
                            ' . $otp . '
                        </h2>
                    </div>
                    
                    <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 0 0 30px 0;">
                        <p style="color: #856404; font-size: 13px; margin: 0; line-height: 1.5;">
                            ⏱️ <strong>This code will expire in 5 minutes</strong>
                        </p>
                    </div>
                    
                    <p style="color: #666666; font-size: 14px; line-height: 1.6; margin: 0 0 20px 0;">
                        If you didn\'t request this password reset, please ignore this email or contact support if you have concerns.
                    </p>
                    
                    <div style="border-top: 1px solid #e0e0e0; padding-top: 20px; margin-top: 30px;">
                        <p style="color: #999999; font-size: 12px; line-height: 1.5; margin: 0;">
                            This is an automated email, please do not reply.
                        </p>
                        <p style="color: #999999; font-size: 12px; line-height: 1.5; margin: 5px 0 0 0;">
                            © ' . date('Y') . ' FindFix. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ';
    }
}
