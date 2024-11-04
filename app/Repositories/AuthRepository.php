<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Otp;

class AuthRepository
{
    protected $user;
    protected $otp;

    public function __construct(User $user, Otp $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    public function createUser(array $data)
    {
        return $this->user->create($data);
    }

    public function createOtp(array $data)
    {
        return $this->otp->create($data);
    }

    public function findUserByEmail(string $email)
    {
        return $this->user->where('email', $email)->first();
    }

    public function getLatestOtpByEmail(string $email)
    {
        return $this->otp->where('email', $email)->orderBy('created_at', 'desc')->first();
    }

    public function updateOtp(Otp $otp, array $data)
    {
        return $otp->update($data);
    }

    public function updatePassword($user, $password)
    {
        $user->password = $password;
        $user->save();
    }
}
