<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'stock', 'description'];

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity')->withTimestamps();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'categories_products');
    }

    public function getAllProducts() {
        $data = $this;

        return $data->get();
    }
}
