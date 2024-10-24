<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = ['otp_code', 'expired', 'email'];

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
