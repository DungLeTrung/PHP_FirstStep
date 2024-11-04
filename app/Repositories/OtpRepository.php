<?php

namespace App\Repositories;

use App\Models\Otp;
use Illuminate\Support\Facades\Hash;

class OtpRepository
{
    protected $otp;

    public function __construct(Otp $otp)
    {
        $this->otp = $otp;
    }

    public function create(array $data)
    {
        return $this->otp->create($data);
    }

    public function findLatestByEmail($email)
    {
        return $this->otp->where('email', $email)->orderBy('created_at', 'desc')->first();
    }

    public function updateOtp($otp, $hashedOtp, $expirationTime)
    {
        $otp->otp_code = $hashedOtp;
        $otp->expired = $expirationTime;
        $otp->updated_at = now();
        $otp->save();
    }

    public function isExpired($otp)
    {
        return $otp->expired < now();
    }

    public function checkOtp($inputOtp, $storedOtp)
    {
        return Hash::check($inputOtp, $storedOtp);
    }
}
