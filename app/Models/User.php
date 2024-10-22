<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\File;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'age',
        'imageUrl'
    ];

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

    public function getAllUsers() {
        return $this->all();
    }

    public function uploadFile($file)
    {
        $publicPath = 'uploads';
        $absolutePath = public_path($publicPath);
        File::makeDirectory($absolutePath, 0755, true, true);
        $file->move($absolutePath, $file->getClientOriginalName());

        return $publicPath . '/' . $file->getClientOriginalName();
    }
}
