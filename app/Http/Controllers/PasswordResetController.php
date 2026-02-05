<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ResendService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetController extends Controller
{
    protected $resendService;

    public function __construct(ResendService $resendService)
    {
        $this->resendService = $resendService;
    }

    /**
     * Request OTP for password reset
     * POST /api/auth/forgot-password
     */
    public function requestOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->error(
                'Validation failed',
                $validator->errors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $email = $request->email;

            // Generate 6-digit OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Delete any existing OTPs for this email
            DB::table('password_reset_otps')
                ->where('email', $email)
                ->delete();

            // Store new OTP (expires in 5 minutes)
            DB::table('password_reset_otps')->insert([
                'email' => $email,
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(5),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Send OTP via email
            $emailSent = $this->resendService->sendOTP($email, $otp);

            if (!$emailSent) {
                return response()->error(
                    'Failed to send OTP email',
                    'Please try again later',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            return response()->success(
                null,
                'OTP has been sent to your email. Valid for 5 minutes.',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            return response()->error(
                'Failed to process request',
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Verify OTP
     * POST /api/auth/verify-otp
     */
    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        if ($validator->fails()) {
            return response()->error(
                'Validation failed',
                $validator->errors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $otpRecord = DB::table('password_reset_otps')
                ->where('email', $request->email)
                ->where('otp', $request->otp)
                ->first();

            if (!$otpRecord) {
                return response()->error(
                    'Invalid OTP',
                    'The OTP code you entered is incorrect',
                    Response::HTTP_BAD_REQUEST
                );
            }

            // Check if expired
            if (Carbon::now()->greaterThan($otpRecord->expires_at)) {
                // Delete expired OTP
                DB::table('password_reset_otps')
                    ->where('id', $otpRecord->id)
                    ->delete();

                return response()->error(
                    'OTP Expired',
                    'This OTP has expired. Please request a new one.',
                    Response::HTTP_BAD_REQUEST
                );
            }

            return response()->success(
                [
                    'verified' => true,
                    'email' => $request->email
                ],
                'OTP verified successfully. You can now reset your password.',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            return response()->error(
                'Verification failed',
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Reset password with verified OTP
     * POST /api/auth/reset-password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->error(
                'Validation failed',
                $validator->errors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            // Verify OTP again
            $otpRecord = DB::table('password_reset_otps')
                ->where('email', $request->email)
                ->where('otp', $request->otp)
                ->first();

            if (!$otpRecord) {
                return response()->error(
                    'Invalid OTP',
                    'The OTP code is incorrect',
                    Response::HTTP_BAD_REQUEST
                );
            }

            // Check if expired
            if (Carbon::now()->greaterThan($otpRecord->expires_at)) {
                DB::table('password_reset_otps')
                    ->where('id', $otpRecord->id)
                    ->delete();

                return response()->error(
                    'OTP Expired',
                    'This OTP has expired. Please request a new one.',
                    Response::HTTP_BAD_REQUEST
                );
            }

            // Update user password
            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            // Delete used OTP
            DB::table('password_reset_otps')
                ->where('id', $otpRecord->id)
                ->delete();

            return response()->success(
                null,
                'Password reset successfully. You can now login with your new password.',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            return response()->error(
                'Reset failed',
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
