<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    protected $user;
    protected $otp;

    public function __construct(User $user, Otp $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    public function index()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validatedData = [
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'age' => $request->age,
            'role' => 'User',
            'isVerify' => false,
        ];

        $this->user->create($validatedData);

        $otpCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $expirationTime = now()->addMinutes(10);

        $hashedOtpCode = Hash::make($otpCode);

        $this->otp->create([
            'otp_code' => $hashedOtpCode,
            'expired' => $expirationTime,
            'email' => $request->email,
        ]);

        Mail::to($request->email)->send(new OtpMail($otpCode, $expirationTime));

        return response()->json(
            [
                'message' => 'User registered successfully. Please verify your email.',
                'redirect' => route('verify.otp', ['email' => $request->email]),
            ],
            201,
        );
    }

    public function showOtpVerificationForm()
    {
        $email = request()->query('email');

        return view('auth.verify_otp', compact('email'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|string|size:4',
        ]);

        $email = $request->email;
        $user = $this->user->where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $otp = $this->otp
            ->where('email', $request->email)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$otp) {
            return response()->json(['message' => 'No OTP found for this email.'], 404);
        }

        if ($otp->expired < now()) {
            return response()->json(['message' => 'OTP has expired.'], 400);
        }

        if ($otp && Hash::check($request->otp_code, $otp->otp_code)) {
            $user->isVerify = true;
            $user->save();

            return response()->json(['message' => 'OTP verified successfully.'], 200);
        } else {
            return response()->json(['message' => 'Invalid OTP.'], 400);
        }
    }

    //SEND OTP
    public function showSendOTPForm()
    {
        return view('auth.send_otp_register');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = $this->user->where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $otp = $this->otp
            ->where('email', $request->email)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($otp && now()->diffInMinutes($otp->created_at) < 10) {
            return response()->json(['message' => 'OTP is still valid.'], 400);
        }

        $newOtpCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $hashedOtp = Hash::make($newOtpCode);
        $newExpirationTime = now()->addMinutes(10);

        if ($otp) {
            $otp->otp_code = $hashedOtp;
            $otp->updated_at = now();
            $otp->expired = $newExpirationTime;
            $otp->save();
        } else {
            $this->otp->create([
                'otp_code' => $hashedOtp,
                'expired' => $newExpirationTime,
                'email' => $request->email,
                'updated_at' => now(),
            ]);
        }

        Mail::to($request->email)->send(new OtpMail($newOtpCode, $newExpirationTime));

        return response()->json(
            [
                'message' => 'OTP sent successfully.',
                'redirect' => route('verify.otp', ['email' => $request->email]),
            ],
            200,
        );
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return response()->json(['message' => 'Login successful'], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->withSuccess('You have logged out successfully!');
    }

    //SEND OTP FORGOT PASSWORD
    public function showSendOTPForgotPassForm()
    {
        return view('auth.send_otp_forgot_password');
    }

    public function sendOtpForgotPassword(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'email' => 'required|email',
            ]);

            $user = $this->user->where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            $otp = $this->otp
                ->where('email', $request->email)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($otp && now()->diffInMinutes($otp->created_at) < 10) {
                return response()->json(['message' => 'OTP is still valid.'], 400);
            }

            $newOtpCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $hashedOtp = Hash::make($newOtpCode);
            $newExpirationTime = now()->addMinutes(10);

            if ($otp) {
                $otp->otp_code = $hashedOtp;
                $otp->updated_at = now();
                $otp->expired = $newExpirationTime;
                $otp->save();
            } else {
                $this->otp->create([
                    'otp_code' => $hashedOtp,
                    'expired' => $newExpirationTime,
                    'email' => $request->email,
                    'updated_at' => now(),
                ]);
            }

            Mail::to($request->email)->send(new OtpMail($newOtpCode, $newExpirationTime));
            DB::commit();
            return response()->json(
                [
                    'message' => 'OTP sent successfully.',
                    'redirect' => route('verify_forgot_password.otp', ['email' => $request->email]),
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error send otp forgot password: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function showOtpVerificationForgotPasswordForm()
    {
        $email = request()->query('email');

        return view('auth.verify_otp_forgot_password', compact('email'));
    }

    public function verifyOtpForPasswordReset(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'email' => 'required|email',
                'otp_code' => 'required|string|size:4',
            ]);

            $email = $request->email;
            $user = $this->user->where('email', $email)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            $otp = $this->otp
                ->where('email', $request->email)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$otp) {
                return response()->json(['message' => 'No OTP found for this email.'], 404);
            }

            if ($otp->expired < now()) {
                return response()->json(['message' => 'OTP has expired.'], 400);
            }

            if ($otp && Hash::check($request->otp_code, $otp->otp_code)) {
                DB::commit();
                return response()->json(
                    [
                        'message' => 'OTP valid!!!.',
                        'redirect' => route('password_reset.otp', ['email' => $request->email]),
                    ],
                    200,
                );
            } else {
                return response()->json(['message' => 'Invalid OTP.'], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error verify otp forgot password: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function showPasswordResetForm(Request $request)
    {
        return view('auth.forgot_password', ['email' => $request->email]);
    }

    public function updatePassword(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'email' => 'required|email',
                'new_password' => 'nullable|string|min:8',
            ]);

            $user = $this->user->where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();
            DB::commit();
            return response()->json(
                [
                    'message' => 'Password changed successfully!!!',
                    'redirect' => route('login'),
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating password: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
