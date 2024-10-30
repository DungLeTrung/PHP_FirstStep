<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendOtpEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use App\Models\Otp;

class AuthController extends Controller
{
    protected $userModel;
    protected $otpModel;

    public function __construct(User $user, Otp $otp)
    {
        $this->userModel = $user;
        $this->otpModel = $otp;
    }

    //LOGIN API
    public function login(Request $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->only('email', 'password');

            if (!Auth::attempt($validatedData)) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            /** @var \App\Models\User $user **/
            $user = Auth::user();

            if (!$user->isVerify) {
                return response()->json(['message' => 'Account not verified.'], 403);
            }

            $token = $user->createToken('Personal Token')->accessToken;

            DB::commit();

            return response()->json([
                'message' => 'Login successful',
                'access_token' => $token,
                'user' => new UserResource($user),
                'token_type' => 'Bearer',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'message' => 'Login failed',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    //REGISTER API
    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'age' => 'nullable|integer|min:0|max:100',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:8',
            ]);

            $validatedData['role'] = 'User';
            $validatedData['isVerify'] = false;
            $validatedData['password'] = bcrypt($validatedData['password']);

            $user = $this->userModel->create($validatedData);
            $user->makeHidden(['password']);

            DB::commit();

            return response()->json(
                [
                    'message' => 'User registered successfully',
                    'user' => new UserResource($user),
                ],
                201,
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(
                [
                    'message' => 'User registration failed',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    //SEND OTP - VERIFY ACCOUNT
    public function sendOtp(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'email' => 'required|email|max:255',
            ]);

            $user = $this->userModel->where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            $otp = $this->otpModel
                ->where('email', $request->email)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($otp && now()->diffInMinutes($otp->created_at) < 10) {
                return response()->json(['message' => 'OTP is still valid.'], 400);
            }

            $newOtpCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $hashedOtp = Hash::make($newOtpCode);
            $newExpirationTime = now()->setTimezone('Asia/Ho_Chi_Minh')->addMinutes(10);

            if ($otp) {
                $otp->otp_code = $hashedOtp;
                $otp->updated_at = now();
                $otp->expired = $newExpirationTime;
                $otp->save();
            } else {
                $this->otpModel->create([
                    'otp_code' => $hashedOtp,
                    'expired' => $newExpirationTime,
                    'email' => $request->email,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]);
            }

            SendOtpEmail::dispatch($request->email, $newOtpCode, $newExpirationTime)->onQueue('email');

            DB::commit();

            return response()->json(
                [
                    'message' => 'OTP sent successfully.',
                ],
                201,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error sending OTP: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to send OTP. Please try again.'], 500);
        }
    }

    //VERIFY ACCOUNT
    public function verifyAccount(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'email' => 'required|email|max:255',
                'otp_code' => 'required|string|size:4',
            ]);

            $email = $request->email;
            $user = $this->userModel->where('email', $email)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            $otp = $this->otpModel
                ->where('email', $request->email)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$otp) {
                return response()->json(['message' => 'No OTP found for this email.'], 404);
            }

            if ($otp->expired < now()) {
                return response()->json(['message' => 'OTP has expired.'], 400);
            }

            if (Hash::check($request->otp_code, $otp->otp_code)) {
                $user->isVerify = true;
                $user->save();

                DB::commit();

                return response()->json(['message' => 'OTP verified successfully.'], 200);
            } else {
                return response()->json(['message' => 'Invalid OTP.'], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error verifying OTP: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to verify OTP. Please try again.'], 500);
        }
    }

    //SEND OTP - FORGOT PASSWORD
    public function forgotPassword(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'email' => 'required|email|max:255',
                'new_password' => 'required|string|min:8',
                'confirm_password' => 'required|string|min:8|same:new_password',
            ]);

            $user = $this->userModel->where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            DB::commit();

            return response()->json(['message' => 'Password updated successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating password: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update password. Please try again.'], 500);
        }
    }

    //LOGOUT
    public function logout()
    {
        DB::beginTransaction();

        try {
            /** @var \App\Models\User $user **/
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            $token = $user->token();

            $token->revoke();

            DB::commit();

            return response()->json(['message' => 'Successfully logged out']);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['message' => 'Logout failed', 'error' => $e->getMessage()], 500);
        }
    }
}
