<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total_price', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product')->withPivot('quantity');
    }

    public function order_product()
    {
        return $this->belongsToMany(Product::class, 'order_product')->withPivot('quantity');
    }

    public static function filterOrders($filters)
    {
        $query = self::query()
            /* Dùng để tải trước (eager loading) các quan hệ liên quan -> giảm số lượng truy vấn trong CSDL vì đã được tải trước
             và giảm thiểu query n+1 do đã được nạp dữ liệu trước (không phải truy vấn từ từ từng bản ghi từ đầu)*/
            ->with(['user', 'products.categories', 'products.order_product'])

            ->when(isset($filters['min_total_price']) && isset($filters['max_total_price']), function ($query) use ($filters) {
                $query->whereBetween('total_price', [$filters['min_total_price'], $filters['max_total_price']]);
            })
            ->when(isset($filters['min_user_age']) && isset($filters['max_user_age']), function ($query) use ($filters) {
                //Giúp lọc các quan hệ theo điều kiện
                //where chỉ sử dụng được với bảng chỉnh, không áp dụng trực tiếp được với các bản quan hệ với bảng chính.
                $query->whereHas('user', function ($q) use ($filters) {
                    $q->whereBetween('age', [$filters['min_user_age'], $filters['max_user_age']]);
                });
            })
            ->when(isset($filters['min_product_price']) && isset($filters['max_product_price']), function ($query) use ($filters) {
                $query->whereHas('products', function ($q) use ($filters) {
                    $q->whereBetween('price', [$filters['min_product_price'], $filters['max_product_price']]);
                });
            })
            ->when(isset($filters['category_name']), function ($query) use ($filters) {
                $query->whereHas('products.categories', function ($q) use ($filters) {
                    $q->where('name', $filters['category_name']);
                });
            })
            ->when(isset($filters['min_quantity']) && isset($filters['max_quantity']), function ($query) use ($filters) {
                $query->whereHas('products.order_product', function ($q) use ($filters) {
                    $q->whereBetween('quantity', [$filters['min_quantity'], $filters['max_quantity']]);
                });
            })
            ->whereDoesntHave('products', function ($q) use ($filters) {
                $q->where('price', '<', $filters['min_product_price'])->orWhere('price', '>', $filters['max_product_price']);
            });

        return $query->get();
    }
}
