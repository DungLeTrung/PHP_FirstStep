<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\File;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['email', 'password', 'first_name', 'last_name', 'age', 'imageUrl', 'role', 'isVerify', 'social_id',
        'social_type'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // Add any fields that you want to hide during serialization, if necessary.
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // You can specify any casting rules for your fields here, if necessary.
    ];

    public function getAllUsers($request)
    {
        $data = $this->where('role', '!=', 'Admin');;

        $ageFilter = $request->query('age_filter');

        if ($ageFilter) {
            $ageRange = explode('-', $ageFilter);
            $minAge = (int) $ageRange[0];
            $maxAge = (int) $ageRange[1];

            $data = $data->whereBetween('age', [$minAge, $maxAge]);
        }
        return $data->get();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function otps()
    {
        return $this->hasMany(Otp::class, 'email', 'email');
    }
}
