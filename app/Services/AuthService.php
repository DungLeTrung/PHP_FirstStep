<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthService
{
    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function registerUser($data)
    {
        $validatedData = [
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'age' => $data['age'],
            'role' => 'User',
            'isVerify' => false,
        ];

        return $this->authRepository->createUser($validatedData);
    }

    public function sendOtp($email)
    {
        $otpCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $expirationTime = now()->addMinutes(10);
        $hashedOtpCode = Hash::make($otpCode);

        $otp = $this->authRepository->getLatestOtpByEmail($email);

        if ($otp && now()->diffInMinutes($otp->created_at) < 10) {
            return ['message' => 'OTP is still valid', 'status' => 400];
        }

        if ($otp) {
            $this->authRepository->updateOtp($otp, [
                'otp_code' => $hashedOtpCode,
                'expired' => $expirationTime,
                'updated_at' => now(),
            ]);
        } else {
            $this->authRepository->createOtp([
                'otp_code' => $hashedOtpCode,
                'expired' => $expirationTime,
                'email' => $email,
            ]);
        }

        Mail::to($email)->send(new OtpMail($otpCode, $expirationTime));

        return ['message' => 'OTP sent successfully', 'status' => 200];
    }

    public function verifyOtp($email, $otpCode)
    {
        $otp = $this->authRepository->getLatestOtpByEmail($email);

        if (!$otp) {
            return ['message' => 'No OTP found for this email', 'status' => 404];
        }

        if ($otp->expired < now()) {
            return ['message' => 'OTP has expired', 'status' => 400];
        }

        if (Hash::check($otpCode, $otp->otp_code)) {
            $user = $this->authRepository->findUserByEmail($email);
            $user->isVerify = true;
            $user->save();

            return ['message' => 'OTP verified successfully', 'status' => 200];
        }

        return ['message' => 'Invalid OTP', 'status' => 400];
    }

    public function updatePassword($email, $newPassword)
    {
        $user = $this->authRepository->findUserByEmail($email);
        if (!$user) {
            return ['message' => 'User not found', 'status' => 404];
        }

        $hashedPassword = Hash::make($newPassword);
        $this->authRepository->updatePassword($user, $hashedPassword);

        return ['message' => 'Updated Password Successfully!', 'status' => 200];
    }

    public function verifyOtpForPasswordReset($email, $otpCode)
    {
        $otp = $this->authRepository->getLatestOtpByEmail($email);

        if (!$otp) {
            return ['message' => 'No OTP found for this email.', 'status' => 404];
        }

        if ($otp->expired < now()) {
            return ['message' => 'OTP has expired.', 'status' => 404];
        }

        if (!Hash::check($otpCode, $otp->otp_code)) {
            return ['message' => 'Invalid OTP.', 'status' => 404];
        }
        return ['message' => 'Invalid OTP', 'status' => 400];
    }

    public function sendOtpForPasswordReset($email)
    {
        $user = $this->authRepository->findUserByEmail($email);
        if (!$user) {
            return ['message' => 'User not found.', 'status' => 404];
        }

        $otp = $this->authRepository->getLatestOtpByEmail($email);

        if ($otp && now()->diffInMinutes($otp->created_at) < 10) {
            return ['message' => 'Invalid OTP.', 'status' => 404];
        }

        $newOtpCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $hashedOtp = Hash::make($newOtpCode);
        $expirationTime = now()->addMinutes(10);

        if ($otp) {
            $this->authRepository->updateOtp($otp, [
                'otp_code' => $hashedOtp,
                'expired' => $expirationTime,
                'updated_at' => now(),
            ]);
        } else {
            $this->authRepository->createOtp([
                'otp_code' => $hashedOtp,
                'expired' => $expirationTime,
                'email' => $email,
            ]);
        }

        Mail::to($email)->send(new OtpMail($newOtpCode, $expirationTime));

        return ['message' => 'OTP sent successfully', 'status' => 200];
    }
}
