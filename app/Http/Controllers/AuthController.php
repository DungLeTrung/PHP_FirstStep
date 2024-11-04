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
use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $user = $this->authService->registerUser($request->all());

        if ($user) {
            $this->authService->sendOtp($request->email);

            return response()->json(
                [
                    'message' => 'User registered successfully. Please verify your email.',
                    'redirect' => route('verify.otp', ['email' => $request->email]),
                ],
                201,
            );
        }

        return response()->json(['message' => 'Error registering user'], 500);
    }

    public function verifyOtp(Request $request)
    {
        try {
            $response = $this->authService->verifyOtp($request->email, $request->otp_code);

            return response()->json(
                [
                    'message' => $response['message'],
                    'redirect' => route('login'),
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error sending OTP for forgot password: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function sendOtp(Request $request)
    {
        try {
            $response = $this->authService->sendOtp($request->email);

            return response()->json(['message' => $response['message'], 'redirect' => route('login')], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error sending OTP for forgot password: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Login successful'], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have logged out successfully!');
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
            $request->validate(['email' => 'required|email']);
            $this->authService->sendOtpForPasswordReset($request->email);
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
            Log::error('Error sending OTP for forgot password: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 400);
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

            $this->authService->verifyOtpForPasswordReset($request->email, $request->otp_code);
            DB::commit();

            return response()->json(
                [
                    'message' => 'OTP valid!!!.',
                    'redirect' => route('password_reset.otp', ['email' => $request->email]),
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error verifying OTP for password reset: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 400);
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

            $this->authService->updatePassword($request->email, $request->new_password);
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
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function showOtpVerificationForm()
    {
        $email = request()->query('email');

        return view('auth.verify_otp', compact('email'));
    }

    public function showSendOTPForm()
    {
        return view('auth.send_otp_forgot_password');
    }
}
